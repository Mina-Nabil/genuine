<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\Users\Notification;
use App\Traits\AlertFrontEnd;

class NotificationIndex extends Component
{
    use AlertFrontEnd;

    public function setAsNotSeen($id)
    {
        $n = Notification::find($id)->setAsNotSeen();
        if ($n) {
            $this->alert('success', 'Notification Set Unseen');
        } else {
            $this->alert('failed', 'Server Error');
        }
    }

    public function deleteNotf($id)
    {
        $n = Notification::find($id)->delete();
        if ($n) {
            $this->alert('success', 'Notification Set Unseen');
        } else {
            $this->alert('failed', 'Server Error');
        }
    }

    public function render()
    {
        return view('livewire.users.notification-index');
    }
}
