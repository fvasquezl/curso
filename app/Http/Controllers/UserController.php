<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
            ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(User $user,Request $request)
    {
        $this->validate($request,[
        'name' => 'required',
        'email' => ['required','email',Rule::unique('users')->ignore($user->id)],
        'password' => ''
        ]);

        if($request['password'] !=null){
           $request['password'] = bcrypt($request['password']);
        }else {
            unset($request['password']);
        }

        $user->update($request->all());

        return redirect()->route('users.show',$user);
    }
}
