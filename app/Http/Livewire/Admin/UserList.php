<?php

namespace App\Http\Livewire\Admin;

use App\Models\GlobalSetting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\User; 



class UserList extends Component
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

    public function render()
    {
        $query = User::query()->where('role', '!=', 'admin'); /** Exclude admin */

        /** Apply search */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        /** Apply status filters */
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        /** Apply role filters */
        if ($this->role !== '') {
            $query->where('role', $this->role);
        }

        $query->orderBy('created_at', 'desc');

        /** Adding pagination here */
        $users = $query->paginate(10);

        return view('admin.users.list', ['users' => $users])->with('paginationView', 'vendor.pagination.custom');
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            /** emit browser event after delete */
            $this->dispatchBrowserEvent('success-message', [
                'message' => 'User deleted successfully!'
            ]);
        }
    }



   
    
}
