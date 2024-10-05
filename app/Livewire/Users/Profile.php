<?php

namespace App\Livewire\Users;

use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Facades\Storage;

class Profile extends Component
{
    // use AlertFrontEnd;
    use WithFileUploads, AlertFrontEnd;
    public $page_title = '• Profile';

    public $username;
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $idNumber;
    public $driveLicienceNo;

    public $OLDuserImage;
    public $OLDuploadIDFile;
    public $OLDuploadLicFile;
    public $userImage;
    public $uploadIDFile;
    public $uploadLicFile;

    public $changes;

    public $oldPass, $password, $newPasswordConfirm;
    public $passwordsMatch = true; // Used to track if the passwords match

    public function updatedPassword()
    {
        $this->checkPasswordsMatch();
    }

    public function updatedNewPasswordConfirm()
    {
        $this->checkPasswordsMatch();
    }

    public function checkPasswordsMatch()
    {
        $this->passwordsMatch = $this->password === $this->newPasswordConfirm;
    }

    public $is_open_update_pass;

    public function clearIDdocFile()
    {
        $this->reset('uploadIDFile');
    }

    public function clearLicDocFile()
    {
        $this->reset('uploadLicFile');
    }

    public function downloadIDDocument()
    {
        $fileContents = Storage::disk('s3')->get(Auth::user()->id_doc_url);
        $extension = pathinfo(Auth::user()->id_doc_url, PATHINFO_EXTENSION);
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . Auth::user()->full_name . '_id_document.' . $extension . '"',
        ];

        return response()->stream(
            function () use ($fileContents) {
                echo $fileContents;
            },
            200,
            $headers,
        );
    }

    public function downloadLicDocument()
    {
        $fileContents = Storage::disk('s3')->get(Auth::user()->driving_license_doc_url);
        $extension = pathinfo(Auth::user()->driving_license_doc_url, PATHINFO_EXTENSION);
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . Auth::user()->full_name . '_licience_document.' . $extension . '"',
        ];

        return response()->stream(
            function () use ($fileContents) {
                echo $fileContents;
            },
            200,
            $headers,
        );
    }

    public function clearImage()
    {
        $this->OLDuserImage = null;
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

    

    // Live check for password match
    public function updated($propertyName)
    {
        if ($propertyName === 'password' || $propertyName === 'newPasswordConfirm') {
            $this->passwordsMatch = $this->password === $this->newPasswordConfirm;
        }
    }

    public function editPassword()
    {
        $this->validate(
            [
                'oldPass' => 'required',
                'password' => 'required|min:8',
            ]
        );

        // Get the authenticated user
        $user = Auth::user();

        // Check if the old password matches the user's current password
        if (!Hash::check($this->oldPass, $user->password)) {
            $this->addError('oldPass', 'The current password is incorrect.');
            return;
        }

        // Change the password
        $user->changePassword($this->password);

        $this->alertSuccess('Password Changed!');
        $this->closeChangePassSec();
    }

    public function closeChangePassSec()
    {
        $this->reset(['oldPass', 'password', 'newPasswordConfirm', 'passwordsMatch', 'is_open_update_pass']);
    }

    public function mount()
    {
        $this->username = Auth::user()->username;
        $this->firstName = Auth::user()->first_name;
        $this->lastName = Auth::user()->last_name;
        $this->phone = Auth::user()->phone;
        $this->email = Auth::user()->email;
        $this->idNumber = Auth::user()->id_number;
        $this->driveLicienceNo = Auth::user()->driving_license_doc_url;
        $this->OLDuserImage = Auth::user()->image_url;
        $this->OLDuploadIDFile = Auth::user()->id_doc_url;
        $this->OLDuploadLicFile = Auth::user()->driving_license_doc_url;
    }

    public function openChangePass()
    {
        $this->is_open_update_pass = true;
    }

    public function updatingUsername()
    {
        $this->changes = true;
    }

    public function updatingFirstName()
    {
        $this->changes = true;
    }

    public function updatingLastName()
    {
        $this->changes = true;
    }

    public function updatingPhone()
    {
        $this->changes = true;
    }

    public function updatingEmail()
    {
        $this->changes = true;
    }

    public function updatingIdNumber()
    {
        $this->changes = true;
    }

    public function updatingDriveLicienceNo()
    {
        $this->changes = true;
    }

    public function saveInfo()
    {
        $this->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore(Auth::user()->id)],
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'idNumber' => 'nullable|string|max:255',
            'driveLicienceNo' => 'nullable|string|max:255',
        ]);

        if ($this->OLDuserImage) {
            $user_image_url = $this->OLDuserImage;
        } elseif ($this->userImage) {
            $this->validate([
                'userImage' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            ]);
            $user_image_url = $this->userImage->store(User::FILES_DIRECTORY, 's3');
        } else {
            $user_image_url = null;
        }

        if ($this->OLDuploadIDFile) {
            $user_id_url = $this->OLDuploadIDFile;
        } elseif ($this->uploadIDFile) {
            $this->validate([
                'uploadIDFile' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            ]);
            $user_id_url = $this->uploadIDFile->store(User::FILES_DIRECTORY, 's3');
        } else {
            $user_image_url = null;
        }

        if ($this->OLDuploadLicFile) {
            $user_lic_url = $this->OLDuploadLicFile;
        } elseif ($this->uploadLicFile) {
            $this->validate([
                'uploadLicFile' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            ]);
            $user_id_url = $this->uploadLicFile->store(User::FILES_DIRECTORY, 's3');
        } else {
            $user_image_url = null;
        }

        $user = User::find(Auth::user()->id);

        $u = $user->editInfo(
            $this->username,
            $this->firstName,
            $this->lastName,
            Auth::user()->type, // Assuming 'type' is a property of the currently authenticated user
            $this->email,
            $this->phone,
            $this->idNumber,
            $user_id_url,
            $this->driveLicienceNo,
            $user_lic_url,
            $user_image_url,
        );

        if ($u) {
            $this->alert('success', 'Updated Successfuly');
            $this->changes = false;
        } else {
            $this->alert('failed', 'Server error');
        }
    }

    public function changePassword()
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|string|min:6',
        ]);

        // Get the authenticated user
        /** @var User */
        $user = Auth::user();
        if ($user == null) {
            return $this->alert('failed', 'Unauthorized access');
        }

        // Check if the entered current password matches the user's actual password
        if (Hash::check($this->currentPassword, $user->password)) {
            // Current password is correct
            // Proceed to update the password
            $user->changePassword($this->newPassword);
            $this->alert('success', 'Updated Successfuly');
        } else {
            // Current password is incorrect
            $this->alert('failed', 'Incorrect password!');
        }
    }

    public function render()
    {
        return view('livewire.users.profile')->layout('layouts.app', ['page_title' => $this->page_title, 'profile' => 'active']);
    }
}
