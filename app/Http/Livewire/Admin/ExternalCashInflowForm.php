<?php
namespace App\Http\Livewire\Admin;

use App\Models\ExternalCashInflow;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ExternalCashInflowForm extends Component
{
    use WithFileUploads;
    use WithPagination;

    /** Page searching & filter parameters */
    public $search = '';
    public $status = '';

    protected $updatesQueryString = ['search', 'status']; 

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status']); /** reset all the applied filters*/
        $this->resetPage();
    }

    public function mount()
    {
        /** This function will run only very first time when pages load */
    }

    public function render()
    {

        $query = ExternalCashInflow::query(); 
        /** Apply search */
       if ($this->search) {
            $query->where('source', 'like', "%{$this->search}%")
                  ->orWhere('received_from', 'like', "%{$this->search}%");
        }

        /** Apply status filters */
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        $query->orderBy('created_at', 'desc');
        $externalCashInflows = $query->paginate(10);

        return view('admin.external-cash-inflow.list',compact('externalCashInflows'))->with('paginationView', 'vendor.pagination.custom');
    }

     public function destroy($id)
    {
        // NOTE: You are using the Location model here, which may be incorrect for deleting an ExternalCashInflow
        $inflow = ExternalCashInflow::findOrFail($id);
        
        // This check and second delete is redundant. Check once, then delete.
        // if ($warehouse) { 
        //     $warehouse->delete(); 
        //     /** emit browser event after delete */
        //     $this->dispatchBrowserEvent('success-message', [
        //         'message' => 'Warehouse deleted successfully!' // Also check this message
        //     ]);
        // }
        
        $inflow->delete();

        $this->dispatchBrowserEvent('success-message', [
            'message' => 'Cash inflow record deleted successfully!'
        ]);
    }  
}