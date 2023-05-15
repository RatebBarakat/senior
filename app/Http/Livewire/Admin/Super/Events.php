<?php

namespace App\Http\Livewire\Admin\Super;

use App\Models\DonationCenter;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Events extends Component
{
    public ?Event $event = null;
    public string $title = "";
    public string $description = "";
    public string $startAt = "";
    public string $endAt = "";
    public int $event_id = 0;
    public array $selectedcenters = [];

    public function mount()
    {
        abort_if(Gate::denies('super-admin'),403);
    }

    private function validateData()
    {
        $rules = [
            'title' => 'required|string|min:3|max:30',
            'description' => 'required|string|min:20|max:200',
            'selectedcenters' => ['required',
                function ($attribute, $value, $fail) {
                    if (count($value) < 1) {
                        $fail('you must select at lest one center');
                    }
                },
            ],
            'startAt' => 'required|date|after:tomorrow',
            'endAt' => 'required|date|after:startAt',
        ];
        $this->validate($rules);
    }

    public function render()
    {
        $events = Event::with('centers')->paginate();
        $centers = DonationCenter::get();
        return view('livewire.admin.super.events',compact('events','centers'));
    }

    public function toggleCenter(int $id)
    {
        $center = DonationCenter::findOrFail($id);
        $isSelected = array_search($id,$this->selectedcenters);
        $message = '';
        if ($isSelected) {
            unset($this->selectedcenters[$isSelected]);
            $message = "{$center->name} removed from added centers";
        }else {
            array_push($this->selectedcenters,$id);
            $message = "{$center->name} added to added centers";
        }
        
        $this->alert('success',$message);
    }

    private function resetInputs()
    {
        $this->reset('title','description','startAt','endAt','selectedcenters','event');
    }

    public function addEvent()
    {
        $this->validateData();

        $event = Event::Create([
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->startAt,
            'end_date' => $this->endAt,
        ]);

        $this->attachCenters($event,$this->selectedcenters);

        $this->alert('success','event created succeefully');
        $this->hideAddModal();
    }
    
    public function updateEvent()
    {
        $this->validateData();

        $this->event->update([
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->startAt,
            'end_date' => $this->endAt,
        ]);

        $this->attachCenters($this->event,$this->selectedcenters);

        $this->alert('success','event updated succeefully');
        $this->hideEditModal();
    }

    public function deleteEvent(){
        if (!Gate::allows('super-admin'))abort(403);

        $this->event = Event::findOrFail($this->event_id);
        $this->event->delete();
        $this->resetInputs();
        $this->dispatchBrowserEvent('hide-delete-modal');
        $this->alert('success',"event deleted successfully");
    }

    private function attachCenters(Event $event,array $centers)
    {
        $event->centers()->sync($centers);
    }

    public function showAddModal()
    {
        $this->resetValidation();
        // $this->resetInputs();
        $this->dispatchBrowserEvent('show-add-modal');
    }

    public function hideAddModal()
    {
        $this->dispatchBrowserEvent('hide-add-modal');
    }

    public function openEditModal(int $id){
        $this->event_id = $id;
        $event = Event::findOrFail($this->event_id);
        $this->event = $event;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->startAt = $event->start_date;
        $this->endAt = $event->end_date;
        $this->selectedcenters = $event->centers->pluck('id')->toArray();
        $this->dispatchBrowserEvent('open-edit-modal');
    }



    public function hideEditModal(){
        $this->dispatchBrowserEvent('close-edit-modal');
    }
    
    public function openDeleteModal(int $id){
        $this->event_id = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }
}
