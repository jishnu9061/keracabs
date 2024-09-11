@extends('layouts.app')

@section('title', 'Dashboard | Create Route Stop')

@section('content')
    <div class="row">
        <div class="col-10 offset-lg-1">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Create Route Stop</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('stop.store',$route->id) }}" method="post">
                        @csrf
                        <div>
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="name" class="form-label">Stop Name</label>
                                        <input class="form-control" type="text" name="name" value="{{ old('name') }}" id="name">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="sequence" class="form-label">Sequence</label>
                                        <input class="form-control" type="number" name="sequence" value="{{ old('sequence') }}" id="sequence">
                                        @error('sequence')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="mb-4">
                                        <label for="name" class="form-label">Price</label>
                                        <input class="form-control" type="text" name="price" value="{{ old('price') }}" id="name">
                                        @error('price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Create Route Stop</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
