<?php

namespace App\Http\Livewire\Admin\Super;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithPagination;

class Admins extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public ?Admin $admin = null;
    public $selectedAdmins = [];
    public ?int $admin_id = null;
    public int $locationFilter = 0;
    public ?int $role_id = null;
    public ?string $password = null;
    public ?string $passwordConfirm = null;
    public $perPage = 10;
//    public array $selectedRoles = [];
//    public bool $isSelectedAll = false;
    public string $name = "";
    public string $email = "";
    public string $search = "";

    public function render()
    {
        return view('livewire.admin.super.admins',[
            'admins' => Admin::nonSuperAdmins()->with('role')->paginate(abs($this->perPage)),
            'roles' => Role::get()
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
            'email' => 'required|email|unique:admins',
            'password' => ['required',Password::min(8)->mixedCase()],
            'passwordConfirm' => 'required|same:password',
            'role_id' => ['required', 'integer',Rule::exists('roles', 'id')],
        ],[
            'role_id.required' => 'the role field is required',
            'role_id.integer' => 'selected option wrong value',
            'role_id.exists' => 'the selected role doeasnt exists ',
        ]);

        Admin::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => $this->role_id == null ? null : (int) $this->role_id
        ]);
        $this->alert('success','center addedd successfully');

        $this->hideAddModal();
    }

    public function openDeleteModal(int $id){
        $this->admin_id = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function deleteAdmin(){

        if (!Gate::allows('delete-centers'))abort(403);

        $admin = DonationAdmin::findOrFail($this->admin_id);
        $admin->delete();
        $this->resetInputs();
        $this->dispatchBrowserEvent('hide-delete-modal');
        $this->alert('success',"center deleted successfully");
        $this->resetPage();
    }

    public function openEditModal(int $id){
        $this->admin_id = $id;
        $this->center = DonationAdmin::with('location')->findOrFail($this->admin_id);
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
        $this->reset('name','email','password','passwordConfirm','role_id','search','admin','selectedAdmins');
    }
}
