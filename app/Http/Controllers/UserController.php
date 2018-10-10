<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Forms\UserForm;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->when(request('search'),function($query,$search){
                $query->where('name','like',"%{$search}%")
                ->orWhere('email','like',"%{$search}%");
            })
            ->orderBy('created_at','DESC')
            ->paginate();

        $title = "Listado de usuarios";
        return view('users.index', compact('users', 'title'));
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->paginate();

        $title = "Listado de usuarios en papelera";
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

    public function update(User $user, UpdateUserRequest $request)
    {
        $request->updateUser($user);

        return redirect()->route('users.show', $user);
    }

    public function trash(User $user)
    {
        $user->delete();
        $user->profile()->delete();

        return redirect()->route('users.index');
    }

    public function destroy($id)
    {
        $user = User::onlyTrashed()->where('id',$id)->firstOrFail();

        $user->forceDelete();

        return redirect()->route('users.trashed');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->where('id',$id)->firstOrFail();

        $user->restore();
        $user->profile()->restore();

        return redirect()->route('users.index');
    }

}
