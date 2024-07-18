@extends('layouts.app')

@section('title', 'Dashboard | Greenveel')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-10 offset-lg-1">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Blogs</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('blog.update', $blog->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div>

                            <div class="row align-items-center">
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="choices-single-default" class="form-label">Title
                                        </label>
                                        <input class="form-control ron" type="text" name="title"
                                            value="{{ old('title', $blog->title) }}" id="example-text-input">
                                    </div>
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label class="form-label">Images</label>
                                        <input type="file" class="form-control" name="image">
                                    </div>
                                </div>
                                @if ($blog->image)
                                    <img src="{{ \App\Http\Helpers\BlogHelper::getBlogImagePath($blog->image) }}" style="float:left; left:50%; width:20%;"
                                        alt="Blog Image">
                                @else
                                    <p>No image available</p>
                                @endif
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="ckeditor-classic" class="form-label">Blog details</label>
                                        <textarea name="description" id="ckeditor-classic">{{ old('description', $blog->description) }}</textarea>
                                        @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    </div>
                                </div>

                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit
                                    </button>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- Single select input Example -->
                    </form>




                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>

    </div>
    </div>
@endsection
