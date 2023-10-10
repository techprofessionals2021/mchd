@extends('layouts.super-admin.super-admin')

@section('page-title')
    {{ __('Users') }}
@endsection
@section('links')
   
    <li class="breadcrumb-item"> {{ __('Users') }}</li>
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

            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Update User</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form method="POST" action="{{route('superadmin.update_user')}}">
                        @csrf
                        <div class="form-group">
                          <label for="recipient-name" class="col-form-label">Name:</label>
                          <input type="text" required class="form-control" name="name" id="name">
                        </div>
                      

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Email:</label>
                            <input type="text" required class="form-control" name="email" id="email">
                          </div>

                          <input type="hidden" name="user_id" id="user_id">


                          <div class="form-group">
                            <label for="permission">Role</label>
                            <select name="role" id="role" class="form-control">
                                @foreach ($role as $roles)
                                    <option value="{{ $roles->name }}">{{ $roles->name }}</option>
                                @endforeach
                            </select>
                         </div>

                    
                        
                        <div class="form-group" id="workspace-div" >
                            <label for="permission">Department - Workspace</label>
                            <select name="workspace_id" id="workspace" class="form-control">
                                @foreach ($workspace as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tags" class="col-form-label">{{ __('Tags') }}</label>
                            <input type="text" name="tags" class="tags form-control" id="tag-assign-user"  value="{{ old('tags') }}"
                               data-role="tagsinput" />
                            {{-- @if($errors->has('tags'))
                            <strong class="text-danger">{{ $errors->first('tags') }}</strong>
                            @endif --}}
                        </div>
                        
                     
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                 </form>
                  </div>
                </div>
              </div>
            <div class="row">
                <div class="col-md-12 mt-2">

                    <div class="table-responsive">
                        <table class="table table-bordered data-table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user as $item)
                                <tr>
                                    
                                <td>{{$item->id}} </td>   
                                <td>{{$item->name}} </td>   
                                <td>{{$item->email}} </td>   

                                <td>
                                    <div class="dropdown">
                                        <button class="btn-sm btn-secondary dropdown-toggle" type="button" id="submenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="submenuDropdown">
                                            <form method="POST" action="{{ route('superadmin.delete-user-superadmin', ['id' => $item->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item btn btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                            <a class="dropdown-item modal-id" href="#"  data-toggle="modal" data-target="#exampleModal" data-name="{{$item->name}}" data-id="{{$item->id}}" data-email="{{$item->email}}" >  <i class="fas fa-edit"></i> Edit User</a>
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

    });

    $('.modal-id').on('click', function () {
            // Get the data attributes
            const name = $(this).data('name');
            const email = $(this).data('email');
            const user_id = $(this).data('id');


            // Update the modal content with the data
            $('#exampleModal').find('.modal-title').text('Edit User');
            $('#exampleModal').find('#name').val(name);
            $('#exampleModal').find('#email').val(email);
            $('#exampleModal').find('#user_id').val(user_id);
            
        });

        // if ($('#role').val() === 'HOD') {
        //     $('#workspace-div').show();
        // }


      
        $('#role').change(function() {

    
            if ($(this).val() === 'HOD') {
                // Show the second div when "hod" is selected
                $('#workspace-div').show();
            } else {
                // Hide the second div for other role selections
                $('#workspace-div').hide();
            }
        });
 
  </script>



<script>
    var inputElement = document.querySelector('#tag-assign-user');

    new Tagify(inputElement)
</script>

@endpush


