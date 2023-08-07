@extends('layouts.admin')

@section('title')
    excel reports
@endsection

@section('content')

<a href="{{route('admin.admincenter.excel.create')}}" class="btn btn-success mb-3">create center</a>

<table class="table">
    <thead class="bg-primary">
    <td class="text-white">#</td>
    <td class="text-white">name</td>
    <td class="text-white">admin</td>
    <td class="text-white">center</td>
    <td class="text-white">type</td>
    <td class="text-white">date</td>
    <td class="text-white">actions</td>
    </thead>
    <tbody>

    @forelse($excels as $excel)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$excel->file_name}}</td>
            <td>{{$excel->admin?->name ?? "no admin"}}</td>
            <td>{{$excel->center?->name ?? "no center"}}</td>
            <td>{{$excel->type}}</td>
            <td>{{$excel->created_at}}</td>
            <td>
                <button class="btn btn-sm btn-outline-success">
                    download
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6"> no reports </td>
        </tr>
    @endforelse
    </tbody>
</table>
@endsection