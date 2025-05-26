<?php

namespace App\Livewire\Users;

use App\Models\Users\Driver;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Facades\Storage;
use Exception;

class Profile extends Component
{
    use WithFileUploads, AlertFrontEnd;
    public $page_title = '• Profile';

    public $user;

    public $username;
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $idNumber;
    public $driveLicienceNo;
    public $carLicienceNo;

    public $OLDuserImage;
    public $OLDuploadIDFile;
    public $OLDuploadLicFile;
    public $OLDuploadCarLicFile;
    public $userImage;
    public $uploadIDFile;
    public $uploadLicFile;
    public $uploadCarLicFile;

    //Drivers
    public $isOpenNewDriverSec = false;
    public $isEditDriverSec = false;
    public $shift_title;
    public $weight_limit;
    public $order_quantity_limit;
    public $car_type = Driver::CAR_TYPE_SEDAN;
    public $car_model;
    public $is_available;
    public $startTime;
    public $endTime;

    public $deleteDriverShiftId;

    public $changes;

    public $oldPass, $password, $newPasswordConfirm;
    public $passwordsMatch = true; // Used to track if the passwords match

    public $is_open_update_day_fee;
    public $editedDayFee;

    public $home_location_url_1;
    public $home_location_url_2;

    public function closeUpdateDayFee()
    {
        $this->is_open_update_day_fee = false;
        $this->editedDayFee = null;
    }

    public function openUpdateDayFee()
    {
        $this->is_open_update_day_fee = true;
        $this->editedDayFee = $this->user->driver_day_fees;
    }

    public function updateDayFee()
    {
        $this->validate([
            'editedDayFee' => 'required|numeric|min:0'
        ]);

        $res = $this->user->updateDeliveryFee($this->editedDayFee);

        if ($res) {
            $this->closeUpdateDayFee();
            $this->alertSuccess('updated successfuly!');
        } else {
            $this->alertFailed();
        }
    }

    public function openDeleteDriverConfirmation($id)
    {
        $this->deleteDriverShiftId = $id;
    }

    public function closeDeleteDriverConfirmation()
    {
        $this->deleteDriverShiftId = null;
    }

    public function deleteDriverShift()
    {
        $res = Driver::findOrFail($this->deleteDriverShiftId)->deleteDriver();

        if ($res) {
            $this->closeDeleteDriverConfirmation();
            $this->alertSuccess('Driver shift added');
        } else {
            $this->alertFailed('Server error');
        }
    }

    public function openNewDriverSec()
    {
        if ($this->user->type === User::TYPE_DRIVER) {
            $this->isOpenNewDriverSec = true;
        }
    }

    public function addDriver()
    {
        $this->validate([
            'shift_title' => 'required|string|max:255',
            'weight_limit' => 'nullable|integer|min:0',
            'order_quantity_limit' => 'nullable|integer|min:0',
            'car_type' => 'nullable|in:' . implode(',', Driver::CAR_TYPES),
            'car_model' => 'nullable|string|max:255',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime'
        ]);
        $res = Driver::createDriver($this->shift_title, $this->user->id, $this->startTime, $this->endTime, $this->weight_limit ? $this->weight_limit * 1000 : null, $this->order_quantity_limit, $this->car_type, $this->car_model);

        if ($res) {
            $this->closeDriverSections();
            $this->alertSuccess('Driver shift added');
        } else {
            $this->alertFailed('Server error');
        }
    }

    public function openEditDriverSec($id)
    {
        if ($this->user->type === User::TYPE_DRIVER) {
            $this->isEditDriverSec = $id;
            $driver = Driver::findOrFail($id);
            $this->shift_title = $driver->shift_title;
            $this->weight_limit = $driver->weight_limit / 1000;
            $this->order_quantity_limit = $driver->order_quantity_limit;
            $this->car_type = $driver->car_type;
            $this->car_model = $driver->car_model;
            $this->startTime = $driver->start_time->format('H:i');
            $this->endTime = $driver->end_time->format('H:i');
            if ($driver->is_available === 1) {
                $this->is_available = true;
            } else {
                $this->is_available = false;
            }
        }
    }

    public function updateDriver()
    {
        $this->validate([
            'shift_title' => 'required|string|max:255',
            'weight_limit' => 'nullable|integer|min:0',
            'order_quantity_limit' => 'nullable|integer|min:0',
            'car_type' => 'nullable|in:' . implode(',', Driver::CAR_TYPES),
            'car_model' => 'nullable|string|max:255',
            'is_available' => 'required|boolean',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime'
        ]);

        $res = Driver::findOrFail($this->isEditDriverSec)->updateDriver($this->shift_title, $this->weight_limit ? $this->weight_limit * 1000 : null, $this->startTime, $this->endTime, $this->order_quantity_limit, $this->car_type, $this->car_model, $this->is_available);

        if ($res) {
            $this->closeDriverSections();
            $this->alertSuccess('Driver shift updated');
        } else {
            $this->alertFailed('Server error');
        }
    }

    public function closeDriverSections()
    {
        $this->reset(['isOpenNewDriverSec', 'isEditDriverSec', 'shift_title', 'weight_limit', 'order_quantity_limit', 'car_type', 'car_model', 'is_available', 'startTime', 'endTime']);
    }

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

    public function updatingUploadIDFile()
    {
        $this->changes = true;
    }

    public function updatingUploadLicFile()
    {
        $this->changes = true;
    }

    public function updatingUploadCarLicFile()
    {
        $this->changes = true;
    }

    public function updatingUserImage()
    {
        $this->changes = true;
    }

    public function downloadIDDocument()
    {
        if (!$this->user->id_doc_url) {
            $this->alertFailed('No document available for download');
            return;
        }

        try {
            $fileContents = Storage::disk('s3')->get($this->user->id_doc_url);
            $extension = pathinfo($this->user->id_doc_url, PATHINFO_EXTENSION);
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $this->user->full_name . '_id_document.' . $extension . '"',
            ];

            return response()->stream(
                function () use ($fileContents) {
                    echo $fileContents;
                },
                200,
                $headers,
            );
        } catch (Exception $e) {
            report($e);
            $this->alertFailed('Error downloading document');
            return;
        }
    }

    public function downloadLicDocument()
    {
        if (!$this->user->driving_license_doc_url) {
            $this->alertFailed('No document available for download');
            return;
        }

        try {
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
        } catch (Exception $e) {
            report($e);
            $this->alertFailed('Error downloading document');
            return;
        }
    }

    public function downloadCarLicDocument()
    {
        if (!$this->user->car_license_doc_url) {
            $this->alertFailed('No document available for download');
            return;
        }

        try {
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
        } catch (Exception $e) {
            report($e);
            $this->alertFailed('Error downloading document');
            return;
        }
    }

    public function clearIDdocFile()
    {
        $this->reset('uploadIDFile', 'OLDuploadIDFile');
        $this->changes = true;
    }

    public function clearLicDocFile()
    {
        $this->reset('uploadLicFile', 'OLDuploadLicFile');
        $this->changes = true;
    }

    public function clearCarLicDocFile()
    {
        $this->reset('uploadCarLicFile', 'OLDuploadCarLicFile');
        $this->changes = true;
    }

    public function clearImage()
    {
        $this->OLDuserImage = null;
        $this->userImage = null;
        $this->changes = true;
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
        /** @var User */
        $user = Auth::user();
        if ($user->id !== 1) {
        $this->validate([
            'oldPass' => 'required',
                'password' => 'required|min:8',
            ]);
        } else {
            $this->validate([
                'password' => 'required|min:8',
            ]);
        }

        // Get the authenticated user
        $user = $this->user;

        // Check if the old password matches the user's current password
        if ($user->id != 1 && !Hash::check($this->oldPass, $user->password)) {
            $this->addError('oldPass', 'The current password is incorrect.');
            return;
        } elseif ($user->id == 1) {
            $user->changePassword($this->password);
            $this->alertSuccess('Password Changed!');
            $this->closeChangePassSec();
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

    public function mount($id)
    {
        $user = User::findOrFail($id);
        $this->username = $user->username;
        $this->firstName = $user->first_name;
        $this->lastName = $user->last_name;
        $this->phone = $user->phone;
        $this->email = $user->email;
        $this->idNumber = $user->id_number;
        $this->driveLicienceNo = $user->driving_license_number;
        $this->carLicienceNo = $user->car_license_number;
        $this->OLDuserImage = $user->image_url;
        $this->OLDuploadIDFile = $user->id_doc_url;
        $this->OLDuploadLicFile = $user->driving_license_doc_url;
        $this->OLDuploadCarLicFile = $user->car_license_doc_url;
        $this->home_location_url_1 = $user->home_location_url_1;
        $this->home_location_url_2 = $user->home_location_url_2;
        $this->user = $user;
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

    public function updatingCarLicienceNo()
    {
        $this->changes = true;
    }

    public function updatingHomeLocationUrl1()
    {
        $this->changes = true;
    }

    public function updatingHomeLocationUrl2()
    {
        $this->changes = true;
    }

    public function saveInfo()
    {
        $this->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->user->id)],
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'idNumber' => 'nullable|string|max:255',
            'driveLicienceNo' => 'nullable|string|max:255',
            'carLicienceNo' => 'nullable|string|max:255',
            'home_location_url_1' => 'nullable|string|max:2048',
            'home_location_url_2' => 'nullable|string|max:2048',
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
            $user_id_url = null;
        }

        if ($this->OLDuploadLicFile) {
            $user_lic_url = $this->OLDuploadLicFile;
        } elseif ($this->uploadLicFile) {
            $this->validate([
                'uploadLicFile' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            ]);
            $user_lic_url = $this->uploadLicFile->store(User::FILES_DIRECTORY, 's3');
        } else {
            $user_lic_url = null;
        }

        if ($this->OLDuploadCarLicFile) {
            $user_car_lic_url = $this->OLDuploadCarLicFile;
        } elseif ($this->uploadCarLicFile) {
            $this->validate([
                'uploadCarLicFile' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
            ]);
            $user_car_lic_url = $this->uploadCarLicFile->store(User::FILES_DIRECTORY, 's3');
        } else {
            $user_car_lic_url = null;
        }

        $user = $this->user;

        $u = $user->editInfo(
            $this->username,
            $this->firstName,
            $this->lastName,
            $this->user->type,
            $this->email,
            $this->phone,
            $this->idNumber,
            $user_id_url,
            $this->driveLicienceNo,
            $user_lic_url,
            $this->carLicienceNo,
            $user_car_lic_url,
            $user_image_url,
            null,
            $this->home_location_url_1,
            $this->home_location_url_2
        );

        if ($u) {
            $this->alertSuccess('Updated Successfully');
            $this->changes = false;
        } else {
            $this->alertFailed('Server error');
        }
    }

    public function changePassword()
    {
        /** @var User */
        $user = Auth::user();
        if ($user->id !== 1) {
            $this->validate([
                'currentPassword' => 'required',
                'newPassword' => 'required|string|min:6',
            ]);
        } else {
            $this->validate([
                'newPassword' => 'required|string|min:6',
            ]);
        }

        // Get the authenticated user
        /** @var User */
        $user = $this->user;
        if ($user == null) {
            return $this->alert('failed', 'Unauthorized access');
        }

        // Check if the entered current password matches the user's actual password
        if ($user->id !== 1 && Hash::check($this->currentPassword, $user->password)) {
            // Current password is correct
            // Proceed to update the password
            $user->changePassword($this->newPassword);
            $this->alert('success', 'Updated Successfuly');
        } elseif ($user->id === 1) {
            $user->changePassword($this->newPassword);
            $this->alert('success', 'Updated Successfuly');
        } else {
            // Current password is incorrect
            $this->alert('failed', 'Incorrect password!');
        }
    }

    public function render()
    {
        $carTypes = Driver::CAR_TYPES;
        return view('livewire.users.profile', [
            'carTypes' => $carTypes,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'profile' => 'active']);
    }
}
