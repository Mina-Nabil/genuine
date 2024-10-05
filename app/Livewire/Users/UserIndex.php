<?php

namespace App\Livewire\Users;

use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserIndex extends Component
{
    use WithFileUploads, AlertFrontEnd;
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

    public $updateUserSec;
    public $username;
    public $userImage;
    public $first_name;
    public $last_name;
    public $type;
    public $email;
    public $phone;
    public $password;
    public $user;

    public function clearImage()
    {
        $this->userImage = null;
    }

    function generateUrl()
    {
        $url = null;
        $user = User::find($this->updateUserSec);
        if (is_null($user->image) && is_null($this->userImage)) {
            $url = null;
        } elseif (!is_null($user->image) && is_null($this->userImage)) {
            $url = null;
        } elseif (!is_null($user->image) && !is_null($this->userImage)) {
            if (is_string($this->userImage)) {
                $this->userImage = null;
                $url = $user->image;
            }
        } elseif (is_null($user->image) && !is_null($this->userImage)) {
            $this->validate([
                'userImage' => 'image|mimes:jpeg,jpg,png|max:1024', // Adjust max size as needed
            ]);
            $url = $this->userImage->store(User::FILES_DIRECTORY, 's3');
        }

        return $url;
    }

    public function updateThisUser($id)
    {
        $this->updateUserSec = $id;
        $user = User::find($id);
        $this->user = $user;
        $this->username = $user->username;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->type = $user->type;
        $this->email = $user->email;
        $this->phone = $user->phone;
        if ($user->image) {
            $this->userImage = Storage::disk('s3')->url(str_replace('//', '/', $user->image));
        }
    }

    public function toggleUserStatus($id)
    {
        $res = User::find($id)->toggle();
        if ($res) {
            $this->alert('success', 'User updated successfuly!');
        } else {
            $this->alert('failed', 'Server error');
        }
    }

    public function closeUpdateThisUser()
    {
        $this->reset(['updateUserSec', 'username', 'first_name', 'last_name', 'type', 'email', 'phone', 'userImage']);
    }

    public function EditUser()
{
    $currentUserId = $this->updateUserSec;
    $this->validate([
        'username' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) use ($currentUserId) {
                $exists = User::where('username', $value)->where('id', '!=', $currentUserId)->exists();
                if ($exists) {
                    $fail('The ' . $attribute . ' has already been taken.');
                }
            },
        ],
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'type' => 'nullable|in:' . implode(',', User::TYPES),
        'email' => [
            'nullable',
            'email',
            function ($attribute, $value, $fail) use ($currentUserId) {
                $exists = User::where('email', $value)->where('id', '!=', $currentUserId)->exists();
                if ($exists) {
                    $fail('The ' . $attribute . ' has already been taken.');
                }
            },
        ],
        'phone' => 'nullable|numeric',
        'IdNumber' => 'nullable|string|max:255', // Validate ID Number
        'DrivingLicenceNo' => 'nullable|string|max:255', // Validate Driving License Number
    ]);

    // Call the editInfo method, omitting id_doc_url, driving_license_doc_url, and image_url
    $res = User::find($currentUserId)->editInfo(
        $this->username,
        $this->first_name,
        $this->last_name,
        $this->type,
        $this->email,
        $this->phone,
        $this->IdNumber, // Pass ID Number
        null, // id_doc_url
        $this->DrivingLicenceNo, // Pass Driving License Number
        null, // driving_license_doc_url
        null, // image_url
        $this->password // Include password if provided
    );

    if ($res) {
        $this->closeUpdateThisUser();
        $this->alertSuccess( 'User updated successfully!');
    } else {
        $this->alertFailed( 'Server error');
    }
}


    public function closeNewUserSec()
    {
        $this->newUserSection = false;
        $this->reset([
            'newUsername',
            'newFirstName',
            'newLastName',
            'newType',
            'newPassword',
            'newPassword_confirmation',
            'newEmail',
            'newPhone',
            'newManagerId',
            'IdNumber',
            'IdNumberDoc', 
            'DrivingLicenceNo', 
            'DrivingLicenceDoc', 
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
        ]);

        // Store the documents if they exist
        $idDocUrl = $this->IdNumberDoc ? $this->IdNumberDoc->store(User::FILES_DIRECTORY, 's3') : null;
        $drivingLicenseDocUrl = $this->DrivingLicenceDoc ? $this->DrivingLicenceDoc->store(User::FILES_DIRECTORY, 's3') : null;

        // Create a new user with the validated data and document URLs
        $res = User::newUser($this->newUsername, $this->newFirstName, $this->newLastName, $this->newType, $this->newPassword, $this->newEmail, $this->newPhone, $this->IdNumber, $idDocUrl, $this->DrivingLicenceNo, $drivingLicenseDocUrl);

        // Check if the user was created successfully
        if ($res) {
            $this->closeNewUserSec(); // Close the user creation section
            $this->alertSuccess('User added successfully!'); // Show success alert
        } else {
            $this->alertFailed( 'Server error'); // Show failure alert
        }
    }

    public function openNewUserSec()
    {
        $this->newUserSection = true;
    }

    public function render()
    {
        $TYPES = User::TYPES;
        $users = User::when($this->search, fn($q) => $q->search($this->search))->paginate(50);
        return view('livewire.users.user-index', [
            'users' => $users,
            'TYPES' => $TYPES,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'users' => 'active']);
    }
}
