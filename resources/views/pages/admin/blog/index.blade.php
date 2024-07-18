@extends('layouts.app')
@section('title', 'Dashboard | Greenveel')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">View Blogs</h4>
                <div class="page-title-right">
                    <a class="btn btn-primary" href="{{ route('blog.create') }}"><i class="bx bx-plus"></i> Add
                        Blogs</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable-buttons" class="table table-bordered  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Date</th>
                                    <th>Blog title</th>
                                    <th>Blog detail</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($blogs as $blog)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $blog->created_at }}</td>
                                    <td>{{ $blog->title }}</td>
                                    <td>{{ Str::limit(strip_tags($blog->description), 50) }}</td>
                                    <td><img src="{{ \App\Http\Helpers\BlogHelper::getBlogImagePath($blog->image) }}" width="60"></td>
                                    <td> <a href="{{ route('blog.edit',$blog->id) }}" class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a class="btn btn-outline-secondary btn-sm delete-btn" data-href="{{ route('blog.delete',$blog->id) }}" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRsN/hYZyoD1t3R2TgC8X5dB2T2syuYrO6+rLdU5e" crossorigin="anonymous">
</script>
<script src="{{ asset('admin/libs/jquery/jquery.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        var App = {
            initialize: function() {
                $(document).on('click', '.delete-btn', function(e) {
                    e.preventDefault();
                    var row = $(this).closest('tr');
                    var url = $(this).data('href');
                    App.deleteItem(row, url);
                });
            },
            deleteItem: function(row, url) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this blog!",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    confirmButtonColor: '#0fb390'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            success: function(data) {
                                if (data.success) {
                                    row.remove();
                                    location.reload();
                                } else {
                                    location.reload();
                                }
                            },
                            error: function(data) {
                                location.reload();
                                console.log(data);
                            }
                        });
                    }
                });
            }
        };

        App.initialize();
    });
</script>
