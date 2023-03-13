@extends('layouts.admin')

@section('title')
    roles and permissions
@endsection

@section('content')
    @livewire('admin.roles-permissions')
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
