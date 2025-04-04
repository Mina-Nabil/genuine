<?php

namespace App\Livewire\Users;

use App\Models\Users\Driver;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithFileUploads, AlertFrontEnd, WithPagination;
    public $page_title = '• Users';

    public $newUserSection;
    public $search;

    public $newUsername;
    public $newFirstName;
    public $newLastName;
    public $newType;
    public $newPassword;
    public $newPassword_confirmation;
    public $newEmail;
    public $newPhone;
    public $newManagerId;
    public $IdNumber;
    public $IdNumberDoc;
    public $DrivingLicenceNo;
    public $DrivingLicenceDoc;
    public $CarLicenceNo;
    public $CarLicenceDoc;
    public $shiftTitle; //for driver
    public $weightLimit; //for driver
    public $orderQuantityLimit; //for driver
    public $carType; //for driver
    public $carModel; //for driver
    public $startTime = '10:00';
    public $endTime = '18:00';
    public $driverIsAvailable;
    public $home_location_url_1;
    public $home_location_url_2;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleUserStatus($id)
    {
        $res = User::find($id)->toggleActivation();
        if ($res) {
            $this->alertSuccess('User updated successfuly!');
        } else {
            $this->alertFailed('Server error');
        }
    }

    public function clearIDdocFile()
    {
        $this->reset('uploadIDFile', 'OLDuploadIDFile');
    }

    public function clearLicDocFile()
    {
        $this->reset('uploadLicFile', 'OLDuploadLicFile');
    }

    public function clearCarLicDocFile()
    {
        $this->reset('uploadCarLicFile', 'OLDuploadCarLicFile');
    }

    public function downloadLicDocument()
    {
        $fileContents = Storage::disk('s3')->get($this->user->driving_license_doc_url);
        $extension = pathinfo($this->user->driving_license_doc_url, PATHINFO_EXTENSION);
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $this->user->full_name . '_licience_document.' . $extension . '"',
        ];

        return response()->stream(
            function () use ($fileContents) {
                echo $fileContents;
            },
            200,
            $headers,
        );
    }

    public function downloadCarLicDocument()
    {
        $fileContents = Storage::disk('s3')->get($this->user->car_license_doc_url);
        $extension = pathinfo($this->user->car_license_doc_url, PATHINFO_EXTENSION);
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $this->user->full_name . '_car_licience_document.' . $extension . '"',
        ];

        return response()->stream(
            function () use ($fileContents) {
                echo $fileContents;
            },
            200,
            $headers,
        );
    }

    public function closeNewUserSec()
    {
        $this->newUserSection = false;
        $this->reset([
            'newUsername', 'newFirstName', 'newLastName', 'newType', 
            'newPassword', 'newPassword_confirmation', 'newEmail', 
            'newPhone', 'newManagerId', 'IdNumber', 'IdNumberDoc', 
            'DrivingLicenceNo', 'DrivingLicenceDoc', 'CarLicenceNo', 
            'CarLicenceDoc', 'shiftTitle', 'weightLimit', 
            'orderQuantityLimit', 'carType', 'carModel',
            'startTime', 'endTime',
            'home_location_url_1',
            'home_location_url_2'
        ]);
    }

    protected $rules = [
        'newUsername' => 'required|string|max:255|unique:users,username',
    ];

    public function updatedNewUsername()
    {
        $this->validateOnly('newUsername');
    }

    public function addNewUser()
    {
        // Validate the incoming data
        $validatedData = $this->validate([
            'newUsername' => 'required|string|max:255|unique:users,username',
            'newFirstName' => 'required|string|max:255',
            'newLastName' => 'required|string|max:255',
            'newType' => 'nullable|in:' . implode(',', User::TYPES),
            'newPassword' => 'required|string|min:8|confirmed',
            'newEmail' => 'nullable|email|unique:users,email',
            'newPhone' => 'nullable|numeric',
            'newManagerId' => 'nullable|exists:users,id',
            'IdNumber' => 'nullable|string|max:255',
            'IdNumberDoc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'DrivingLicenceNo' => 'nullable|string|max:255',
            'DrivingLicenceDoc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'CarLicenceNo' => 'nullable|string|max:255',
            'CarLicenceDoc' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($this->newType === User::TYPE_DRIVER) {
            $this->validate([
                'shiftTitle' => 'required|string|max:255',
                'weightLimit' => 'required|integer|min:1',
                'orderQuantityLimit' => 'required|integer|min:1',
                'carType' => 'nullable|in:' . implode(',', Driver::CAR_TYPES),
                'carModel' => 'nullable|string|max:255',
                'startTime' => 'required|date_format:H:i',
                'endTime' => 'required|date_format:H:i|after:startTime',
                'home_location_url_1' => 'nullable|string',
                'home_location_url_2' => 'nullable|string',
            ]);
        }

        // Store the documents if they exist
        $idDocUrl = $this->IdNumberDoc ? $this->IdNumberDoc->store(User::FILES_DIRECTORY, 's3') : null;
        $drivingLicenseDocUrl = $this->DrivingLicenceDoc ? $this->DrivingLicenceDoc->store(User::FILES_DIRECTORY, 's3') : null;
        $carLicenseDocUrl = $this->CarLicenceDoc ? $this->CarLicenceDoc->store(User::FILES_DIRECTORY, 's3') : null;

        // Create a new user with the validated data and document URLs
        $res = User::newUser($this->newUsername, $this->newFirstName, $this->newLastName, $this->newType, $this->newPassword, $this->newEmail, $this->newPhone, $this->IdNumber, $idDocUrl, $this->DrivingLicenceNo, $drivingLicenseDocUrl, $this->CarLicenceNo, $carLicenseDocUrl, null, $this->shiftTitle, $this->weightLimit ? $this->weightLimit * 1000 : null , $this->orderQuantityLimit, $this->carType, $this->carModel,$this->startTime,$this->endTime, $this->home_location_url_1, $this->home_location_url_2);

        // Check if the user was created successfully
        if ($res) {
            $this->alertSuccess('User added successfully!'); // Show success alert
            return redirect(route('profile', $res->id));
        } else {
            $this->alertFailed('Server error'); // Show failure alert
        }
    }

    public function UpdatedshiftTitle()
    {
        // if($this->shiftTitle && str_contains($this->shiftTitle, ' AM')){
        //     $this->startTime = ;
        //     $this->endTime = ;
        // } else if($this->shiftTitle && str_contains($this->shiftTitle, ' PM')) {
        //     $this->startTime = ;
        //     $this->endTime = ; 
        // }
    }

    public function openNewUserSec()
    {
        $this->newUserSection = true;
    }

    public function render()
    {
        $TYPES = User::TYPES;
        $carTypes = Driver::CAR_TYPES;
        $users = User::when($this->search, fn($q) => $q->search($this->search))->paginate(30);
        return view('livewire.users.user-index', [
            'users' => $users,
            'TYPES' => $TYPES,
            'carTypes' => $carTypes,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'users' => 'active']);
    }
}
