@extends('layouts.app')
@section('title', 'Dashboard | Keracabs')
@section('content')
    <div class="row">
        <div class="col-10 offset-lg-1">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Sliders</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <form action="{{ route('slider.update', $banner->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div>
                            <div class="row align-items-center">
                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label class="form-label">Slider</label>
                                        <input type="file" name="image" class="form-control">
                                    </div>
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                                @if ($banner->image)
                                    <img src="{{ \App\Http\Helpers\BlogHelper::getBannerImagePath($banner->image) }}"
                                        style="float:left; left:50%; width:20%;" alt="Blog Image">
                                @else
                                    <p>No image available</p>
                                @endif
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form>
            </div>
        </div>
    </div>
@endsection
