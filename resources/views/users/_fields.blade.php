@csrf
<div class="form-group">
    <label for="name">Nombre</label>
    <input type="text"
           name="name"
           id="name"
           class="form-control"
           value="{{old('name',$user->name)}}"
           placeholder="Tu nombre">
</div>
<div class="form-group">
    <label for="name">Email</label>
    <input type="email"
           name="email"
           value="{{old('email',$user->email)}}"
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
    <textarea name="bio" id="bio" class="form-control">{{old('bio',$user->profile->bio)}}</textarea>
</div>
<div class="form-group">
    <label for="profession_id">Profession</label>
    <select name="profession_id" id="profession_id" class="form-control">
        <option value="">Selecciona una profesion</option>
        @foreach($professions as $profession)
            <option value="{{$profession->id}}"{{old('profession_id',$user->profile->profession_id) == $profession->id ? ' selected':''}}>
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
           value="{{old('twitter',$user->profile->twitter)}}"
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
                {{$errors->any() ? old("skills.{$skill->id}") : $user->skills->contains($skill) ? 'checked':''}}>
        <label class="form-check-label" for="skill_{{$skill->id}}">{{$skill->name}}</label>
    </div>
@endforeach

<h5 class="mt-3">Rol</h5>

@foreach(trans('users.roles') as $role=>$name)
    <div class="form-check form-check-inline">
        <input class="form-check-input"
               name="role"
               type="radio"
               id="role_{{$role}}"
               value="{{$role}}"
                {{ old('role',$user->role)==$role ? 'checked' :'' }}>
        <label class="form-check-label" for="role_{{$role}}">{{$name}}</label>
    </div>
@endforeach

<h5 class="mt-3">Estado</h5>

@foreach(trans('users.states') as $state=>$label)
    <div class="form-check form-check-inline">
        <input class="form-check-input"
               name="state"
               type="radio"
               id="state_{{$state}}"
               value="{{$state}}"
                {{ old('state',$user->state)==$state ? 'checked' :'' }}>
        <label class="form-check-label" for="state_{{$state}}">{{$label}}</label>
    </div>
@endforeach