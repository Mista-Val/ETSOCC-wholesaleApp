<?php

namespace App\Http\Livewire\Admin;

use App\Models\GlobalSetting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\User; 
use App\Models\ReceivedStock;
use App\Models\StockTransferRequest;

class StockList extends Component
{
    use WithFileUploads;
    use WithPagination;

    /** Page searching & filter parameters */
    public $search = '';
   


    protected $updatesQueryString = ['search']; 

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
        $this->reset(['search']); /** reset all the applied filters*/
        $this->resetPage();
    }

    public function mount()
    {
        /** This function will run only very first time when pages load */
    }

    public function render()
    {
       $query = StockTransferRequest::with(['receiverWarehouse', 'items.product'])->latest();

        /** Apply search */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('supplier_name', 'like', "%{$this->search}%");
                //   ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        /** Adding pagination here */
         $query = $query->where('type','admin');
          $query->orderBy('created_at', 'desc');
         $stocks = $query->paginate(10);

        //  dd($stocks->toArray());

        return view('admin.stock.list', ['stocks' => $stocks])->with('paginationView', 'vendor.pagination.custom');
    }

    // public function destroy($id)
    // {
    //     $user = User::find($id);

    //     if ($user) {
    //         $user->delete();
    //         /** emit browser event after delete */
    //         $this->dispatchBrowserEvent('success-message', [
    //             'message' => 'User deleted successfully!'
    //         ]);
    //     }
    // }

    public function destroy($id)
        {
            $stock = StockTransferRequest::findOrFail($id);
            if ($stock->status !== 'created') {
                return redirect()->route('admin.stock.index')->with('error', 'Only stocks with "created" status can be deleted.');
            }
            $stock->items()->delete();
            $stock->delete();
            
            /** emit browser event after delete */
            $this->dispatchBrowserEvent('success-message', [
                'message' => 'Stock deleted successfully!'
            ]);
        }



   
    
}
