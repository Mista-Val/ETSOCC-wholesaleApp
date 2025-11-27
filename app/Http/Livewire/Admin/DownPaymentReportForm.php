<?php
namespace App\Http\Livewire\Admin;

use App\Models\DownPayment; // Use the correct model
use App\Models\Location; 
use App\Models\User; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB; 

class DownPaymentReportForm extends Component
{
    use WithPagination;

    public $search = '';
    public $paymentMethod = ''; 
    public $date = '';
    
    // Status filter is still declared, but its logic will be removed/commented out.
    // We'll keep the public property here to prevent breaking the livewire binding in the Blade file,
    // and rely on the Blade fix below to remove the filter entirely.
    public $status = ''; 

    protected $updatesQueryString = ['search', 'paymentMethod', 'status','date']; 

    // --- Pagination and Filter Reset Methods ---
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPaymentMethod()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'paymentMethod', 'status','date']);
        $this->resetPage();
    }
    
    // --- Data Query Logic ---

    /**
     * Creates the base Eloquent query for down payments, applying current filters.
     */
    private function getPaymentsQuery() 
    {
        $query = DownPayment::with(['coustomer', 'location'])->where('type', 'down_payment'); 
        
        /** Apply search on customer name, location name, or remarks */
       if ($this->search) {
            $query->where(function($q) {
                $q->where('remarks', 'like', "%{$this->search}%")
                  ->orWhere('type', 'like', "%{$this->search}%")
                  ->orWhere('payment_method', 'like', "%{$this->search}%"); 
                
                // 2. Search by Customer Name
                $q->orWhereHas('coustomer', function($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                })
                // 3. Search by Location Name
                ->orWhereHas('location', function($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                });
            });
        }

        // Filter by 'payment_method' field
        if ($this->paymentMethod) {
            $query->where('payment_method', $this->paymentMethod);
        }

         if ($this->date) {
            $query->whereDate('date', $this->date);
        }


        return $query->orderBy('created_at', 'desc');
    }

    // --- Render Method ---

    public function render()
    {
        $payments = $this->getPaymentsQuery()->paginate(10);
        
        // Ensure the correct view path is used (admin.down-payment-report.list)
        return view('admin.down-payment-report.list',[ 
             'payments' => $payments 
         ])->with('paginationView', 'vendor.pagination.custom');
    }

    // --- Export Method ---

    /**
     * Handles exporting the current filtered data to a CSV file.
     */
    public function exportCsv()
    {
        $payments = $this->getPaymentsQuery()->get(); 
        
        if ($payments->isEmpty()) {
            $this->dispatchBrowserEvent('error-message', ['message' => 'No records to export!']);
            return;
        }

        $filename = 'down_payment_report_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Create the CSV content stream
        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // CSV Header 
            fputcsv($file, [
                'ID', 'Date', 'Amount', 'Payment Method', 'Type', 'Location', 'Customer Name', 'Phone Number','Address','Remarks'
            ]);
            
            // CSV Data Rows
            foreach ($payments as $payment) {
                
                fputcsv($file, [
                    $payment->id,
                    $payment->date ?? $payment->created_at->format('Y-m-d'),
                    $payment->amount, 
                    $payment->payment_method, 
                    ucfirst($payment->type),  
                    $payment->location->name ?? 'N/A', 
                    $payment->coustomer->name ?? 'N/A', 
                    $payment->coustomer->phone_number ?? 'N/A', 
                    $payment->coustomer->address ?? 'N/A', 
                    $payment->remarks
                ]);
            }
            fclose($file);
        };

        // Return the streaming response which triggers the file download
        return Response::stream($callback, 200, $headers);
    }
}