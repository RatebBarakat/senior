@extends('layouts.app')

@section('title')
    reset password
@endsection
@section('content')
    <form action="{{route('changePassword.storePassword',["$token"])}}" method="post" class="container mt-3">
        @csrf
        reset password
        <div class="form-group">
            <label for="">password</label>
            <span class="text-danger">@error('password'){{$message}}@enderror</span>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="form-group">
            <label for="">password confirm</label>
            <span class="text-danger">@error('password-confirm'){{$message}}@enderror</span>
            <input type="password" name="password-confirm" class="form-control">
        </div>

        <button type="submit" class=" btn btn-success">submit</button>
    </form>
@endsection