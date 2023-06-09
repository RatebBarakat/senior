<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>    {{auth()->guard('admin')->user()->center->name}} | report
    </title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        
    <style>
        table {
  border-collapse: collapse;
  width: 100%;
  max-width: 700px;
  margin: 0 auto;
  padding: 4px;
}

table th,
table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: center;
}

th{
    background-color: blue ;
    color: white ;
}
table th {
  font-weight: bold;
  text-align: center;
  vertical-align: middle;
}

table tbody tr:hover {
  background-color: #f5f5f5;
}

table td:first-child,
table th:first-child {
  border-left: none;
}

table td:last-child,
table th:last-child {
  border-right: none;
}

@media (max-width: 768px) {
  table {
    font-size: 12px;
  }
  
  table th,
  table td {
    padding: 6px;
  }
}

    </style>

</head>

<body>
    <h1 style="text-align: center;color: blue">{{ auth()->guard('admin')->user()->center->name }} reports</h1>

    @php
        $needed = [
            'bloodStocks' => 'blood stocks',
            'employees' => 'employees',
            'bloodRequests' => 'blood requests'
        ];
    @endphp
    
    @foreach ($needed as $key => $value)
        @if (array_key_exists($key, $data))
            <h2>{{ $value }}</h2>
            @if ($key == 'employees')
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>center id</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data[$key] as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->employeeCenter?->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif ($key == 'bloodRequests')
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Blood Type</th>
                            <th>Quantity</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data[$key] as $request)
                            <tr>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ $request->blood_type }}</td>
                                <td>{{ $request->quantity }}</td>
                                <td>{{ $request->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            
            @else  
            
                <ul>
                    <li><h3>expire</h3></li> <br>
                     <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Blood Type</th>
                                    <th>Quantity</th>
                                    <th>center</th>
                                    <th>expire at</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data[$key]['expire'] as $blood)
                                    <tr>
                                        <td>{{ $blood->blood_type }}</td>
                                        <td>{{ $blood->quantity }}</td>
                                        <td>{{ $blood->center->name }}</td>
                                        <td>{{$blood->expire_at->diffForHumans()}}</td>
                                        <td>{{ $blood->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>   
                    
                    <li><h3>non expire</h3></li> <br>
                    <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Blood Type</th>
                            <th>Quantity</th>
                            <th>center</th>
                            <th>expire at</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data[$key]['nonExpire'] as $blood)
                            <tr>
                                <td>{{ $blood->blood_type }}</td>
                                <td>{{ $blood->quantity }}</td>
                                <td>{{ $blood->center->name }}</td>
                                <td>{{$blood->expire_at->diffForHumans()}}</td>
                                <td>{{ $blood->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </ul>

                
            </div>
            
            @endif
        @endif
    @endforeach

            
</body>
</html>

