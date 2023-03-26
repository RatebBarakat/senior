@extends('layouts.admin')
@section('title')
    dashboard
@endsection
@section('content')

    </canvas>
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <a class="col-xl-3 col-lg-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <!-- Earnings (Monthly) Card Example -->
        <a class="col-xl-3 col-md-6 mb-4"
         >
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                        </div>
                        <div class="col-auto">
{{--                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>--}}
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Earnings (Monthly) Card Example -->
        <a href="" class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">categories
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"></div>
                                </div>
                                {{-- <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Pending Requests Card Example -->
        <a href="" class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
<<<<<<< HEAD
=======

    <div class="row gy-4">
        <div class="col-lg-6">
          <div class="wrapper bg-white shadow border p-2 radius">
            <p>blood donated last week</p>
            <canvas class="" id="week"></canvas>    
          </div>
        </div>
        <div class="col-lg-6">
          <div class="wrapper bg-white shadow border p-2 radius">
            <p>blood donated last month</p>
            <canvas class="" id="month"></canvas> 
          </div>
        </div>
    </div>
      
      
    
    @endsection

    @php
        $data_week = json_encode($donationsWeek);
        $data_mounth = json_encode($donationsMounth);
    @endphp

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

        <script>
            var data = {!! $data_week !!};
            var blood_types = [];
            var blood_types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            var total_donated = [];
            

        for (var i = 0; i < blood_types.length; i++) {
            var blood_type = blood_types[i];
            var donation = data.find(function(item) {
                return item.blood_type === blood_type;
            });
            if (donation) {
                total_donated.push(donation.total_donated);
            } else {
                total_donated.push(0);
            }
        }

            var ctx = document.getElementById('week').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: blood_types,
                    datasets: [{
                        label: 'Total Donated',
                        data: total_donated,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>

<script>
    var data = {!! $data_mounth !!};
    var blood_types = [];
    var blood_types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
var total_donated = [];

for (var i = 0; i < blood_types.length; i++) {
    var blood_type = blood_types[i];
    var donation = data.find(function(item) {
        return item.blood_type === blood_type;
    });
    if (donation) {
        total_donated.push(donation.total_donated);
    } else {
        total_donated.push(0);
    }
}

    var ctx = document.getElementById('month').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: blood_types,
            datasets: [{
                label: 'Total Donated',
                data: total_donated,
                backgroundColor: 'rgba(0, 255, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
        
    @endpush
>>>>>>> 4495fd5fc3ed5bb086f659b6d56208c08a143aad

    <div class="row gy-4">
        <div class="col-lg-6 mb-4">
          <div class="wrapper bg-white shadow border p-2 radius">
            <p>blood donated last week</p>
            <canvas class="" id="week"></canvas>    
          </div>
        </div>
        <div class="col-lg-6 mb-4">
          <div class="wrapper bg-white shadow border p-2 radius">
            <p>blood donated last month</p>
            <canvas class="" id="month"></canvas> 
          </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="wrapper bg-white shadow border p-2 radius">
                expired blood
                <canvas id="expired-chart"></canvas>
            </div>
        </div>
    </div>
      
      
    
    @endsection

    @php
        $data_week = json_encode($donationsWeek);
        $data_mounth = json_encode($donationsMounth);
    @endphp

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script>
        function createChart(canvasId, data, labels, color, borderColor) {
        var total_donated = [];

        for (var i = 0; i < labels.length; i++) {
            var blood_type = labels[i];
            var donation = data.find(function(item) {
                return item.blood_type === blood_type;
            });
        if (donation) {
            total_donated.push(donation.total_donated * 1000);
            } else {
                total_donated.push(0);
            }
        }

        var ctx = document.getElementById(canvasId).getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Donated (in mills)',
                    data: total_donated,
                    backgroundColor: color,
                    borderColor: borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
    var labels = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    var donationsWeek = {!! json_encode($donationsWeek) !!};
    createChart('week', donationsWeek, labels, 'rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)');

    var donationsMounth = {!! json_encode($donationsMounth) !!};
    createChart('month', donationsMounth, labels, 'rgba(0, 255, 235, 0.2)', 'rgba(54, 162, 235, 1)');

    </script>


    <script>
        // Get the canvas element
var ctx = document.getElementById('expired-chart').getContext('2d');

// Set the data and options for the chart
var labels = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
var data = [];
var expireBlood = {!! json_encode($expireBlood) !!};
var nonExpireBlood = {!! json_encode($nonExpireBlood) !!};

for (var i = 0; i < labels.length; i++) {
    var bloodType = labels[i];
    var expireDonation = expireBlood.find(function(item) {
        return item.blood_type === bloodType;
    });
    var nonExpireDonation = nonExpireBlood.find(function(item) {
        return item.blood_type === bloodType;
    });
    var obj = {
        blood_type: bloodType,
        total_expire: expireDonation ? expireDonation.total_expire : 0,
        total_non_expire: nonExpireDonation ? nonExpireDonation.total_non_expire : 0
    };
    data.push(obj);
}

var expireData = data.map(function(item) {
    return item.total_expire * 1000;
});

var nonExpireData = data.map(function(item) {
    return item.total_non_expire * 1000;
});

var datasetExpire = {
    label: 'Expired Blood (in millis)',
    data: expireData,
    backgroundColor: 'rgba(255, 99, 132, 0.2)',
    borderColor: 'rgba(255, 99, 132, 1)',
    borderWidth: 1,
    borderRadius: 5 // Set the border radius
};

var datasetNonExpire = {
    label: 'Non-Expired Blood (in millis)',
    data: nonExpireData,
    backgroundColor: 'rgba(54, 162, 235, 0.2)',
    borderColor: 'rgba(54, 162, 235, 1)',
    borderWidth: 1,
    borderRadius: 5 // Set the border radius
};

var chartData = {
    labels: labels,
    datasets: [datasetExpire, datasetNonExpire]
};

var options = {
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero: true
            }
        }]
    }
};

// Create the chart
var myChart = new Chart(ctx, {
    type: 'bar',
    data: chartData,
    options: options
});

    </script>

    @endpush
