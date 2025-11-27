<?php

namespace App\Http\Livewire\Admin;

use App\Models\Customer;
use App\Models\GlobalSetting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\User; 
use App\Models\Sale; // Make sure to use Sale model
use Illuminate\Support\Facades\DB; // Needed for subquery/joins if kept

class CustomerList extends Component
{
    use WithFileUploads;
    use WithPagination;

    /** Page searching & filter parameters */
    public $search = '';
    public $status = '';
    public $role = '';


    protected $updatesQueryString = ['search', 'status','role']; 

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

     public function updatingRole()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status','role']); /** reset all the applied filters*/
        $this->resetPage();
    }

    public function mount()
    {
        /** This function will run only very first time when pages load */
    }
    
    // Helper function to get sales count by location type (e.g., 'warehouse', 'outlet')
    protected function getSalesCountByLocationType()
    {
        // Get the counts of sales grouped by the location's 'type'
        return Sale::selectRaw('customer_id, locations.type as location_type, COUNT(*) as count')
            ->join('locations', 'sales.location_id', '=', 'locations.id')
            ->groupBy('customer_id', 'locations.type');
    }

    public function render()
    {
        $query = Customer::query();

        /** Apply search */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('phone_number', 'like', "%{$this->search}%");
            });
        }
        
        // --- Sales Data Aggregation ---
        // 1. Eager load the total sales count
        $query->withCount('sales'); 

        // 2. Fetch total sales amount
        $query->withSum('sales as total_sales_amount', 'total_amount');

        // Note: The logic for showing separate warehouse/outlet sales counts 
        // in the list view is complex and inefficient. It's usually better to show it
        // on a detailed sales page. We will keep the subquery clean here.
        
        $query->orderBy('created_at', 'desc');
        
        $customers = $query->paginate(10);
        

        return view('admin.customers.list', ['customers' => $customers])->with('paginationView', 'vendor.pagination.custom');
    }

    public function destroy($id)
    {
        $user = Customer::find($id);

        if ($user) {
            $user->delete();
            /** emit browser event after delete */
            $this->dispatchBrowserEvent('success-message', [
                'message' => 'User deleted successfully!'
            ]);
        }
    }
}