@extends('layouts.admin')
@section('title')
    {{auth()->guard('admin')->user()->name}}
@endsection

@section('content')
    @livewire('admin.center.appointments')
@endsection

@push('js')
    <script !src="">
        window.addEventListener('show-complete-modal', () => {
            $('#completeModal').modal('show');
        });
        window.addEventListener('hide-complete-modal', event => {
            $('#completeModal').modal('hide')
        });
    </script>
@endpush

