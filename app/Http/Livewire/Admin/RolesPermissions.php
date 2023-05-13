<?php

namespace App\Http\Livewire\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Lean\LivewireAccess\WithImplicitAccess;
use Livewire\Component;
use Livewire\WithPagination;

class RolesPermissions extends Component
{
    use WithPagination,WithImplicitAccess;
    #[BlockFrontendAccess]

    public ?Role $role = null;
    public $selectedPermissions = [];
    public int $role_id = 0;
    protected $paginationTheme = 'bootstrap';
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
        $roles = Role::where('name','<>','super-admin')->when(!empty($this->search),function ($q){
            $q->where('name','like','%'.$this->search.'%');
        })->paginate(abs($this->perPage));
//        $roles->count() > count($this->selectedRoles) ?
//            $this->isSelectedAll = false :
//            $this->isSelectedAll = true;
        return view('livewire.admin.roles-permissions',[
            'roles' => $roles,
            'permissions' => Permission::all()
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

    public function addRole(){
        if (!Gate::allows('create-roles'))abort(403);
        $this->validate([
            'name' => 'required|string|min:2',
            'selectedPermissions' => 'required|min:1',
        ]);

        try {
            $role = Role::create([
                'name' => $this->name,
                'editable' => true
            ]);

            foreach ($this->selectedPermissions as $permission){
                $perm = Permission::where('id',$permission)->first();
                $role->permissions()->attach($perm);

            }
            $this->alert('success', 'role added successfully');
            $this->resetInputs();
        }catch (\Exception $e){
            $this->alert('error',$e->getMessage());
        }


        $this->hideAddModal();
    }

    public function openDeleteModal(int $id){
        $this->role_id = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }
    public function deleteRole(){
        if (!Gate::allows('delete-roles'))abort(403);
        $role = Role::findOrFail($this->role_id);
        if ($role->editable == 1){
            $role->delete();
            $this->resetInputs();
            $this->dispatchBrowserEvent('hide-delete-modal');
            $this->alert('success',"role deleted successfully");
            $this->resetPage();
        }else{
            $this->dispatchBrowserEvent('hide-delete-modal');
            $this->alert('error',"this role cannot be deleted  ");
        }


    }

    public function openEditModal(int $id){
        $this->role_id = $id;
        $this->role = Role::findOrFail($this->role_id);
        $this->name = $this->role->name;
        $this->selectedPermissions = $this->role->permissions->pluck('id')->toArray();
        $this->dispatchBrowserEvent('open-edit-modal');
    }

    public function hideEditModal(){
        $this->dispatchBrowserEvent('close-edit-modal');
    }

    public function updateRole(){
        if (!Gate::allows('update-roles'))abort(403);
        $this->validate([
            'name' => 'required|string|min:2',
            'selectedPermissions' => 'required|min:1',
        ]);
        $permissionsExist = DB::table('permissions')
                ->whereIn('id', $this->selectedPermissions)
                ->count() == count($this->selectedPermissions);

        if ($permissionsExist) {
            // All permissions exist in the table, proceed with updating the relationship
            if ($this->role->editable){
                $this->role->update(['name' => $this->name]);
            }
            $this->role->permissions()->sync($this->selectedPermissions);
            if (Cache::has('permissions'))
                Cache::delete('permissions');
            $this->alert('success','permissions updated successfully');
            $this->hideEditModal();
        } else {
            $this->alert('error','an error occurs');
        }
    }

    public function resetInputs(){
        $this->reset('name','role_id','search','role','selectedPermissions');
    }
}
