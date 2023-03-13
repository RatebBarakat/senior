<?php

namespace App\Http\Livewire\Admin\Super;

use App\Models\Admin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Admins extends Component
{
    public function render()
    {
        return view('livewire.admin.super.admins',[
            'admins' => Admin::nonSuperAdmins()->get()
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

    public function addAdmin(){
        $this->validate([
            'name' => 'required|string|min:2',
            'location_id' => ['required', 'integer',Rule::exists('locations', 'id')],
            'admin_id' => ['nullable',Rule::exists('admins', 'id')]
        ]);

        DonationAdmin::create([
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

    public function deleteAdmin(){

        if (!Gate::allows('delete-centers'))abort(403);

        $center = DonationAdmin::findOrFail($this->center_id);
        $center->delete();
        $this->resetInputs();
        $this->dispatchBrowserEvent('hide-delete-modal');
        $this->alert('success',"center deleted successfully");
        $this->resetPage();
    }

    public function openEditModal(int $id){
        $this->center_id = $id;
        $this->center = DonationAdmin::with('location')->findOrFail($this->center_id);
        $this->name = $this->center->name;
        $this->location_id = $this->center->location->id;
        $this->dispatchBrowserEvent('open-edit-modal');
    }

    public function hideEditModal(){
        $this->dispatchBrowserEvent('close-edit-modal');
    }

    public function updateAdmin(){
        $this->validate([
            'name' => 'required|string|min:2',
            'location_id' => ['required','integer',Rule::exists('locations', 'id')]
        ]);
        $this->center->update([
            'name' => $this->name,
            'location_id' => $this->location_id
        ]);
        $this->hideEditModal();
        $this->alert('success',$this->center->name.'updated successfully');
        $this->resetInputs();
    }

    public function resetInputs(){
        $this->reset('name','center_id','location_id','search','center','selectedAdmins');
    }
}
