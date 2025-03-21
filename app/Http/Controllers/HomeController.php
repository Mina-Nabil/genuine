<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Users\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // return view('home.index');
        return redirect()->Route('login');
    }

    public function login(Request $request)
    {
        /** @var User */
        $user = Auth::user();
        if ($user) return redirect($user->home_page);
        return view('auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->action([self::class, 'login']);
    }

    public function authenticate(LoginRequest $request)
    {
        $res = User::login(...$request->validated());
        if (is_a($res, User::class)) {
            return redirect($res->home_page);
        } else {
            return redirect('login')->with(['alert_msg' => $res]);
        }
    }
}
