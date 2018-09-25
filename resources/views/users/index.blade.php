@extends('layout')
@section('title','Usuarios')

@section('content')

    <h1>{{$title}}</h1>
    <h2><a href="{{route('users.create')}}">Crear Usuario</a></h2>
    <ul>
        @forelse($users as $user)
            <li>
                {{$user->name}}, ({{$user->email}})
                <a href="{{route('users.show',$user->id)}}">Ver detalles</a>
            </li>
        @empty
            <p>No hay usuarios registrados.</p>
        @endforelse
    </ul>
@endsection
