<?php

namespace App\Http\Livewire\Admin\Super;

use App\Models\Admin;
use App\Models\DonationCenter;
use App\Models\Location;
use App\Models\Role;
use App\Notifications\NotifyAdminPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lean\LivewireAccess\WithImplicitAccess;
use Livewire\Component;
use Illuminate\Support\Str;

class CreateCenter extends Component
{
    use WithImplicitAccess;
    #[BlockFrontendAccess]

    public ?string $adminName = "";
    public ?string $email = "";
    // public ?string $password = null;
    // public ?string $passwordConfirm = null;
    public string|int $adminCenter = "";
    public ?string $centerName = null;
    public string|int $location = "";

    public function render()
    {
        return view('livewire.admin.super.create-center',[
            'locations' => Location::get(),
            'centerAdmins' => Admin::whereHas('role', function ($query) {
                $query->where('name', 'center-admin');
            })->doesntHave('center')->get()
        ]);
    }

    public function createCenterAdmin(){
        if (!Gate::allows('create-admins'))abort(403);
        $centerAdminRole = Role::where('name','center-admin')->select('id')->firstOrFail();
        $this->validate([
            'adminName' => 'required|string|min:2',
            'email' => 'required|email|unique:admins',
            // 'password' => ['required',Password::min(8)->mixedCase()],
            // 'passwordConfirm' => 'required|same:password',
        ]);

        $token = Str::random(40);

        $admin = Admin::create([
            'name' => $this->adminName,
            'email' => $this->email,
            'password' => "",
            'role_id' => $centerAdminRole->id,
            'password_token' => $token
        ]);

        try {
            $admin->notify(new NotifyAdminPassword($admin,$token));
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }

        $this->adminCenter = $admin->id;

        $this->alert('success'," admin of center added successfully");

    }

    public function addCenter(){
        try {
            $this->validate([
                'centerName' => 'required|string|min:2',
                'location' => ['required', 'integer',Rule::exists('locations', 'id')],
                'adminCenter' => ['required',Rule::exists('admins', 'id')]
            ]);

            DonationCenter::create([
                'name' => $this->centerName,
                'location_id' => $this->location,
                'admin_id' => $this->adminCenter
            ]);

            $this->alert('success','center added successfully');
            return redirect()->route('admin.centers.index');
        }catch (\Exception $exception){
            $this->alert('error',$exception->getMessage());
        }
    }

    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }

}
