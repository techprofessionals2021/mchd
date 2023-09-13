<form class="" method="post" action="{{ route('contract_type.store',[$currentWorkspace->slug]) }}">
    @csrf
<div class="modal-body">
    
    <div class="row">
        @if ($currentWorkspace->is_chagpt_enable())
        <div class="text-end col-12">
            <a href="#" data-size="md" data-ajax-popup-over="true" class="btn btn-sm btn-primary" data-url="{{ route('generate',['contract type']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate with AI') }}" data-title="{{ __('Generate Contract Type Name') }}">
                <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}</a>
        </div>
        @endif
        <div class="form-group col-12">
            {{ Form::label('name', __('Contract Type Name'),['class'=>'col-form-label']) }}
            {{ Form::text('name', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
    </div>             
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>
    
</form>