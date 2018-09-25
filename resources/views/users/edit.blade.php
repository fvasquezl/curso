@extends('layout')
@section('title','Crear usuario')

@section('content')

    <h1>Editar usuario</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <p>Por favor corrige los siguientes errores</p>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{route('users.update',$user)}}" method="POST">
        {{method_field('PUT')}}
        @csrf
        <label for="name">Nombre</label>
        <input type="text"
               name="name"
               id="name"
               value="{{old('nombre',$user->name)}}"
               placeholder="Tu nombre">
        <label for="name">Email</label>
        <input type="email"
               name="email"
               value="{{old('email',$user->email)}}"
               id="email"
               placeholder="Tu Email">
        <label for="name">Password</label>
        <input type="password"
               name="password"
               id="password"
               placeholder="Mayor a 6 caracteres">
        <button type="submit">Actualizar usuario</button>
    </form>
    <p>
        <a href="{{route('users.index')}}">Regresar al listado de usuarios</a>
    </p>
@endsection
