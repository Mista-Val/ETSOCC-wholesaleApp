<?php
namespace App\Http\Livewire\Admin;

use App\Models\ExternalCashInflow;
use App\Models\Location;
use App\Models\RecordExpense;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class RecordExpenseForm extends Component
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
         $user = auth()->user(); 
        $query = RecordExpense::with('location');
        // $query = RecordExpense::with('location') ->where('receiver_id', $user->id) ;
        
        /** Apply search */
        if ($this->search) {
            $query->whereHas('location', function($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        /** Apply status filters */
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        
        $query->orderBy('created_at', 'desc');
        $recordExpense = $query->paginate(10);

        return view('admin.record-expense.list', compact('recordExpense'))
            ->with('paginationView', 'vendor.pagination.custom');
    }

    //  public function destroy($id)
    // {
    
    //     $inflow = ExternalCashInflow::findOrFail($id);
    
        
    //     $inflow->delete();

    //     $this->dispatchBrowserEvent('success-message', [
    //         'message' => 'Cash inflow record deleted successfully!'
    //     ]);
    // }  
}