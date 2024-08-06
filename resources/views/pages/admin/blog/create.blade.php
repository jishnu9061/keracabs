@extends('layouts.app')

@section('title', 'Dashboard | Greenveel')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-10 offset-lg-1">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Add Blogs</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('blog.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <div class="row align-items-center">
                                <!-- Title Field -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="title" class="form-label">Title</label>
                                        <input class="form-control" type="text" name="title" value="{{ old('title') }}" id="title">
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Image Field -->
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="image" class="form-label">Images</label>
                                        <input type="file" class="form-control" name="image" id="image">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Keywords Field -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="keywords" class="form-label">Keywords</label>
                                        <input class="form-control" type="text" name="keywords" value="{{ old('keywords') }}" id="keywords">
                                        @error('keywords')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description Field -->
                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Blog Details Field -->
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="ckeditor-classic" class="form-label">Blog details</label>
                                        <textarea name="blog_details" id="ckeditor-classic">{{ old('blog_details') }}</textarea>
                                        @error('blog_details')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
@endsection
