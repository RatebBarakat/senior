@extends('layouts.admin')
@section('title')
    {{auth()->guard('admin')->user()->center->name}}
@endsection

@section('content')
    <h4>{{auth()->guard('admin')->user()->center->name}} reports</h4>
    <form action="{{route('admin.admincenter.reports.store')}}" method="post">
        @csrf
        <div class="d-flex">
            <span class="p-2">
                <label for="bloodStocks">blood stocks</label>
                <input type="checkbox" name="needed[]" value="bloodStocks" id="bloodStocks">
            </span>
            <span class="p-2">
                <label for="employees">employees</label>
                <input type="checkbox" name="needed[]" value="employees" id="employees">
            </span>
            <span class="p-2">
                <label for="bloodRequests">blood requests</label>
                <input type="checkbox" name="needed[]" value="bloodRequests" id="bloodRequests">
            </span>
        </div>
        <button class="btn btn-success" type="submit">generate</button>
    </form>
@endsection
