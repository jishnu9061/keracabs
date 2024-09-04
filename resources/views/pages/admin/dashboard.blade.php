@extends('layouts.app')

@section('title', 'Dashboard | Greenveel')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-4 col-md-6">
            <!-- card -->
            <div class="card card-h-100 cardColora">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-7">
                            <span class="text-muted mb-3 lh-2 d-block text-truncate"
                                style="color: #EEEEEE !important;">Manager</span>
                            <h4 class="mb-3" style="color: #EEEEEE !important;">
                                <span class="counter-value" data-target="{{ $counts['manager']['total'] }}">0</span>k
                            </h4>
                        </div>

                        <div class="col-5">
                            <div class="dashicon">
                                <i class="bx bx-layer"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">+{{ $counts['manager']['last_week'] }}</span>
                        <span class="ms-1 text-muted font-size-13" style="color: #EEEEEE !important;">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        <div class="col-xl-4 col-md-6">
            <!-- card -->
            <div class="card card-h-100 cardColorb">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-7">
                            <span class="text-muted mb-3 lh-2 d-block text-truncate"
                                style="color: #EEEEEE !important;">Devices</span>
                            <h4 class="mb-3" style="color: #EEEEEE !important;">
                                <span class="counter-value" data-target="{{ $counts['device']['total'] }}">0</span>
                            </h4>
                        </div>
                        <div class="col-5">
                            <div class="dashicon">
                                <i class="bx bx-rupee"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">+{{ $counts['device']['last_week'] }} </span>
                        <span class="ms-1 text-muted font-size-13" style="color: #EEEEEE !important;">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col-->

        <div class="col-xl-4 col-md-6">
            <!-- card -->
            <div class="card card-h-100 cardColorc">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-7">
                            <span class="text-muted mb-3 lh-2 d-block text-truncate"
                                style="color: #EEEEEE !important;">Routes</span>
                            <h4 class="mb-3" style="color: #EEEEEE !important;">
                                <span class="counter-value" data-target="{{ $counts['route']['total'] }}">0</span>M
                            </h4>
                        </div>
                        <div class="col-5">
                            <div class="dashicon">
                                <i class="bx bx-line-chart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">+ {{ $counts['route']['last_week'] }}</span>
                        <span class="ms-1 text-muted font-size-13" style="color: #EEEEEE !important;">Since last week</span>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3>Manager List</h3>
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable" class="table table-bordered   nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($managers as $manager)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><a href="addDevice.html">{{ $manager->name }}</a></td>
                                        <td>{{ $manager->user_name }}</td>
                                        <td>{{ $manager->password }}</td>
                                        <td>{{ $manager->contact }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
