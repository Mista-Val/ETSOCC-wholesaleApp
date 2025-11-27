<?php
namespace App\Http\Livewire\Admin;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class SalesReportForm extends Component
{
    use WithPagination;

    public $search = '';
    public $payment_method = '';
    public $date = '';

    protected $updatesQueryString = ['search', 'payment_method', 'date']; 

    // --- Pagination and Filter Reset Methods ---
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPaymentMethod()
    {
        $this->resetPage();
    }

    public function updatingDate()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'payment_method', 'date']);
        $this->resetPage();
    }
    
    // --- Data Query Logic ---

    /**
     * Creates the base Eloquent query for sales, applying current filters.
     */
    private function getSalesQuery()
    {
        $query = Sale::with(['customer', 'location']); 
        
        /** Apply search on customer/location names */
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('customer', function($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                })
                ->orWhereHas('location', function($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                })
                ->orWhere('payment_method', 'like', "%{$this->search}%")
                ->orWhere('total_amount', 'like', "%{$this->search}%");
            });
        }

        /** Apply payment method filter */
        if ($this->payment_method) {
            $query->where('payment_method', $this->payment_method);
        }

        /** Apply date filter */
        if ($this->date) {
            $query->whereDate('created_at', $this->date);
        }

        return $query->orderBy('created_at', 'desc');
    }

    // --- Render Method ---

    public function render()
    {
        // Paginate the results for the table display
        $sales = $this->getSalesQuery()->paginate(10);
        
        return view('admin.sales-reports.list',[
             'sales' => $sales
         ])->with('paginationView', 'vendor.pagination.custom');
    }

    // --- Export Method ---

    /**
     * Handles exporting the current filtered data to a CSV file with product details.
     */
    public function exportCsv()
    {
        // Get all data matching the current filters with soldProducts
        $sales = $this->getSalesQuery()->with(['soldProducts.product'])->get();
        
        if ($sales->isEmpty()) {
            $this->dispatchBrowserEvent('error-message', ['message' => 'No records to export!']);
            return;
        }

        $filename = 'sales_report_detailed_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Create the CSV content stream
        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($file, [
                'Sale ID', 
                'Sale Date', 
                'Customer', 
                'Location', 
                'Payment Method', 
                'Product Name',
                'Unit Price',
                'Quantity',
                'Product Total',
                // 'Sale Total Amount', 
                'Remark'
            ]);
            
            // CSV Data Rows
            foreach ($sales as $sale) {
                // Check if sale has products
                if ($sale->soldProducts && $sale->soldProducts->count() > 0) {
                    // Loop through each product in the sale
                    foreach ($sale->soldProducts as $soldProduct) {
                        fputcsv($file, [
                            $sale->id,
                            $sale->created_at->format('Y-m-d H:i:s'),
                            $sale->customer->name ?? 'N/A',
                            $sale->location->name ?? 'N/A',
                            ucfirst($sale->payment_method),
                            $soldProduct->product->name ?? 'N/A',
                            number_format($soldProduct->per_unit_amount, 2, '.', ''),
                            $soldProduct->quantity,
                            number_format($soldProduct->total_product_amount, 2, '.', ''),
                            // number_format($sale->total_amount, 2, '.', ''),
                            $sale->remark ?? '-'
                        ]);
                    }
                } else {
                    // If no products, still export the sale info
                    fputcsv($file, [
                        $sale->id,
                        $sale->created_at->format('Y-m-d H:i:s'),
                        $sale->customer->name ?? 'N/A',
                        $sale->location->name ?? 'N/A',
                        ucfirst($sale->payment_method),
                        'No Products',
                        '-',
                        '-',
                        '-',
                        number_format($sale->total_amount, 2, '.', ''),
                        $sale->remark ?? '-'
                    ]);
                }
            }
            fclose($file);
        };

        // Return the streaming response which triggers the file download
        return Response::stream($callback, 200, $headers);
    }
    
    // --- Destroy Method ---

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        $this->dispatchBrowserEvent('success-message', [
            'message' => 'Sales record deleted successfully!'
        ]);
        // Re-render the component to update the list
        $this->render(); 
    }  
}