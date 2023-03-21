<?php

namespace App\Http\Livewire\Admin\Center;

use App\Models\Appointment;
use App\Models\Donation;
use Carbon\Carbon;
use Livewire\Component;
use Lean\LivewireAccess\WithImplicitAccess;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Notifications\SendPdfEmail;
use App\Helpers\AppointmentPdf;
use TCPDF;

class Appointments extends Component
{

    use WithPagination,WithImplicitAccess;
    #[BlockFrontendAccess]
    public ?int $appointment_id = null;
    public ?Appointment $appointment = null;
    public int $quantity = 0;

    public function mount()
    {
        if (!auth()->guard('admin')->user()->isEmployee())abort(403);
    }

    public function render()
    {
        $admin = auth()->guard('admin')->user();
        $admin->load(['employeeCenter','employeeCenter.appointments' => function ($query) {
            $query->scheduled()->with('user')->whereBetween('date',[Carbon::parse(now())->format('y-m-d'),
            Carbon::parse(now()->addWeek())->format('y-m-d')]);
        }]);     
        $appointments = $admin->employeeCenter->appointments;
        
        return view('livewire.admin.center.appointments',compact('appointments'));
    }
    
    public function showCompleteAppointment(int $id)
    {
        $app = Appointment::findOrFail($id);
        $this->appointment = $app;
        $this->appointment_id = $app->id;
        $this->dispatchBrowserEvent('show-complete-modal');
    }

    public function completeAppointment()
    {
        $this->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'quantity' => 'required|integer|min:1|max:3'
        ]);

        $this->appointment->load('center','user');

      try {
        DB::beginTransaction();
        Donation::create([
            'quantity' => $this->quantity,
            'blood_type' => $this->appointment->blood_type,
            'user_id' => $this->appointment->user->id, 
            'appointment_id' => $this->appointment->id, 
            'center_id' => $this->appointment->center->id, 
        ]);

        $pdfName = AppointmentPdf::generatePdf($this->appointment,$this->quantity,'test AppointmentPdf');

        $this->appointment->update([
            'status' => 'complete',
            'pdf_file' => $pdfName
        ]);

        $this->dispatchBrowserEvent('hide-complete-modal');
        $this->alert('success','appointment comleted successfully');
      } catch (\Exception $th) {
        DB::rollback();
        $this->alert('error',$th->getMessage());
      }

      DB::commit();
    
        

    }


    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }
}
