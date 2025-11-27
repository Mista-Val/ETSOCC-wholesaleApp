<?php

namespace App\Http\Livewire\Admin;

use App\Models\GlobalSetting;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\User; 
use App\Models\Outlet; 
use App\Models\Product;


class ProductList extends Component
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
          $query = Product::query();

        /** Apply search */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
                //   ->orWhere('address', 'like', "%{$this->search}%");
            });
        }

        /** Apply status filters */
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        $query->orderBy('created_at', 'desc');
        /** Adding pagination here */
         $products = $query->paginate(10);

        return view('admin.products.list', ['products' => $products])->with('paginationView', 'vendor.pagination.custom');
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
    // public function destroy($id)
    // {
    //     $outlet = Location::findOrFail($id);
    //     $outlet->delete();
    //     if ($outlet) {
    //         $outlet->delete();
    //         /** emit browser event after delete */
    //         $this->dispatchBrowserEvent('success-message', [
    //             'message' => 'Outlet deleted successfully!'
    //         ]);
    //     }

    // }

     public function destroy($id)
    {
        $outlet = Product::findOrFail($id);
        $outlet->delete();
        if ($outlet) {
            $outlet->delete();
            /** emit browser event after delete */
            // $this->dispatchBrowserEvent('success-message', [
            //     'message' => 'Outlet deleted successfully!'
            // ]);
             return redirect()->route('admin.products.index');
        }

    }




   
    
}
