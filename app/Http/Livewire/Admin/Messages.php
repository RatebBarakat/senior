<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class Messages extends Component
{
    public $filter = "";

    public function render()
    {
        $admin = auth()->guard('admin')->user();
        $query = $admin->messages()->with('sender');
        if (!empty($this->filter)) {
            $query->where('message_type', $this->filter);
        }
        $messages = $query->get();
        return view('livewire.admin.messages', [
            'messages' => $messages,
        ]);        
    }

    public function markAsRead(int $id)
    {
        $message = auth()->guard('admin')->user()->messages()->findOrFail($id); 
        $message->read_at = now();
        $message->save();
        $this->alert('success','message marked as read');
    }

    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }
}
