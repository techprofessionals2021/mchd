<form class="" method="post" action="{{ route('store_lang_workspace') }}">
    @csrf
     <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <label for="lang_code" class="col-form-label">{{ __('Language Code') }}</label>
                <input class="form-control" type="text" id="lang_code" name="lang_code" required="" placeholder="{{ __('Enter Language Code') }}">
            </div>
            <div class="col-md-12">
                <label for="lang_fullname" class="col-form-label">{{ __('Language Fullname') }}</label>
                <input class="form-control" type="text" id="lang_fullname" name="lang_fullname" required="" placeholder="{{ __('Enter Language Fullname') }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
        <input type="submit" value="{{ __('Save')}}" class="btn  btn-primary">
    </div>
    
</form>
