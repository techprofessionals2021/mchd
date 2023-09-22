
<form method="post" action="{{ route('work-space-permission.store',[$currentWorkspace->id,$currentWorkspace->slug,$user->id]) }}">
    @csrf

    <div class="modal-body">
        <table class="table  mb-0" id="dataTable-1">
        <thead>
        <tr>
            <th>{{__('Module')}}</th>
            <th>{{__('Permissions')}}</th>
        </tr>
        </thead>
        <tbody> 
    
            <td>{{__('User')}}</td>
            <td>
                {{-- {{dd(in_array('invite user',$permissions))}} --}}
                <div class="row">
                    <div class="form-check form-switch d-inline-block col">
                        <input class="form-check-input" id="permission1" @if(in_array('invite user',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="invite user">
                        <label for="permission1" class="custom-control-label">{{__('Invite')}}</label><br>
                    </div>
                    {{-- <div class="form-check form-switch d-inline-block col">
                        <input class="form-check-input" id="permission8" @if(in_array('edit task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="edit task">
                        <label for="permission8" class="custom-control-label">{{__('Edit')}}</label><br>
                    </div>
                    <div class="form-check form-switch d-inline-block col">
                        <input class="form-check-input" id="permission9" @if(in_array('delete task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="delete task">
                        <label for="permission9" class="custom-control-label">{{__('Delete')}}</label><br>
                    </div>
                    <div class="form-check form-switch d-inline-block col">
                        <input class="form-check-input" id="permission6" @if(in_array('show task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="show task">
                        <label for="permission6" class="custom-control-label">{{__('Show')}}</label><br>
                    </div>
                    <div class="form-check form-switch d-inline-block col">
                        <input class="form-check-input" id="permission10" @if(in_array('move task',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="move task">
                        <label for="permission10" class="custom-control-label">{{__('Move')}}</label><br>
                    </div> --}}
                </div>
            </td>
            
        </tr>


        <td>{{__('Project')}}</td>
        <td>
            {{-- {{dd(in_array('invite user',$permissions))}} --}}
            <div class="row">
                <div class="form-check form-switch d-inline-block col">
                    <input class="form-check-input" id="permission1" @if(in_array('create project',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="create project">
                    <label for="permission1" class="custom-control-label">{{__('Create')}}</label><br>
                </div>

                
            
            </div>
        </td>
        
    </tr>




    
    <td>{{__('Calendar')}}</td>
    <td>
        {{-- {{dd(in_array('invite user',$permissions))}} --}}
        <div class="row">
            <div class="form-check form-switch d-inline-block col">
                <input class="form-check-input" id="permission1" @if(in_array('show calendar',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="show calendar">
                <label for="permission1" class="custom-control-label">{{__('Show')}}</label><br>
            </div>

    
        </div>
    </td>
    
</tr>


<td>{{__('Timesheet')}}</td>
<td>
    {{-- {{dd(in_array('invite user',$permissions))}} --}}
    <div class="row">
        <div class="form-check form-switch d-inline-block col">
            <input class="form-check-input" id="permission1" @if(in_array('show timesheet',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="show timesheet">
            <label for="permission1" class="custom-control-label">{{__('Show')}}</label><br>
        </div>


    </div>
</td>

</tr>


<td>{{__('Project Report')}}</td>
<td>
    {{-- {{dd(in_array('invite user',$permissions))}} --}}
    <div class="row">


        <div class="form-check form-switch d-inline-block col">
            <input class="form-check-input" id="permission1" @if(in_array('project report',$permissions)) checked="checked" @endif name="permissions[]" type="checkbox" value="project report">
            <label for="permission1" class="custom-control-label">{{__('Show')}}</label><br>
        </div>
    
    </div>
</td>

</tr>

  
      
    
  
        </tbody>
    </table>
    </div>
    
    <div class="modal-footer">
       <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
         <input type="submit" value="{{ __('Save Changes')}}" class="btn  btn-primary">
    </div>
</form>