@extends('layout')
@section('title','Crear usuario')

@section('content')
    @card
        @slot('header','Crear usuario')

            @include('shared._errors')
            <form action="{{route('users.store')}}" method="POST">
                @render('UserFields',['user'=>$user])

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                    <a href="{{route('users.index')}}" class="btn btn-link">Regresar al listado de usuarios</a>
                </div>
            </form>
    @endcard
@endsection
