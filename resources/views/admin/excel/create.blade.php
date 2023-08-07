@extends('layouts.admin')

@section('title')
    donation reports
@endsection

@section('content')


<form action="{{route('admin.admincenter.excel.store')}}" method="post" enctype="multipart/form-data">
    @csrf
    @if (session('error'))
        <div class="alert alert-success">
            {{ session('error') }}
        </div>
    @endif

    <input type="file" name="excel" id="excel">
    <button type="submit" class="btn btn-success mb-3">import donations</button>
</form>

<table class="table">
    <thead class="bg-primary">
    <td class="text-white">#</td>
    <td class="text-white">select</td>
    <td class="text-white">quantity</td>
    <td class="text-white">blood_type</td>
    <td class="text-white">date</td>
    <td class="text-white">expire_at</td>
    <td class="text-white">taken</td>
    <td class="text-white">user</td>
    <td class="text-white">created_at</td>
    <td class="text-white">actions</td>
    </thead>
    <tbody>

    @forelse($donations as $donation)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>
                <input type="checkbox" name="selecteddonations[]" value="{{$donation->id}}" 
                      id="check{{$donation->id}}">
            </td>
            <td>{{$donation->quantity}}</td>
            <td>{{$donation->blood_type}}</td>
            <td>{{$donation->date}}</td>
            <td>{{$donation->expire_at}}</td>
            <td>{{$donation->taken}}</td>
            <td>{{$donation->appointment?->user?->name}}</td>
            <td>{{$donation->created_at}}</td>
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