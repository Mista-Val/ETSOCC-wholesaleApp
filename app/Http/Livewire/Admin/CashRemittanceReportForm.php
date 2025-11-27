<?php
namespace App\Http\Livewire\Admin;

use App\Models\CashRemittance; // Use the correct model
use App\Models\Location; 
use App\Models\User; // Assuming User model is used for 'coustomer'
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB; 

class CashRemittanceReportForm extends Component
{
    use WithPagination;

    public $search = '';
    public $role = ''; // Changed 'type' to 'role' based on CashRemittance model
    public $status = ''; 
    public $date = '';

    // Updated to use 'role' instead of 'type'
    protected $updatesQueryString = ['search', 'role', 'status','date']; 

    // --- Pagination and Filter Reset Methods ---
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Changed updatingType to updatingRole
    public function updatingRole()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'role', 'status','date']);
        $this->resetPage();
    }
    
    // --- Data Query Logic ---

    /**
     * Creates the base Eloquent query for cash remittances, applying current filters.
     */
    private function getRemittancesQuery()
    {
        $query = CashRemittance::with(['coustomer', 'location']); 
    
       if ($this->search) {
            $query->where(function($q) {
                
                // 1. Direct field search (remark, status, role)
                $q->where('remarks', 'like', "%{$this->search}%")
                  ->orWhere('status', 'like', "%{$this->search}%")
                  ->orWhere('role', 'like', "%{$this->search}%");
                
                // 2. Search by Receiver (coustomer) Name
                $q->orWhereHas('coustomer', function($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                    // Assuming the User model has a 'name' column
                });
                
                // 3. Search by Sender Location Name
                $q->orWhereHas('location', function($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                });
            });
        }

        /** Apply role filter (admin, warehouse, outlet) */
        if ($this->role) {
            $query->where('role', $this->role);
        }
        
        /** Apply status filter (pending, created, dispatched, partially accepted) */
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
        // 🚨 CRITICAL CHANGE: Use the new query method name
        $remittances = $this->getRemittancesQuery()->paginate(10);
        
        return view('admin.cash-remittance-report.list',[ 
             'remittances' => $remittances // Changed variable name to 'remittances'
         ])->with('paginationView', 'vendor.pagination.custom');
    }

    // --- Export Method ---

    /**
     * Handles exporting the current filtered data to a CSV file.
     */
    public function exportCsv()
    {
        // 🚨 CRITICAL CHANGE: Use the new query method name and variable name
        $remittances = $this->getRemittancesQuery()->get(); 
        
        if ($remittances->isEmpty()) {
            $this->dispatchBrowserEvent('error-message', ['message' => 'No records to export!']);
            return;
        }

        $filename = 'cash_remittance_report_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Create the CSV content stream
        $callback = function() use ($remittances) {
            $file = fopen('php://output', 'w');
            
            //CRITICAL CHANGE: Updated CSV Header for Cash Remittance
            fputcsv($file, [
                'ID', 'Date', 'Amount', 'Receiver Role', 'Sender Location', 'Receiver Name', 'Status', 'Remarks'
            ]);
            
            // CSV Data Rows
            foreach ($remittances as $remittance) {
                
                fputcsv($file, [
                    $remittance->id,
                    $remittance->created_at->format('Y-m-d'),
                    $remittance->amount, // Added Amount
                    ucfirst($remittance->role), // Sender Role
                    $remittance->location->name ?? 'N/A', // Sender Location (location_id)
                    $remittance->coustomer->name ?? 'N/A', // Receiver Name (receiver_id)
                    ucfirst($remittance->status),
                    $remittance->remarks
                ]);
            }
            fclose($file);
        };

        // Return the streaming response which triggers the file download
        return Response::stream($callback, 200, $headers);
    }
}