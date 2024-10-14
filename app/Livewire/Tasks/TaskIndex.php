<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use App\Models\Tasks\Task;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Traits\ToggleSectionLivewire;

class TaskIndex extends Component
{
    use WithPagination, AlertFrontEnd, WithFileUploads, ToggleSectionLivewire;

    public $page_title = '• Tasks';

    public $dateRange;
    public $startDate;
    public $endDate;
    public $filteredStatus = ['active'];
    public $searchText;
    public $myTasks;
    public $watcherTasks;

    public $taskTitle;
    public $assignedTo;
    public $desc;
    public $dueDate;
    public $dueTime;
    public $file;
    public $addWatchersSection;
    public $setWatchersList;

    public $files = [];


    public $showNewTask = false;

    public function toggleAddWatchers()
    {
        $this->toggle($this->addWatchersSection);
    }

    public function openNewTask()
    {
        $this->showNewTask = true;
    }

    public function closeNewTask()
    {
        $this->resetFormFields();
        $this->resetValidation();
    }

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function redirectToShowPage($id)
    {
        return redirect(route('tasks.show', ['id' => $id]));
    }

    public function createTask()
    {

        $this->validate(
            [
                'taskTitle' => 'required|string|max:255',
                'assignedTo' => 'required',
                'desc' => 'nullable|string',
                'dueDate' => 'required|date',
                'dueTime' => 'nullable|date_format:H:i',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,bmp,gif,svg,webp|max:33000',
                'setWatchersList' => 'nullable|array',
                'setWatchersList.*' => 'integer|exists:users,id',
            ],
            [
                'file.max' => 'The file must not be greater than 5MB.',
            ],
            [
                'taskTitle' => 'Title',
                'assignedTo' => 'Assignee',
                'desc' => 'Description',
                'dueDate' => 'Date',
                'dueTime' => 'Time',
                'setWatchersList' => 'Watchers',
            ],
        );

        if ($this->files) {
            $urls  = [];
            foreach ($this->files as $file) {
                array_push($urls, [
                    'name'      => $file->getClientOriginalName(),
                    'file_url'  => $file->store(Task::FILES_DIRECTORY, 's3'),
                    'user_id'   => Auth::id()
                ]);
            }
        } else {
            $urls = [];
        }


        $dueDate = $this->dueDate ? Carbon::parse($this->dueDate) : null;
        $dueTime = $this->dueTime ? Carbon::parse($this->dueTime) : null;
        $combinedDateTime = $dueTime ? $dueDate->setTime($dueTime->hour, $dueTime->minute, $dueTime->second) : $dueDate;

        $t = Task::newTask($this->taskTitle, null, $this->assignedTo, $combinedDateTime, $this->desc, $urls, $this->setWatchersList ?? []);

        if ($t) {
            $this->alert('success', 'Task Added!');
            $this->resetFormFields();
        } else {
            $this->alert('failed', 'Error Adding Task!');
        }
    }

    private function resetFormFields()
    {
        $this->taskTitle = null;
        $this->assignedTo = null;
        $this->desc = null;
        $this->dueDate = null;
        $this->dueTime = null;
        $this->file = null;
        $this->showNewTask = null;
    }

    public function filterByStatus($status)
    {
        $this->filteredStatus = [$status];
    }

    public function mount($filters = [])
    {
        foreach ($filters as $filter) {
            switch ($filter) {
                case 'my':
                    $this->myTasks = Auth::id();
                    break;
            }
        }

        $this->searchText = null;
        $this->startDate = null;
        $this->endDate = null;
        $this->dateRange = ($this->startDate && $this->endDate) ? $this->startDate . ' to ' . $this->endDate : "N/A";
        $this->watcherTasks = false;
    }

    public function updatedDateRange()
    {
        if (strpos($this->dateRange, 'to') !== false) {
            // The string contains 'to'
            [$this->startDate, $this->endDate] = explode(' to ', $this->dateRange);
            // dd($this->startDate, $this->endDate);
        }
    }

    public function updatedSearchText()
    {
        $this->resetPage();
    }

    public function render()
    {
        $statuses = Task::STATUSES;

        $users = User::all();
        $user_types = User::TYPES;

        $tasks = Task::myTasksQuery($this->myTasks, $this->watcherTasks)
            ->when($this->startDate && $this->endDate, function ($query) {
                $startDate = Carbon::parse($this->startDate);
                $endDate = Carbon::parse($this->endDate);
                return $query->fromTo($startDate, $endDate);
            })
            ->when($this->searchText, function ($query) {
                return $query->searchByTitle($this->searchText);
            })
            ->when($this->filteredStatus, function ($query) {
                return $query->byStates($this->filteredStatus);
            })
            ->when($this->filteredStatus == null, function ($query) {
                return $query->byStates(['active']);
            })
            ->paginate(10);

        //fixing assignedTo when a user adds a test without changing the assigned to list
        $this->assignedTo = $this->assignedTo ?? $users->first()?->id;

        return view('livewire.tasks.task-index', [
            'tasks' => $tasks,
            'statuses' => $statuses,
            'filteredStatus' => $this->filteredStatus,
            'users' => $users,
            'user_types' => $user_types,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'tasks' => 'active']);
    }
}
