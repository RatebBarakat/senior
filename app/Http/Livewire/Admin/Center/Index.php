<?php

namespace App\Http\Livewire\Admin\Center;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lean\LivewireAccess\WithImplicitAccess;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    public function render()
    {
        auth()->guard()->user()->load('center');
        $center = auth()->guard()->user()->center;
        $center->load('employees');

        return view('livewire.admin.center.index',[
            'center' => $center,
            'employees' => $center->employees,
            'availableEmployees' => Admin::centersEmployees()->whereNull('center_id')->get()
        ]);
    }
    use WithPagination,WithImplicitAccess;
    #[BlockFrontendAccess]

    public $paginationTheme = 'bootstrap';
    public ?Admin $employee = null;
    public $selectedAdmins = [];
    public ?int $employee_id = null;
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
        if (!auth()->guard()->user()->isAdminCenter())abort(403);
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

    public function addAdminCenter(){
        if (!Gate::allows('create-employees'))abort(403);
        $this->validate([
           'employee_id' => 'required|integer|exists:admins,id'
        ]);

       $admin = Admin::centersEmployees()->whereNull('center_id')
           ->where('id',$this->employee_id)->first();
       if (!$admin)$this->alert('error','employee not found or is another center');
       $admin->center_id = auth()->guard()->user()->center->id;
       $admin->save();

        $this->alert('success','employee added successfully');

        $this->hideAddModal();
    }

    public function openDeleteModal(int $id){
        $this->employee_id = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function deleteAdmin(){
        if (!Gate::allows('delete-employees'))abort(403);

        $employee = auth()->guard()->user()->center->employees()
            ->where('id', $this->employee_id)->first();

        if ($employee){
            $employee->center_id = null;
            $employee->save();

            $this->resetInputs();
            $this->dispatchBrowserEvent('hide-delete-modal');
            $this->alert('success', "Employee removed from center successfully");
            $this->resetPage();
        }
        else{
            $this->alert('error','employee not found');
        }

    }

    public function openEditModal(int $id){
        $this->employee_id = $id;
        $employee = Admin::nonSuperAdmins()->with('role')->findOrFail($this->employee_id);
        $this->employee = $employee;
        $this->name = $employee->name;
        $this->email = $employee->email;
        $this->role_id = $employee->role->id;
        $this->dispatchBrowserEvent('open-edit-modal');
    }

    public function hideEditModal(){
        $this->dispatchBrowserEvent('close-edit-modal');
    }

    public function updateAdmin(){
        if (!Gate::allows('update-employees'))abort(403);

        $this->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:employees,email,'.$this->employee_id,
            'role_id' => ['required','integer',Rule::exists('roles', 'id')]
        ]);
        $this->employee->update([
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id
        ]);
        $this->hideEditModal();
        $this->alert('success',$this->employee->name.'updated successfully');
        $this->resetInputs();
    }

    public function resetInputs(){
        $this->reset('name','email','password','passwordConfirm'
            ,'employee_id','search','employee','selectedAdmins');
    }
}
