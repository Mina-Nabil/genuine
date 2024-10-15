<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\SetUserRequest;
use App\Models\Users\Notification;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function index()
    {
        $this->authorize('viewAny', User::class);

        return view('users.user-management-index');
    }

    public function contactIndex(){
        return view('users.contact-index');
    }

    public function show()
    {
        /** @var User */
        $user = auth()->user();
        $this->authorize('view', $user);

        $data['users'] = User::all();
        $data['types'] = User::TYPES;
        $data['user'] = $user;

        return view('users.profile', $data);
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $data['types'] = User::TYPES;
        return view('users.profile', $data);
    }

    public function insert(SetUserRequest $request)
    {
        $validated = $request->validated();
        $newUser = User::newUser(
            $validated['username'],
            $validated['first_name'],
            $validated['last_name'],
            $validated['type'],
            $validated['password'],
            $validated['email'],
            $validated['phone']
        );

        if (is_a($newUser, User::class)) {
            return redirect()->action([self::class, 'index']);
        } else {
            return redirect()->action([self::class, 'create'])->with(['alert_msg' => 'Failed to create user. Please check application logs']);
        }
    }

    public function update(SetUserRequest $request)
    {
        $validated = $request->validated();
        /** @var User */
        $user = User::findOrFail($validated['id']);

        $res = $user->editInfo(
            $validated['username'],
            $validated['first_name'],
            $validated['last_name'],
            $validated['type'],
            $validated['email'],
            $validated['phone']
        );

        if ($res) {
            return redirect()->action([self::class, 'show', [$user->id]]);
        } else {
            return redirect()->action([self::class, 'create'])->with(['alert_msg' => 'Failed to edit user. Please check application logs']);
        }
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            "id"    =>  "required|exists:users",
            "password"  =>  "required"
        ]);
        /** @var User */
        $user = User::findOrFail($validated['id']);
        $this->authorize('update', $user);
        $res = $user->changePassword($validated['password']);

        if ($res) {
            return redirect()->action([self::class, 'show', [$user->id]]);
        } else {
            return redirect()->action([self::class, 'create'])->with(['alert_msg' => 'Failed to edit user. Please check application logs']);
        }
    }

    public function toggleStatus($id)
    {
        /** @var User */
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $res = $user->toggle();
        if ($res) {
            return redirect()->action([self::class, 'show', [$user->id]]);
        } else {
            return redirect()->action([self::class, 'create'])->with(['alert_msg' => 'Failed to edit user. Please check application logs']);
        }
    }

    public function setNotfAsSeen($id)
    {
        /** @var Notification */
        $notf = Notification::findOrFail($id);
        Log::info($notf->is_seen);
        if (!$notf->is_seen)
            Log::info($notf->setAsSeen());
        return response()->json([
            "status"  =>   true
        ]);
    }

    public function calendar()
    {
        return view('users.calendar');
    }

    public function notfIndex()
    {
        return view('users.notifications');
    }


    public function fixPasswords()
    {
        $users = User::whereIn("id", [6])->get();
        foreach ($users as $user) {
            $user->changePassword($user->username);
        }
    }
}
