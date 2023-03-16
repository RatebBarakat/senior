@extends('layouts.admin')
@section('title')
    {{auth()->guard('admin')->user()->center->name}}
@endsection

@section('content')
    <h4>{{auth()->guard('admin')->user()->center->name}}</h4>
    @livewire('admin.center.index')
@endsection

