@extends('layouts.admin')
@section('title')
    blood request
@endsection

@section('content')
    @livewire('admin.blood-request.blood-request-edit',
     ['bloodRequest' => $bloodRequest,'AvailableBLood' => $AvailableBLood])
@endsection

@push('js')
    <script !src="">
        window.addEventListener('show-complete-modal', event => {
            $('#showComleteModal').modal('show')
        })
        window.addEventListener('hide-complete-modal', event => {
            $('#showComleteModal').modal('hide')
        })
    </script>
@endpush
