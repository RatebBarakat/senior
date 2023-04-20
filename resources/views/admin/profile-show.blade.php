@extends('layouts.admin')

@section('title')
    {{$admin->name}}
@endsection

@section('content')
    <div class="container-sm bg-white m-auto py-4 profile-show">
        <div class="row">
            <div class="col">
                @if($admin->profile->avatar)
                    <img style="max-width: 300px;" src="{{asset('storage/'.$admin->profile->avatar)}}" alt="">
                @else
                    <div class="noimage col-12 h-100">
                        no image
                    </div>
                @endif
            </div>
            <div class="col-md-8 row m-0 p-0">
                <div class="col-12">
                    <label for="name">name</label>
                    <div class="ready-only form-control" type="text" readonly>
                        {{$admin->name}}
                    </div>
                </div>

                <div class="col-12">
                    <label for="name">location</label>
                    <div class="ready-only form-control" type="text" readonly>
                        {{$admin->profile->locations ?
                            $admin->profile->locations : 'no location'
                        }}
                    </div>
                </div>

                <div class="col-12">
                    <label for="name">bio</label>
                    <div class="ready-only form-control" type="text" readonly>
                        {{$admin->profile->bio ??'no bio'}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
