<?php

namespace App\Livewire\Users;

use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    // use AlertFrontEnd;
    public $page_title = '• Profile';

    public $username;
    public $firstName;
    public $lastName;
    public $phone;
    public $email;

    public $changes;

    public $currentPassword;
    public $newPassword;
    public $password_confirmation;

    public $is_open_update_pass;

    public function mount()
    {
        $this->username = Auth::user()->username;
        $this->firstName = Auth::user()->first_name;
        $this->lastName = Auth::user()->last_name;
        $this->phone = Auth::user()->phone;
        $this->email = Auth::user()->email;
    }

    public function updatingcurrentPassword()
    {
        $this->username = strtolower($this->username);
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



    public function saveInfo()
    {
        $this->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore(auth()->user()->id)],
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        $user = User::find(Auth::user()->id);

        $u = $user->editInfo(
            $this->username,
            $this->firstName,
            $this->lastName,
            Auth::user()->type, // Assuming 'type' is a property of the currently authenticated user
            $this->email,
            $this->phone,
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
        if($user == null) return $this->alert('failed', 'Unauthorized access');
        
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
