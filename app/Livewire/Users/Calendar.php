<?php

namespace App\Livewire\Users;

use App\Models\Customers\Followup;
use App\Models\Offers\Offer;
use App\Models\Payments\ClientPayment;
use App\Models\Tasks\Task;
use App\Models\Users\CalendarEvent;
use App\Models\Users\User;
use App\Models\Users\CalendarEventUser;
use Carbon\Carbon;
use Livewire\Component;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Facades\Auth;

class Calendar extends Component
{
    use AlertFrontEnd;

    public $page_title =  '• Calendar';

    protected $listeners = ['showEvent'];

    public $newEventSection = false;

    public $title;
    public $start_time;
    public $end_time;
    public $all_day = false;
    public $all_user = false;
    public $location;
    public $note;
    public $users_array = [];

    public $eventID;
    public $deleteEventConfirmation = false;

    public function deleteThisEvent()
    {
        $this->deleteEventConfirmation = true;
    }

    public function ConfirmdDeleteThisEvent()
    {
        //delete Event
        $res = CalendarEvent::find($this->eventID)->deleteEvent();
        if ($res) {
            $this->reset(['eventID', 'deleteEventConfirmation']);
            $this->alert('success', 'Event deleted!');
            redirect(url('calendar'));
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function IgnoreDeleteThisEvent()
    {
        $this->deleteEventConfirmation = false;
    }

    public function showEvent($id)
    {
        $this->eventID = $id;
        $e = CalendarEvent::find($id);
        $this->title = $e->title;
        $this->start_time = $e->start_time;
        $this->end_time = $e->end_time;
        $this->all_day = $e->all_day;
        $this->all_user = $e->all_user;
        $this->location = $e->location;
        $this->note = $e->note;
    }

    public function updateEvent()
    {

        // Validate the input data
        $this->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'all_day' => 'nullable|boolean',
            'all_user' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);
        // Convert start_time and end_time to Carbon instances
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        $res = CalendarEvent::find($this->eventID)->editInfo(
            $this->title,
            $startTime,
            $endTime,
            $this->all_day,
            $this->all_user,
            $this->location,
            $this->note,
        );

        if ($res) {
            $this->reset(['title', 'start_time', 'end_time', 'all_day', 'all_user', 'location', 'note', 'eventID']);
            $this->alert('success', 'Event updated!');
            redirect(url('calendar'));
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function closeUpdateEvent()
    {
        $this->reset(['eventID']);
    }

    public function addEvent()
    {
        // Validate the input data
        $this->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'all_day' => 'nullable|boolean',
            'all_user' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
            'users_array.*.tag' => 'required',
        ]);
        // Convert start_time and end_time to Carbon instances
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        // dd($this->users_array);

        // Call the static newEvent function on CalendarEvent
        $res = CalendarEvent::newEvent(
            $this->title,
            $startTime,
            $endTime,
            $this->all_day,
            $this->all_user,
            $this->location,
            $this->note,
            $this->users_array
        );

        if ($res) {
            $this->reset(['title', 'start_time', 'end_time', 'all_day', 'all_user', 'location', 'note', 'newEventSection']);
            $this->alert('success', 'Event Added!');
            redirect(url('calendar'));
        } else {
            $this->alert('failed', 'server error');
        }
    }

    public function mount()
    {
        // Initialize with one user
        $this->users_array[] = [
            'tag' => CalendarEventUser::TAG_OWNER,
            'user_id' => Auth::id(),
        ];
    }


    public function addUser()
    {
        $this->users_array[] = [
            'tag' => '',
            'user_id' => '',
            'guest_name' => '',
        ];
    }

    public function removeUser($index)
    {
        if (count($this->users_array) > 1) {
            unset($this->users_array[$index]);
            $this->users_array = array_values($this->users_array); // Reindex array
        }
    }

    public function closeNewEventSec()
    {
        $this->newEventSection = false;
    }

    public function openNewEventSec()
    {
        $this->newEventSection = true;
    }
    public function render()
    {
        $events = [];

        foreach (Task::myTasksQuery(upcoming_only: true, assignedToMeOnly: true, includeWatchers: true)->get() as $t) {
            $events[] =  [
                'id'        =>  "task" . $t->id,
                'title'     => "T. " . $t->title,
                'backgroundColor' => 'blue',
                'allDay'    => true,
                'start'     => (new Carbon($t->due))->subMinutes(15)->toIso8601String(),
                'end'       => (new Carbon($t->due))->toIso8601String(),
                'url'       => url('tasks', $t->id)
            ];
        }

        foreach (Followup::userData(upcoming_only: true, mineOnly: true)->with('called')->get() as $t) {
            $events[] =  [
                'id'        =>  "followup" . $t->id,
                'title'     => "F. " . $t->title . ' - ' . $t->called?->name,
                'backgroundColor' => '#c5c6c7',
                'textColor' => 'white',
                'start'     => (new Carbon($t->call_time))->subMinutes(15)->toIso8601String(),
                'end'       => (new Carbon($t->call_time))->toIso8601String(),
                'url'       => url($t->called_type . 's', $t->called_id)
            ];
        }

        foreach (CalendarEvent::userData()->with('event_users')->get() as $t) {

            $events[] =  [
                'id'        => "event" . $t->id,
                'title'     => "$t->title with " . $t->event_users_names,
                'backgroundColor' => '#c5c6c7', //blue
                'allDay'    => true,
                'start'     => (new Carbon($t->start_time))->toIso8601String(),
                'end'       => (new Carbon($t->end_time))->toIso8601String(),
                'url'       => "#"
            ];
        }

        $USER_TAGS = CalendarEventUser::TAGS;
        $USERS = User::all();

        return view('livewire.users.calendar', [
            'events' => $events,
            'USER_TAGS' => $USER_TAGS,
            'USERS' => $USERS
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'calendar' => 'active']);
    }
}
