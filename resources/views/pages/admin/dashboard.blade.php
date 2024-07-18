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
                                style="color: #eeeeee !important">Bookings</span>
                            <h4 class="mb-3" style="color: #eeeeee !important">
                                <span class="counter-value" data-target="{{ $counts['bookings'] }}">0</span>
                            </h4>
                        </div>

                        <div class="col-5">
                            <div class="dashicon">
                                <i class="bx bx-layer"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="ms-1 text-muted font-size-13" style="color: #eeeeee !important">Since last
                            week</span>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-4 col-md-6">
            <!-- card -->
            <div class="card card-h-100 cardColorb">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-7">
                            <span class="text-muted mb-3 lh-2 d-block text-truncate"
                                style="color: #eeeeee !important">Blogs</span>
                            <h4 class="mb-3" style="color: #eeeeee !important">
                                <span class="counter-value" data-target="{{ $counts['blogs'] }}">0</span>
                            </h4>
                        </div>
                        <div class="col-5">
                            <div class="dashicon">
                                <i class="bx bx-rupee"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="ms-1 text-muted font-size-13" style="color: #eeeeee !important">Since last
                            week</span>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col-->

        <div class="col-xl-4 col-md-6">
            <!-- card -->
            <div class="card card-h-100 cardColorc">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-7">
                            <span class="text-muted mb-3 lh-2 d-block text-truncate"
                                style="color: #eeeeee !important">Contact</span>
                            <h4 class="mb-3" style="color: #eeeeee !important">
                                <span class="counter-value" data-target="{{ $counts['contacts'] }}">0</span>
                            </h4>
                        </div>
                        <div class="col-5">
                            <div class="dashicon">
                                <i class="bx bx-line-chart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="ms-1 text-muted font-size-13" style="color: #eeeeee !important">Since last
                            week</span>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row-->

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Booking</h4>
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable" class="table table-bordered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Phone No</th>
                                    <th>Email</th>
                                    <th>Vehicle</th>
                                    <th>Message</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $booking->created_at->format('m/d/Y') }}</td>
                                        <td> {{ $booking->name }}</td>
                                        <td>{{ $booking->phone }}</td>
                                        <td>{{ $booking->email }}</td>
                                        <td>{{ $booking->vehicle }}</td>
                                        <td>{{ $booking->message }}</td>
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
