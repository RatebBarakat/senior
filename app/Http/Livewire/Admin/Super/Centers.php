<?php

namespace App\Http\Livewire\Admin\Super;

use App\Models\Admin;
use App\Models\DonationCenter;
use App\Models\Location;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Lean\LivewireAccess\WithImplicitAccess;
use Livewire\Component;
use Livewire\WithPagination;

class Centers extends Component
{
    use WithPagination,WithImplicitAccess;
    #[BlockFrontendAccess]

    public ?DonationCenter $center = null;
    public $selectedCenters = [];
    public int $center_id = 0;
    public $admin_id = null;
    public int $locationFilter = 0;
    public int $location_id = 0;
    protected $paginationThضeme = 'bootstrap';
    public $perPage = 10;
//    public array $selectedRoles = [];
//    public bool $isSelectedAll = false;
    public string $name = "";
    public string $search = "";

    public function mount(){
        if (!auth()->guard()->user()->isSuperAdmin())abort(403);
    }

    public function render()
    {
        $centers = DonationCenter::with('location','admin')
            ->when(!empty($this->search), function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(abs($this->perPage));

//        $centers->count() > count($this->selectedRoles) ?
//            $this->isSelectedAll = false :
//            $this->isSelectedAll = true;
        return view('livewire.admin.super.centers',[
            'centers' => $centers,
            'admins' => Admin::select('id','name')->get(),
            'locations' => Location::get()
        ]);
    }

    public function showAddModal()
    {
        $this->resetValidation();
        $this->resetInputs();
        $this->dispatchBrowserEvent('show-add-modal');
    }

    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }

    public function hideAddModal()
    {
        $this->dispatchBrowserEvent('hide-add-modal');
    }

    public function addCenter(){
        if (!Gate::allows('create-centers'))abort(403);
        $this->validate([
            'name' => 'required|string|min:2',
            'location_id' => ['required', 'integer',Rule::exists('locations', 'id')],
            'admin_id' => ['nullable',Rule::exists('admins', 'id')]
        ]);

        DonationCenter::create([
            'name' => $this->name,
            'location_id' => $this->location_id,
            'admin_id' => $this->admin_id == null ? null : (int) $this->admin_id
        ]);
        $this->alert('success','center addedd successfully');

        $this->hideAddModal();
    }

    public function openDeleteModal(int $id){
        $this->center_id = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function deleteCenter(){

        if (!Gate::allows('delete-centers'))abort(403);

        $center = DonationCenter::findOrFail($this->center_id);
        $center->delete();
        $this->resetInputs();
        $this->dispatchBrowserEvent('hide-delete-modal');
        $this->alert('success',"center deleted successfully");
        $this->resetPage();
    }

    public function openEditModal(int $id){
        $this->center_id = $id;
        $this->center = DonationCenter::with('location','admin')->findOrFail($this->center_id);
        $this->name = $this->center->name;
        $this->admin_id = $this->center->admin->id ?? null;
        $this->location_id = $this->center->location->id;
        $this->dispatchBrowserEvent('open-edit-modal');
    }

    public function hideEditModal(){
        $this->dispatchBrowserEvent('close-edit-modal');
    }

    public function updateCenter(){
        if (!Gate::allows('update-centers'))abort(403);
        $this->validate([
            'name' => 'required|string|min:2',
            'location_id' => ['required','integer',Rule::exists('locations', 'id')]
        ]);
        $this->center->update([
            'name' => $this->name,
            'location_id' => $this->location_id,
            'admin_id' => $this->admin_id
        ]);
        $this->hideEditModal();
        $this->alert('success',$this->center->name.'updated successfully');
        $this->resetInputs();
    }

    public function resetInputs(){
        $this->reset('name','center_id','location_id','search','center','selectedCenters');
    }
}
