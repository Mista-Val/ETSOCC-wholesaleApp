<?php
namespace App\Http\Livewire\Admin;

use App\Models\Refund;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CashRefundForm extends Component
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
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function mount()
    {
        /** This function will run only very first time when pages load */
    }

    public function render()
    {
        $query = Refund::with(['location', 'customer', 'sale']);
        
        /** Apply search */
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('location', function($locationQuery) {
                    $locationQuery->where('name', 'like', "%{$this->search}%");
                })
                ->orWhereHas('customer', function($customerQuery) {
                    $customerQuery->where('name', 'like', "%{$this->search}%")
                                 ->orWhere('phone_number', 'like', "%{$this->search}%");
                })
                ->orWhere('refund_reason', 'like', "%{$this->search}%")
                ->orWhere('refund_amount', 'like', "%{$this->search}%");
            });
        }

        /** Apply status filters */
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        
        $query->orderBy('created_at', 'desc');
        $cashRefunds = $query->paginate(10);

        return view('admin.cash-refund.list', compact('cashRefunds'))
            ->with('paginationView', 'vendor.pagination.custom');
    }
}