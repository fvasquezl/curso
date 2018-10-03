<?php

namespace App\Http\Controllers;

use App\Http\Forms\UserForm;
use App\Http\Requests\CreateUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = "Listado de usuarios";
        return view('users.index', compact('users', 'title'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        return new UserForm('users.create', new User);
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();
        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return new UserForm('users.edit', $user);
    }

    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => '',
            'role' => '',
            'bio' => '',
            'profession_id' => '',
            'twitter' => '',
            'skills' => ''
        ]);


        if ($request['password'] != null) {
            $request['password'] = bcrypt($request['password']);
        } else {
            unset($request['password']);
        }

        $user->fill($request->all());
        $user->role = $request->role;
        $user->save();

        $user->profile->update($request->all());

        $user->skills()->sync($request->skills);

        return redirect()->route('users.show', $user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }

}
