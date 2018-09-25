<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = "Listado de usuarios";
        return view('users.index', compact('users','title'));
    }

    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    public function create()
    {
       return view('users.create');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
            ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('users.index');
    }
}
