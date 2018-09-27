@extends('layout')
@section('title','Crear usuario')

@section('content')
    <div class="card">
        <h4 class="card-header">Editar usuario</h4>
        <div class="card-body">
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
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           value="{{old('nombre',$user->name)}}"
                           placeholder="Tu nombre">
                </div>
                <div class="form-group">
                    <label for="name">Email</label>
                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control"
                           value="{{old('email',$user->email)}}"
                           placeholder="Tu Email">
                </div>
                <div class="form-group">
                    <label for="name">Password</label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control"
                           placeholder="Mayor a 6 caracteres">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar usuario</button>
                <a href="{{route('users.index')}}">Regresar al listado de usuarios</a>
            </form>
        </div>
    </div>
@endsection
