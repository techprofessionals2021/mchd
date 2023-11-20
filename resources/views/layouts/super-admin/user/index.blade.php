@extends('layouts.super-admin.super-admin')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                            <select name="role" id="role" class="form-control" required>
                                <option value="">- Select Role -</option>
                                @foreach ($role as $roles)
                                    <option value="{{ $roles->name }}">{{ $roles->name }}</option>
                                @endforeach
                            </select>
                         </div>

                    
                        
                        <div class="form-group" id="workspace-div" style="display: none" >
                            <label for="permission">Workspace</label>
                            <select name="workspace_id[]" id="workspace" class="form-control" multiple >
                                @foreach ($workspace as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group" id="department-div" style="display: none" >
                            <label for="permission">Department</label>
                            <select name="department_id[]" id="department_name" class="form-control" multiple >
                      
                            </select>
                        </div>

                        <div class="form-group" id="depart-role-div" style="display: none" >
                            <label for="permission">Depart Role</label>
                            <select name="depart_user_role[]" id="depart_user_role" class="form-control" multiple >
                                @foreach ($depart_user_role as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        
                        <div class="form-group" id="hod-div" style="display: none" >
                            <label for="permission">Hods</label>
                            <select name="hods[]" id="hod"  class="form-control" multiple>
                                {{-- @foreach ($hodUsers as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach --}}
                            </select>
                        </div>

                        <div class="form-group" id="executive-div" style="display: none">
                            <label for="permission">Executives</label>
                            <select name="executives[]" id="executive" class="form-control" multiple >
                          
                            </select>
                        </div>

                        {{-- <div class="form-group" id="executive-div" style="display: none">
                            <label for="permission">Executives</label>
                            <select name="executives[]" id="executive" class="form-control multi-select" multiple>
            
                            </select>
                        </div> --}}


                        {{-- <div class="form-group" id="executive-div" style="display: none"> --}}
                            {{-- <label for="permission">Executives</label>
                            <select class=" multi-select" id="assign_to" name="assign_to[]" data-toggle="select2" multiple="multiple" data-placeholder="{{ __('Select Users ...') }}" required>
                                <option value="AL">Alabama</option>
                                ...
                              <option value="WY">Wyoming</option>
                            </select> --}}
                  
                        {{-- </div> --}}



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
                                            <button type="submit" class="dropdown-item btn btn-danger btn-delete" data-record-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            {{-- <form method="POST" action="{{ route('superadmin.delete-user-superadmin', ['id' => $item->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item btn btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form> --}}
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">

@endpush
{{-- <link rel="stylesheet" href="{{ asset('assets/custom/css/datatables.min.css') }}"> --}}



@push('scripts')

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script> --}}
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">
    $(function () {

      var table = $('.data-table').DataTable();

    });

    $(document).ready(function() {
        if ($(".multi-select").length > 0) {
            $( $(".multi-select") ).each(function( index,element ) {
                var id = $(element).attr('id');
                   var multipleCancelButton = new Choices(
                        '#'+id, {
                            removeItemButton: true,
                        }
                    );
            });
       }
});
  
    $('.modal-id').on('click', function () {


  

            const user_id = $(this).data('id');


            // Update the modal content with the data
            $('#exampleModal').find('.modal-title').text('Edit User');
     
            $('#exampleModal').find('#user_id').val(user_id);

            $.ajax({
                type: 'POST',
                url: '{{ route('superadmin.get_user_role', ['id' => 'user_id']) }}'.replace('user_id', user_id), // Replace 'permissionId' with the actual permission ID

                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: user_id
                  
                },
                success: function(response) {



                              
             

                     $('#exampleModal').find('#name').val(response.user.name);
                     $('#exampleModal').find('#email').val(response.user.email);
                    //  $('#exampleModal').find('#role').val(response.role.name);
                     $('#exampleModal').find('#tag-assign-user').val(response.model_has_role.tag);
                     $('#exampleModal').find('#workspace').val(response.model_has_role.workspace_id);
                    //  $('#exampleModal').find('#depart_user_role').val(response.model_has_role.depart_user_role_id);
                    
                     
                    //  var selectedDepartmentIds = response.model_has_role.department_id;

                    // // Clear existing selections
                    // // $('#exampleModal').find('#department').val(null);

                    // // // Iterate over the options and mark them as selected if their value is in the array
                    // $('#exampleModal').find('#department option').each(function() {
                    //     var optionValue = $(this).val();

                    //     alert(optionValue);

                    //     if (selectedDepartmentIds.includes(optionValue)) {
                    //         $(this).prop("selected", true);
                    //     } else {
                    //         $(this).prop("selected", false);
                    //     }
                    // });





                    if (response.role.name == "HOD") {
                        // Show the second div when "hod" is selected
                        $('#workspace-div').show();
                        $('#department-div').show();
                        $('#depart-role-div').show();
                        $('#executive-div').hide();
                        $('#hod-div').hide();
                        
                    }
                    if (response.role.name == "Executive") {
                        // Show the second div when "hod" is selected
                        $('#workspace-div').hide();
                        $('#department-div').hide();
                        $('#depart-role-div').hide();


                        $('#hod-div').show();
                        $('#executive-div').hide();
                    }

                    if (response.role.name == "Ceo") {
                        // Show the second div when "hod" is selected
                        $('#workspace-div').hide();
                        $('#department-div').hide();
                        $('#depart-role-div').hide();


                        $('#hod-div').hide();
                        $('#executive-div').show();
                    }


           



                    var hodUsers = @json($hodUsers->pluck('id')->toArray());
                    

                    var executiveUsers = @json($executiveUsers->pluck('id')->toArray());

                    // var executiveUsers = executiveUsers.map(function(id) {
                    //     return String(id);
                    // });

              
                 
                    // console.log(executiveUsers);


                    var id = response.user.id;
                    var excludeUserId = user_id;
                    // console.log(id);
                    // console.log(executiveUsers);
                    // console.log(executiveUsers);
                    // console.log(response.model_has_role.executives);

                    // var checkExecutiveExistsinArray = executiveUsers.some(item => response.model_has_role.executives.includes(String(item)));
                    // var checkHodExistsinArray = hodUsers.some(item => response.model_has_role.hods.includes(String(item)));


                    // console.log(response.model_has_role.hods  , hodUsers );


                    $('#hod option').each(function () {
                        var optionValue = parseInt($(this).val()); // Convert the value to an integer

                        if (optionValue == excludeUserId) {
                            $(this).remove(); // Remove the option with the matching user ID
                        }

                        else if (response.model_has_role.hods.includes(optionValue)) {
                            $(this).prop("selected", true); // Select the option if its value is in the array
                        } else {
                            $(this).prop("selected", false); // Deselect the option if not in the array
                        }
                    });


          


                //     $('#hod option').each(function () {
                //     var optionValue = $(this).val();


                //     if (optionValue == excludeUserId) {
                //             // console.log('hod user');
                //             $(this).remove(); // Remove the option with the matching user ID
                //     }
                //    else  if (checkHodExistsinArray) {
                //        console.log('id includes hod');
                //         $(this).prop("selected", true);
                //     } else {
                //         $(this).prop("selected", false);
                //     }

                  
                // });

                $('#executive option').each(function () {

                    var optionValue = parseInt($(this).val()); // Convert the value to an integer

                    if (optionValue == excludeUserId) {
                        $(this).remove(); // Remove the option with the matching user ID
                    }

                    else if (response.model_has_role.executives.includes(optionValue)) {
                        $(this).prop("selected", true); // Select the option if its value is in the array
                    } else {
                        $(this).prop("selected", false); 
                    }

                });


                
                $('#workspace option').each(function () {

                    var optionValue = parseInt($(this).val()); // Convert the value to an integer

                     if (response.model_has_role.workspace_id.includes(optionValue)) {
                        $(this).prop("selected", true); // Select the option if its value is in the array
                    } else {
                        $(this).prop("selected", false); 
                    }

                    });


                               
                $('#depart_user_role option').each(function () {

                var optionValue = parseInt($(this).val()); // Convert the value to an integer

                if (response.model_has_role.depart_user_role_id.includes(optionValue)) {
                    $(this).prop("selected", true); // Select the option if its value is in the array
                } else {
                    $(this).prop("selected", false); 
                }

                });

                $('#department_name option').each(function () {

                    var optionValue = parseInt($(this).val()); // Convert the value to an integer
                    
                    if (response.model_has_role.department_id.includes(optionValue)) {
                        $(this).prop("selected", true); // Select the option if its value is in the array
                    } else {
                        $(this).prop("selected", false); 
                    }

                    });


            


                // $('#executive').select2();


                },
                error: function(error) {
                    // Handle error if needed
                    console.error('AJAX request error', error);
                }
            });


    


            // var hodUsers = @json($hodUsers);
            // var executiveUsers = @json($executiveUsers);

            // console.log(hodUsers);




            
        });


        

        // $('#workspace').change(function() {

        //     const workspace_id = $(this).val();

        //     var select = $('#department_name');

        //     select.empty(); // Clear existing options

        //     $.ajax({
        //     type: 'POST',
        //     url: '{{ route('superadmin.get_department', ['id' => 'workspace_id']) }}'.replace('workspace_id', workspace_id), // Replace 'permissionId' with the actual permission ID

        //     data: {
        //         _token: '{{ csrf_token() }}',
        //         workspace_id: workspace_id
            
        //     },
        //     success: function(response) {


        //         $.each(response, function (index, item) {

        //         select.append('<option value="' + item.id + '">' + item.name + '</option>');
        //         });


        //     },
        //     error: function(error) {
        //         // Handle error if needed
        //         console.error('AJAX request error', error);
        //     }
        //     });


        //     });

 
        var hodUsers = @json($hodUsers);
                    var selectElement = $("#hod");
                    $.each(hodUsers, function (index, user) {
                        selectElement.append($("<option></option>")
                            .attr("value", user.id)
                            .text(user.name));
                    });

                    // Populate the "executive" select element
                    var executiveUsers = @json($executiveUsers);
                    var selectElement1 = $("#executive");
                    $.each(executiveUsers, function (index, user) {
                        selectElement1.append($("<option></option>")
                            .attr("value", user.id)
                            .text(user.name));
                    });


                               // Populate the "executive" select element
                  var Departments = @json($department);
                    var select = $("#department_name");
                    $.each(Departments, function (index, Departments) {
                        select.append($("<option></option>")
                            .attr("value", Departments.id)
                            .text(Departments.name));
                    });
      
        $('#role').change(function() {


    
            if ($(this).val() === 'HOD') {
                // Show the second div when "hod" is selected
                $('#workspace-div').show();
                $('#department-div').show();
                $('#depart-role-div').show();

                $('#executive-div').hide();
                $('#hod-div').hide();
            }
            if ($(this).val() === 'Executive') {
                // Show the second div when "hod" is selected
                $('#workspace-div').hide();
                $('#department-div').hide();
                $('#depart-role-div').hide();

                $('#hod-div').show();
                $('#executive-div').hide();
            }

            if ($(this).val() === 'Ceo') {
                // Show the second div when "hod" is selected
                $('#workspace-div').hide();
                $('#department-div').hide();
                $('#depart-role-div').hide();


                $('#hod-div').hide();
                $('#executive-div').show();
            }
            
            else {
                // Hide the second div for other role selections
                // $('#workspace-div').hide();
                // $('#hod-div').hide();
            }
        });

        $('#executive-div').show();


        $('.btn-delete').click(function () {

            var user_id = $(this).data('record-id');

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
                        url: '{{ route('superadmin.delete-user-superadmin', ['id' => 'user_id']) }}'.replace('user_id', user_id), // Replace 'permissionId' with the actual permission ID

                        data: {
                            _token: '{{ csrf_token() }}',
                            id: user_id
                        
                        },
                        success: function(response) {
                            // Handle success response if needed
                            show_toastr('{{ __('Success') }}', '{{ __('User Deleted Successfully!') }}',
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



<script>
    var inputElement = document.querySelector('#tag-assign-user');

    new Tagify(inputElement)
</script>

@endpush


