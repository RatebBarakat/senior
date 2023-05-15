@extends('layouts.admin')

@section('title')
    events
@endsection

@section('content')
    @livewire('admin.super.events')
@endsection

@push('js')
    <script !src="">
        window.addEventListener('show-add-modal', () => {
            $('#addModal').modal('show');
        });

        window.addEventListener('hide-add-modal', event => {
            $('#addModal').modal('hide')
        });

        window.addEventListener('open-delete-modal', event => {
            $('#deleteModal').modal('show')
        });

        window.addEventListener('hide-delete-modal', event => {
            $('#deleteModal').modal('hide')
        });

        window.addEventListener('open-edit-modal', event => {
            $('#editModal').modal('show');
        });

        window.addEventListener('close-edit-modal', event => {
           $('#editModal').modal('hide');
        });
    </script>
@endpush
