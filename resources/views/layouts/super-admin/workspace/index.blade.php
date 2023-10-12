@extends('layouts.super-admin.super-admin')

@section('page-title')
    {{ __('Workspaces') }}
@endsection
@section('links')
   
    <li class="breadcrumb-item"> {{ __('Workspaces') }}</li>
@endsection
@push('css-page')
    <style>
        .page-content .select2-container {
            z-index: 0 !important;
        }

        .display-none {
            display: none !important;
        }
    </style>
@endpush
@section('content')
    <div class="card">

        <div class="card-body mt-3 mx-2">
            <div class="row">
                <div class="col-md-12 mt-2">

                    <div class="table-responsive">
                        <table class="table table-bordered data-table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Workspace Name</th>
                                    <th>Action</th>
                                
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workspace as $item)
                                <tr>
                                    
                                <td>{{$item->id}} </td>   
                                <td>{{$item->name}} </td>   
                                <td> 
                                    <button type="button" class="btn btn-danger btn-delete" data-record-id="{{ $item->id }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
        
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

@push('css-page')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush
{{-- <link rel="stylesheet" href="{{ asset('assets/custom/css/datatables.min.css') }}"> --}}



@push('scripts')

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script> --}}
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
    $(function () {

      var table = $('.data-table').DataTable();

    });


    $('.btn-delete').click(function () {

        var workspace_id = $(this).data('record-id');

            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to delete this item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                    type: 'POST',
                    url: '{{ route('superadmin.delete-workspace-superadmin', ['id' => 'workspace_id']) }}'.replace('workspace_id', workspace_id), // Replace 'permissionId' with the actual permission ID

                    data: {
                        _token: '{{ csrf_token() }}',
                        id: workspace_id
                     
                    },
                    success: function(response) {
                        // Handle success response if needed
                        show_toastr('{{ __('Success') }}', '{{ __('Workspace Deleted Successfully!') }}',
                                    'success');
                                    location.reload();
                    },
                    error: function(error) {
                        // Handle error if needed
                        console.error('AJAX request error', error);
                    }
                });
                }
            });
        });

  </script>



@endpush
