<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;


use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserAuthController extends Controller

{

    function login()
    {
        return view('auth.login');
    }
    function register()
    {
        return view('auth.register');
    }
    function create(Request $request)
    {
        //validate request
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|min:5|max:12'
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;

        $query = $user->save();
        if ($query) {
            return back()->with('success', 'You have been successfuly registered');
        } else {
            return back()->with('fail', 'Something went wrong');
        }
    }

    function check(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5|max:12'
        ]);
        //if form validated successfuly,the process login
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $request->session()->put('LoggedUser', $user->id);
                return redirect('profile');
            } else {
                return back()->with('fail', 'Invalid password');
            }
        } else {
            return back()->with('fail', 'No account found for this email');
        }
    }
    function profile()
    {
        if (session()->has('LoggedUsed')) {
            $user = User::where('id', '=', session('LoggedUser'))->first();
            $data = [
                "LoggedUserInfo" => $user
            ];
        }
        return view('admin.profile', $data);
    }
    function logout()
    {
        if (session()->has('LoggedUser')) {
            session()->pull('LoggedUser');
            return redirect('login');
        }
    }
}
