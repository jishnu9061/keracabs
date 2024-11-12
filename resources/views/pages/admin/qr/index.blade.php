@extends('layouts.app')

@section('title', 'Dashboard | Edit QR Code')

@section('content')
    <div class="row">
        <div class="col-10 offset-lg-1">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">QR Code</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('qr.update', $qrCode->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div>
                            <div class="row align-items-center">
                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="qr_image" class="form-label">Upload QR Code Image</label>
                                        <input class="form-control" type="file" name="image" id="image" accept="image/*">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center mb-4">
                                    <div id="qrCodeDisplay">
                                        @if($qrCode->image)
                                            <img src="{{ asset('storage/qr/' . $qrCode->image) }}" alt="Uploaded QR Code" style="max-width: 250px;">
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Update QR Code</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
