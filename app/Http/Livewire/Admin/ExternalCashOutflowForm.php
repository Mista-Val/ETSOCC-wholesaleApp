<?php
namespace App\Http\Livewire\Admin;

use App\Models\ExternalCashInflow;
use App\Models\ExternalCashOutflow;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ExternalCashOutflowForm extends Component
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

        $query = ExternalCashOutflow::query(); 
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
        $externalCashOutFlow = $query->paginate(10);

        return view('admin.external-cash-outflow.list',compact('externalCashOutFlow'))->with('paginationView', 'vendor.pagination.custom');
    }

     public function destroy($id)
    {
        // NOTE: You are using the Location model here, which may be incorrect for deleting an ExternalCashInflow
        $inflow = ExternalCashOutflow::findOrFail($id);
        
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
            'message' => 'Cash outflow record deleted successfully!'
        ]);
    }  
}