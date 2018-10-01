@extends('layout')
@section('title','Crear usuario')

@section('content')
    <div class="card">
        <h4 class="card-header">Crear usuario</h4>
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

            <form action="{{route('users.store')}}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           value="{{old('name')}}"
                           placeholder="Tu nombre">
                </div>
                <div class="form-group">
                    <label for="name">Email</label>
                    <input type="email"
                           name="email"
                           value="{{old('email')}}"
                           id="email"
                           class="form-control"
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
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea name="bio" id="bio" class="form-control">{{old('bio')}}</textarea>
                </div>
                <div class="form-group">
                    <label for="profession_id">Profession</label>
                    <select name="profession_id" id="profession_id" class="form-control">
                        <option value="">Selecciona una profesion</option>
                        @foreach($professions as $profession)
                            <option value="{{$profession->id}}"{{old('profession_id') == $profession->id ? ' selected':''}}>
                                {{$profession->title}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Twitter</label>
                    <input type="text"
                           name="twitter"
                           id="twitter"
                           class="form-control"
                           value="{{old('twitter')}}"
                           placeholder="Tu twitter">
                </div>

                <h5>Habilidades</h5>
                @foreach($skills as $skill)
                    <div class="form-check form-check-inline">
                        <input name="skills[{{$skill->id}}]"
                               class="form-check-input"
                               type="checkbox"
                               id="skill_{{$skill->id}}"
                               value="{{$skill->id}}"
                               {{old("skills.{$skill->id}") ? 'checked':''}}>
                        <label class="form-check-label" for="skill_{{$skill->id}}">{{$skill->name}}</label>
                    </div>
                @endforeach

                <h5 class="mt-3">Rol</h5>

                @foreach($roles as $role=>$name)
                <div class="form-check form-check-inline">
                    <input class="form-check-input"
                           name="role"
                           type="radio"
                           id="role_{{$role}}"
                           value="{{$role}}"
                           {{ old('role')==$role ? 'checked' :'' }}>
                    <label class="form-check-label" for="role_{{$role}}">{{$name}}</label>
                </div>
                @endforeach



                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                    <a href="{{route('users.index')}}" class="btn btn-link">Regresar al listado de usuarios</a>
                </div>

            </form>
        </div>
    </div>


@endsection
