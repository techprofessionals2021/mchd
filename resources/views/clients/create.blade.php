<form class="" method="post" action="{{ route('clients.store',$currentWorkspace->slug) }}">
    @csrf
    <div class="modal-body">
    <div class="row">
        <div class="form-group ">
            <label for="name" class="col-form-label">{{ __('Name') }}</label>
            <input class="form-control" type="text" id="name" name="name" required="" placeholder="{{ __('Enter Name') }}">
        </div>
        <div class="form-group ">
            <label for="email" class="col-form-label">{{ __('Email') }}</label>
            <input class="form-control" type="email" id="email" name="email" required="" placeholder="{{ __('Enter Email') }}">
        </div>
        <div class="form-group ">
            <label for="password" class="col-form-label">{{ __('Password') }}</label>
            <input class="form-control" type="password" id="password" name="password" required="" placeholder="{{ __('Enter Password') }}">
        </div>
         </div>
          </div>
        <div class="modal-footer">
             <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
             <input type="submit" value="{{ __('Save Changes')}}" class="btn  btn-primary">
            
        </div>
   
</form>

