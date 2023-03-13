@extends('layouts.admin')

@section('title')
    centers
@endsection

@section('content')
    @livewire('admin.super.centers')
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

        document.getElementById('show-add-admin').addEventListener('click',e => {
           document.getElementById('addAdmin').classList.toggle('active');
           document.getElementById('show-add-admin').classList.toggle('remove');
        });

        document.getElementById('addAdminButton').addEventListener('click',e => {
            e.preventDefault();
            document.getElementById('addAdminForm').submit();
        })
    </script>
@endpush
