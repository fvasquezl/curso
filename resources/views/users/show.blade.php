@extends('layout')
@section('title', "Usuario {$user->id}")

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Usuario #{{$user->id}}</h4>
        </div>
        <div class="card-body">
            <p>Nombre del usuario: {{$user->name}}</p>
            <p>Correo Electronico: {{$user->email}}</p>
            <p>
                <a href="{{route('users.index')}}">Regresar al listado de usuarios</a>
            </p>
        </div>
    </div>
@endsection


