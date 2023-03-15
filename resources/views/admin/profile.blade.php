@extends('layouts.admin')
@section('title')
    profile
@endsection

@section('content')
    @livewire('admin.adminprofile')
@endsection

@push('js')
    <script !src="">
        window.addEventListener('update-name', event => {
            $('#userDropdown').html(event.detail.name)
        })
    </script>
@endpush
