@extends('layouts.super-admin.super-admin')

@section('page-title')
    {{ __('Permissions') }}
@endsection
@section('links')
   
    <li class="breadcrumb-item"> {{ __('Permissions') }}</li>
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


<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add New Permission</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{route('superadmin.permission.store')}}">
            @csrf
            <div class="form-group">
              <label for="recipient-name" class="col-form-label">Permission Name:</label>
              <input type="text" required class="form-control" name="name" id="recipient-name">
            </div>
          
         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
     </form>
      </div>
    </div>
  </div>

  <div class="col-md-10 text-left mb-4">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Add Permission</button>
</div>
    <div class="card">
        @if($errors->has('error'))
        <div class="alert alert-danger">
            {{ $errors->first('error') }}
        </div>
      @endif
        <div class="card-body mt-3 mx-2">
        
            <div class="row">
             
                <div class="col-md-12 mt-2">

                    <div class="table-responsive">
                     
                        <table class="table table-bordered data-table" style="width: 100%">
                          
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Permission</th>
                                    <th>Status</th>
                               
                                
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $item)
                                <tr>
                                    
                                <td>{{$item->id}} </td>   
                                <td>{{$item->name}} </td>   

                              
                                <td>
                                  <div class="form-check form-switch d-inline-block col">
                                  <input class="form-check-input permission-toggle" id="is_active" name="is_active" type="checkbox" value="{{$item->id }}"  {{ $item->is_active == 1 ? 'checked' : '' }}>
                                  </div>
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




      $('.permission-toggle').click(function() {
            var isChecked = $(this).prop('checked');
            var permissionId = $(this).val(); // Get the value attribute, which contains the permission ID
            
            // Define your AJAX request
            $.ajax({
                type: 'POST',
                url: '{{ route('superadmin.permission.update_is_active', ['permission' => 'permissionId']) }}'.replace('permissionId', permissionId), // Replace 'permissionId' with the actual permission ID

                data: {
                    _token: '{{ csrf_token() }}',
                    permission_id: permissionId,
                    is_active: isChecked ? 1 : 0 // Convert true/false to 1/0
                },
                success: function(response) {
                    // Handle success response if needed
                    show_toastr('{{ __('Success') }}', '{{ __('Status Updated Successfully!') }}',
                                'success');
                },
                error: function(error) {
                    // Handle error if needed
                    console.error('AJAX request error', error);
                }
            });
        });
    });
  </script>
@endpush
