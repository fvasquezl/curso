<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\Profession;
use App\Models\Skill;
use App\User;
use Illuminate\Http\Request;
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
        $professions = Profession::orderBy('title','ASC')->get();
        $skills = Skill::orderBy('name', 'ASC')->get();
        $roles = trans('users.roles');

       return view('users.create', compact('professions','skills','roles'));
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();
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
        'password' => '',
        ]);

        if($request['password'] !=null){
           $request['password'] = bcrypt($request['password']);
        }else {
            unset($request['password']);
        }

        $user->update($request->all());

        return redirect()->route('users.show',$user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }

}
