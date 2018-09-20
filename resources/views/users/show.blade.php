@extends('layout')
@section('title', "Usuario {$id}")

@section('content')
    <h2>Usuario #{{$id}}</h2>

   <p>Mostrando detalle del usuario: {{$id}}</p>
@endsection


