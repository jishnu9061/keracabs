@extends('layouts.app')

@section('title', 'Dashboard | Edit Manager')

@section('content')
    <div class="row">
        <div class="col-10 offset-lg-1">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Device</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('manager-device.update', $device->id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row align-items-center">
                            <!-- Manager Information -->
                            <div class="col-lg-6 col-md-6">
                                <div class="mb-4">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control" type="text" name="name"
                                        value="{{ old('name', $device->user_name) }}" id="name">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Password</label>
                                    <input class="form-control" type="text" name="password" value="{{ old('password') }}"
                                        id="password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Header one</label>
                                    <input class="form-control" type="text" name="header_one"
                                        value="{{ old('header_one', $device->header_one) }}" id="header_one">
                                    @error('header_one')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Header two</label>
                                    <input class="form-control" type="text" name="header_two"
                                        value="{{ old('header_two', $device->header_two) }}" id="header_two">
                                    @error('header_two')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Footer</label>
                                    <input class="form-control" type="text" name="footer"
                                        value="{{ old('footer', $device->footer) }}" id="footer">
                                    @error('footer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="logo" class="form-label">Logo</label>
                                        <input class="form-control file-input" type="file" name="logo" id="logo">
                                        @error('logo')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="qr_code" class="form-label">QR Code</label>
                                        <input class="form-control file-input" type="file" name="qr_code" id="qr_code">
                                        @error('qr_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    @if ($device->logo)
                                        <div class="image-preview">
                                            <img src="{{ asset('storage/device/' . $device->logo) }}" alt="Device Logo"
                                                class="img-fluid preview-img">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    @if ($device->qr_code)
                                        <div class="image-preview">
                                            <img src="{{ asset('storage/qr_code/' . $device->qr_code) }}" alt="QR Code"
                                                class="img-fluid preview-img">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Update
                                    Manager</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
