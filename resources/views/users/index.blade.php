@extends('layout')
@section('title','Usuarios')

@section('content')

    <h1>{{$title}}</h1>
    <h4><a href="{{route('users.create')}}">Crear Usuario</a></h4>
    <ul>
        @forelse($users as $user)
            <li>
                {{$user->name}}, ({{$user->email}})
                <a href="{{route('users.show',$user)}}">Ver detalles</a> |
                <a href="{{route('users.edit',$user)}}">Editar Usuario</a> |
                <form action="{{route('users.delete',$user)}}" method="POST">
                    @csrf
                    {{method_field('DELETE')}}
                    <button type="submit">Eliminar</button>
                </form>
            </li>
        @empty
            <p>No hay usuarios registrados.</p>
        @endforelse
    </ul>
@endsection
