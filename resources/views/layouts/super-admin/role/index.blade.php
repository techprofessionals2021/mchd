@extends('layouts.super-admin.super-admin')

@section('page-title')
    {{ __('Roles') }}
@endsection
@section('links')
   
    <li class="breadcrumb-item"> {{ __('Roles') }}</li>
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
          <h5 class="modal-title" id="exampleModalLabel">Add New Role</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{route('superadmin.role_store')}}">
            @csrf
            <div class="form-group">
              <label for="recipient-name" class="col-form-label">Role Name:</label>
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



  <div class="modal fade" id="exampleModalPermission" tabindex="-1" role="dialog" aria-labelledby="exampleModalPermission" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">All Permissions</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{route('superadmin.assign_permission')}}">
            @csrf
            <input type="hidden" name="role_id" id="role_id">

          <table class="table">
            <thead>
              <tr>
                <th>Permission</th>
                <th>Assign</th>
              </tr>
            </thead>
            <tbody>
              @foreach($permission as $item)
                <tr>
                  <td>{{ $item->name }}</td>
                  <td>
                    <div class="form-check form-switch d-inline-block col">
                    <input class="form-check-input" id="permission{{$item->id}}" name="permissions[]" type="checkbox" value="{{$item->id}}" >
                    </div>

                   
             
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
  
        
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
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Add Role</button>
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
                                    <th>Role</th>
                                    <th>Action</th>
                               
                                
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($role as $item)
                                
                                <tr>
                                    
                                <td>{{$item->id}} </td>   
                                <td>{{$item->name}} </td>   
                              
 
                                <td>
                                  <div class="dropdown">
                                      <button class="btn-sm btn-secondary dropdown-toggle" type="button" id="submenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          Actions
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="submenuDropdown">
                                         
                                          <a class="dropdown-item modal-idd" href="#"  data-toggle="modal" data-target="#exampleModalPermission"  data-id="{{$item->id}}"  data-permission-id="" >  <i class="fas fa-edit"></i> Assign Permission User</a>
                                          <!-- Add more submenu items here -->
                                      </div>
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



      $('.modal-idd').click(function(e) {
      e.preventDefault(); // Prevent the default behavior of the link

      // Get the data-id attribute value
      var role_id = $(this).data('id');

  
      $('#role_id').val(role_id);

      var url = "{{ route('superadmin.get_permission_by_role', ':role_id') }}".replace(':role_id', role_id);


      $.ajax({
        type: 'GET',
        url: url, // Replace with your actual route URL
        success: function(data) {

          data.forEach(function(permission) {
    // Access properties of each permission object within the loop

        //  alert(id);
          // var inputValue = $('#permission7').val();


          var permissionIds = @json($permission->pluck('id')->toArray());

          // Assuming 'id' contains the ID you want to match
          var id = permission.id;

          // console.log(permissionIds ,  id);

          // Check if 'id' is included in the permission IDs array
          if (permissionIds.includes(id)) {
              // If 'id' is in the array, check the corresponding checkbox
              // console.log('id includes');
              $('#permission' + id).prop('checked', true);
          } else {
            // console.log('id not includes');
              // If 'id' is not in the array, uncheck the checkbox
              $('#permission' + id).prop('checked', false);
          }


                              
      
  });
        
        },
        error: function(error) {
            // Handle errors if the request fails
            console.log(error);
        }
        
    });


    
    });

    });
  </script>
@endpush
