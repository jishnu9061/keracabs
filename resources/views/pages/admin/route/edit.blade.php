@extends('layouts.app')

@section('title', 'Dashboard | Edit Route')

@section('content')
    <div class="row">
        <div class="col-10 offset-lg-1">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Route</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('route.update', $route->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div>
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="route_from" class="form-label">Route From</label>
                                        <input class="form-control" type="text" name="route_from"
                                            value="{{ old('route_from', $route->route_from) }}" id="route_from">
                                        @error('route_from')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="route_to" class="form-label">Route To</label>
                                        <input class="form-control" type="text" name="route_to"
                                            value="{{ old('route_to', $route->route_to) }}" id="route_to">
                                        @error('route_to')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="min_charge" class="form-label">Minimum Charge</label>
                                        <input class="form-control" type="number" name="min_charge"
                                            value="{{ old('min_charge', $route->minimum_charge) }}" id="min_charge">
                                        @error('min_charge')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="charge_type" class="form-label">Charge Type</label>
                                        <select class="form-control" name="charge_type">
                                            <option value="{{ AdminConstants::ROUTE_TYPE_FAIR }}"
                                                {{ old('charge_type', $route->type) == AdminConstants::ROUTE_TYPE_FAIR ? 'selected' : '' }}>
                                                Fair
                                            </option>
                                            <option value="{{ AdminConstants::ROUTE_TYPE_STUDENT }}"
                                                {{ old('charge_type', $route->type) == AdminConstants::ROUTE_TYPE_STUDENT ? 'selected' : '' }}>
                                                Student
                                            </option>
                                        </select>
                                        @error('charge_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update
                                        Route</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
