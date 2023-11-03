<form class="" method="post" action="{{ route('projects.update', [$currentWorkspace->slug, $project->id]) }}" onkeydown="return event.key != 'Enter';">
    @csrf
    <div class="modal-body">
        <div class="row">
            @if ($currentWorkspace->is_chagpt_enable())
                <div class="text-end col-12">
                    <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                        data-url="{{ route('generate', ['project']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ __('Generate with AI') }}"
                        data-title="{{ __('Generate Project Name & Description') }}">
                        <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}</a>
                </div>
            @endif

            <div class="form-group col-md-12">
                <label for="projectname" class="form-label">{{ __('Name') }}</label>
                <input class="form-control" type="text" id="projectname" name="name" required=""
                    placeholder="{{ __('Project Name') }}" value="{{ $project->name }}">
            </div>
            <div class="form-group col-md-12">
                <label for="description" class="form-label">{{ __('Description') }}</label>
                <textarea rows="3" class="form-control" id="description" name="description" required=""
                    placeholder="{{ __('Add Description') }}">{{ $project->description }}</textarea>
            </div>
            <div class="form-group col-md-12">
                <label for="status" class="form-label">{{ __('Status') }}</label>
                <select id="status" name="status" class="form-control select2">
                    <option value="Ongoing">{{ __('Ongoing') }}</option>
                    <option value="Finished" @if ($project->status == 'Finished') selected @endif>{{ __('Finished') }}
                    </option>
                    {{-- <option value="OnHold" @if ($project->status == 'OnHold') selected @endif>{{ __('OnHold') }}
                    </option> --}}
                </select>
            </div>

            <div class="col-md-12">

                <label class="col-form-label">{{ __('Duration')}}</label>
                  <div class='input-group form-group'>
                        <input type='text' class=" form-control form-control-light" id="duration" name="duration" required autocomplete="off"
                             placeholder="Select date range" />
                             <input type="hidden" name="start_date">
                             <input type="hidden" name="end_date">
                            <span class="input-group-text"><i
                            class="feather icon-calendar"></i></span>
                    </div>
            </div>

            <div class="form-group col-md-12">
                <label class="col-form-label">{{ __('Assign To')}}</label>
                <select class="multi-select" multiple="multiple" id="assign_to" name="users_list[]" required>

                    @foreach($currentWorkspace->users as $u)
                      {{-- @if (auth()->id() != $u->id) --}}
                        <option @if(in_array($u->id,$project->users->pluck('id')->toArray())) selected @endif value="{{$u->id}}">{{$u->name}} - {{$u->email}}</option>
                      {{-- @endif --}}
                    @endforeach
                </select>
            </div>

            <div class="col-md-12">
                <label for="tags" class="col-form-label">{{ __('Tags') }}</label>
                <input type="text" name="tags" id="tag-project-edit" class="tags form-control" value="{{ old('tags',$project->tags) }}"
                   data-role="tagsinput" />
                @if($errors->has('tags'))
                <strong class="text-danger">{{ $errors->first('tags') }}</strong>
                @endif
             </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <input type="submit" value="{{ __('Save Changes') }}" class="btn  btn-primary">
    </div>

</form>

<script>
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

$(function () {
            var start = moment('{{$project->start_date}}', 'YYYY-MM-DD HH:mm:ss');
            var end = moment('{{$project->end_date}}', 'YYYY-MM-DD HH:mm:ss');

            function cb(start, end) {
                $("form #duration").val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
                $('form input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm:ss'));
                $('form input[name="end_date"]').val(end.format('YYYY-MM-DD HH:mm:ss'));
            }

            $('form #duration').daterangepicker({
                /*autoApply: true,
                autoclose: true,*/
                timePicker: true,
                autoUpdateInput: false,
                startDate: start,
                endDate: end,
                /*startDate: start,
                endDate: end,
                autoApply: true,
                autoclose: true,
                autoUpdateInput: false,*/
                locale: {
                    format: 'MMMM D, YYYY hh:mm A',
                    applyLabel: "{{__('Apply')}}",
                    cancelLabel: "{{__('Cancel')}}",
                    fromLabel: "{{__('From')}}",
                    toLabel: "{{__('To')}}",
                    daysOfWeek: [
                        "{{__('Sun')}}",
                        "{{__('Mon')}}",
                        "{{__('Tue')}}",
                        "{{__('Wed')}}",
                        "{{__('Thu')}}",
                        "{{__('Fri')}}",
                        "{{__('Sat')}}"
                    ],
                    monthNames: [
                        "{{__('January')}}",
                        "{{__('February')}}",
                        "{{__('March')}}",
                        "{{__('April')}}",
                        "{{__('May')}}",
                        "{{__('June')}}",
                        "{{__('July')}}",
                        "{{__('August')}}",
                        "{{__('September')}}",
                        "{{__('October')}}",
                        "{{__('November')}}",
                        "{{__('December')}}"
                    ],
                }
            }, cb);

            cb(start, end);
        });
</script>
<script>
$(document).on('change', "select[name=project_id]", function () {
    $.get('@auth('web'){{route('home')}}@elseauth{{route('client.home')}}@endauth' + '/userProjectJson/' + $(this).val(), function (data) {
        $('select[name=assign_to]').html('');
        data = JSON.parse(data);
        $(data).each(function (i, d) {
            $('select[name=assign_to]').append('<option value="' + d.id + '">' + d.name + ' - ' + d.email + '</option>');
        });
    });
    $.get('@auth('web'){{route('home')}}@elseauth{{route('client.home')}}@endauth' + '/projectMilestoneJson/' + $(this).val(), function (data) {
        $('select[name=milestone_id]').html('<option value="">{{__('Select Milestone')}}</option>');
        data = JSON.parse(data);
        $(data).each(function (i, d) {
            $('select[name=milestone_id]').append('<option value="' + d.id + '">' + d.title + '</option>');
        });
    });
})
</script>

<script>
var inputElement = document.querySelector('#tag-project-edit')
new Tagify(inputElement)
</script>

<link rel="stylesheet" href="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.css') }}">

<script src="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script>
