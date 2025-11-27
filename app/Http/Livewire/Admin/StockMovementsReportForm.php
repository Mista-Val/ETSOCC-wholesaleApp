<?php
namespace App\Http\Livewire\Admin;

use App\Models\StockTransferRequest; 
use App\Models\Location; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB; 

class StockMovementsReportForm extends Component
{
    use WithPagination;

    public $search = '';
    public $type = ''; 
    public $status = ''; 
    public $date = '';

    protected $updatesQueryString = ['search', 'type', 'status','date']; 

    // --- Pagination and Filter Reset Methods ---
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'type', 'status','date']);
        $this->resetPage();
    }
    
    // --- Data Query Logic ---

    /**
     * Creates the base Eloquent query for stock transfers, applying current filters.
     */
    private function getTransfersQuery()
    {
        // Use 'warehouse' for supplier (sender) and 'outlet' for receiver
        $query = StockTransferRequest::with(['warehouse', 'outlet']); 
        
        // **FIX: Exclude all records where transfer_type is 'return'**
        $query->where('transfer_type', '!=', 'return');
        
        /** Apply search on sender/receiver names or remark/type/status/transfer_type */
       if ($this->search) {
            $query->where(function($q) {
                
                // 1. Direct field search (for records with NULL location IDs)
                $q->where('remark', 'like', "%{$this->search}%")
                  ->orWhere('type', 'like', "%{$this->search}%")
                  ->orWhere('status', 'like', "%{$this->search}%")
                  // Removed the 'transfer_type' search here to avoid matching the excluded value
                  ->orWhere('transfer_type', 'like', "%{$this->search}%");
                
                // 2. OR Location Name search 
                $q->orWhereHas('warehouse', function($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                })
                ->orWhereHas('outlet', function($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                });
            });
            
            // Note: Since we are excluding 'return' globally, if the user searches for 'return', 
            // the query will return an empty set. If this is undesirable, the search line for 'transfer_type' 
            // can be commented out, but I've kept it for completeness unless you specifically don't want to search the column at all.
        }

        /** Apply transfer type filter (admin, warehouse, outlet) */
        if ($this->type) {
            $query->where('type', $this->type);
        }
        
        /** Apply status filter (pending, transferred, received, rejected) */
        if ($this->status) {
            $query->where('status', $this->status);
        }

         if ($this->date) {
            $query->whereDate('created_at', $this->date);
        }

        return $query->orderBy('created_at', 'desc');
    }

    // --- Render Method ---

    public function render()
    {
        // Paginate the results for the table display
        $transfers = $this->getTransfersQuery()->paginate(10);
        
        return view('admin.stock-movements.list',[ 
             'transfers' => $transfers 
         ])->with('paginationView', 'vendor.pagination.custom');
    }

    // --- Export Method ---

    /**
     * Handles exporting the current filtered data to a CSV file.
     */
 /**
 * Handles exporting the current filtered data to a CSV file with product details.
 */
public function exportCsv()
{
    // Get all data matching the current filters with product items
    $transfers = $this->getTransfersQuery()->with('items.product')->get(); 
    
    if ($transfers->isEmpty()) {
        $this->dispatchBrowserEvent('error-message', ['message' => 'No records to export!']);
        return;
    }

    $filename = 'stock_movements_report_' . date('Ymd_His') . '.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    // Create the CSV content stream
    $callback = function() use ($transfers) {
        $file = fopen('php://output', 'w');
        
        // CSV Header Row - Updated for Stock Movements with Products
        fputcsv($file, [
            'Transfer ID', 
            'Date', 
            'Sender Type', 
            'Sender Location', 
            'Receiver Location', 
            'Transfer Type', 
            'Status', 
            'Remark',
            'Product Name',
            'Set Quantity',
            'Received Quantity',
            'Product Type',
            'Product Remarks'
        ]);
        
        // CSV Data Rows
        foreach ($transfers as $transfer) {
            // Determine Sender Name
            $senderName = $transfer->type === 'admin' 
                ? 'Admin' 
                : ($transfer->warehouse->name ?? 'N/A');

            // Base transfer information
            $baseData = [
                $transfer->id,
                $transfer->created_at->format('Y-m-d'),
                ucfirst($transfer->type), 
                $senderName,
                $transfer->outlet->name ?? 'N/A',
                ucwords(str_replace('_', ' ', $transfer->transfer_type)), 
                ucfirst($transfer->status),
                $transfer->remark ?? '-'
            ];

            // If transfer has items, create a row for each product
            if ($transfer->items && $transfer->items->isNotEmpty()) {
                foreach ($transfer->items as $item) {
                    fputcsv($file, array_merge($baseData, [
                        $item->product->name ?? 'N/A',
                        $item->set_quantity,
                        $item->received_quantity ?? '-',
                        ucfirst($item->type ?? '-'),
                        $item->remarks ?? '-'
                    ]));
                }
            } else {
                // If no items, still export the transfer row with empty product fields
                fputcsv($file, array_merge($baseData, [
                    'No Products',
                    '-',
                    '-',
                    '-',
                    '-'
                ]));
            }
        }
        fclose($file);
    };

    // Return the streaming response which triggers the file download
    return Response::stream($callback, 200, $headers);
}
}