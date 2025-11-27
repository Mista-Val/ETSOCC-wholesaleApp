<?php

namespace App\Http\Livewire\Admin;

use App\Models\GlobalSetting;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\User; 
use App\Models\Outlet; 



class OutletList extends Component
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
          $query = Location::query();

        /** Apply search */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('address', 'like', "%{$this->search}%");
            });
        }

        /** Apply status filters */
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        /** Adding pagination here */
        $query = $query->where('type', 'outlet')->with('user');
        $query->orderBy('created_at', 'desc');
         $outlets = $query->paginate(10);

        //  dd($outlets)->toArray();

        return view('admin.outlets.list', ['outlets' => $outlets])->with('paginationView', 'vendor.pagination.custom');
    }

    //  public function destroy($id)
    // {
    //     $warehouse = Warehouse::findOrFail($id);
    //     $warehouse->delete();
    //      if ($warehouse) {
    //         $warehouse->delete();
    //         /** emit browser event after delete */
    //         $this->dispatchBrowserEvent('success-message', [
    //             'message' => 'Warehouse deleted successfully!'
    //         ]);
    //     }

    // }
    public function destroy($id)
    {
        $outlet = Location::findOrFail($id);
        $outlet->delete();
        if ($outlet) {
            $outlet->delete();
            /** emit browser event after delete */
            $this->dispatchBrowserEvent('success-message', [
                'message' => 'Outlet deleted successfully!'
            ]);
        }

    }




   
    
}
