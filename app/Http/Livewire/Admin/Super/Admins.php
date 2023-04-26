<?php

namespace App\Http\Livewire\Admin\Super;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lean\LivewireAccess\WithImplicitAccess;
use Livewire\Component;
use Livewire\WithPagination;

class Admins extends Component
{
    use WithPagination,WithImplicitAccess;
    #[BlockFrontendAccess]

    public $paginationTheme = 'bootstrap';
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
    public int $typeFilter = 0;

    public function mount(){
        if (!auth()->guard()->user()->isSuperAdmin())abort(403);
    }

    public function render()
    {
        $admins = Admin::when(!empty($this->search),function ($q){
                    $q->where('name','like','%'.$this->search.'%')
                    ->orWhere('email','like','%'.$this->search.'%');
                })->nonSuperAdmins()
            ->whereHas('role', function ($q){
                $q->when(!$this->typeFilter == 0,function () use ($q){
                    $q->where('id',$this->typeFilter);
                });
            })
            ->with('role')
            ->paginate(abs($this->perPage));

        return view('livewire.admin.super.admins',[
            'admins' => $admins,
            'roles' => Role::where('name','<>','super-admin')->get()
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
        if (!Gate::allows('create-admins'))abort(403);

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
            'role_id' => $this->role_id ?? null
        ]);
        $this->alert('success','admin addedd successfully');

        $this->hideAddModal();
    }

    public function openDeleteModal(int $id){
        $this->admin_id = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function deleteAdmin(){
        if (!Gate::allows('delete-admins'))abort(403);

        $admin = Admin::nonSuperAdmins()->findOrFail($this->admin_id);
        $admin->delete();
        $this->resetInputs();
        $this->dispatchBrowserEvent('hide-delete-modal');
        $this->alert('success',"admin deleted successfully");
        $this->resetPage();
    }

    public function openEditModal(int $id){
        $this->admin_id = $id;
        $admin = Admin::nonSuperAdmins()->with('role')->findOrFail($this->admin_id);
        $this->admin = $admin;
        $this->name = $admin->name;
        $this->email = $admin->email;
        $this->role_id = $admin->role->id;
        $this->dispatchBrowserEvent('open-edit-modal');
    }

    public function hideEditModal(){
        $this->dispatchBrowserEvent('close-edit-modal');
    }

    public function updateAdmin(){
        if (!Gate::allows('update-admins'))abort(403);

        $this->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:admins,email,'.$this->admin_id,
            'role_id' => ['required','integer',Rule::exists('roles', 'id')]
        ]);
        $this->admin->update([
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id
        ]);
        $this->hideEditModal();
        $this->alert('success',$this->admin->name.'updated successfully');
        $this->resetInputs();
    }

    public function resetInputs(){
        $this->reset('name','email','password','passwordConfirm','role_id','search','admin','selectedAdmins');
    }

}
