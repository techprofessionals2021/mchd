<form class="" method="post" action="{{ route('projects.store',$currentWorkspace->slug) }}" onkeydown="return event.key != 'Enter';">
    @csrf
     <div class="modal-body">
    <div class="row">
        @if ($currentWorkspace->is_chagpt_enable())
        <div class="text-end col-12">
            <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary" data-url="{{ route('generate',['project']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate with AI') }}" data-title="{{ __('Generate Project Name & Description') }}">
                <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}</a>
        </div>
        @endif
        <div class="form-group col-md-12">
            <label for="projectname" class="col-form-label">{{ __('Name') }}</label>
            <input class="form-control" type="text" id="projectname" name="name" required="" placeholder="{{ __('Project Name') }}">
        </div>
        <div class="form-group col-md-12">
            <label for="description" class="col-form-label">{{ __('Description') }}</label>
            <textarea rows="3" class="form-control" id="description" name="description" required="" placeholder="{{ __('Add Description') }}"></textarea>
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
        <div class="col-md-12">
            <label for="users_list" class="col-form-label">{{ __('Users') }}</label>
            <select class=" multi-select" id="users_list" name="users_list[]" data-toggle="select2" multiple="multiple" data-placeholder="{{ __('Select Users ...') }}">
                @foreach($currentWorkspace->users($currentWorkspace->created_by) as $user)
                    <option value="{{$user->email}}">{{$user->name}} - {{$user->email}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-12">
            <label for="tags" class="col-form-label">{{ __('Tags') }}</label>
            <input type="text" name="tags" class="tags form-control" value="{{ old('tags') }}"
               data-role="tagsinput" />
            @if($errors->has('tags'))
            <strong class="text-danger">{{ $errors->first('tags') }}</strong>
            @endif
         </div>

    </div>
</div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close')}}</button>
            <input type="submit" value="{{ __('Add New project')}}" class="btn  btn-primary">
        </div>

</form>
{{-- tagsInput --}}
{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}
<!-- Bootstrap Tags Input CDN -->
{{-- <link rel='stylesheet'
   href='https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css'> --}}



   {{--  --}}


   {{-- <script src='https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js'></script>

   <script>
      $(function () {
         $('input').on('change', function (event) {

            var $element = $(event.target);
            var $container = $element.closest('.example');

            if (!$element.data('tagsinput'))
               return;

            var val = $element.val();
            if (val === null)
               val = "null";
            var items = $element.tagsinput('items');

            $('code', $('pre.val', $container)).html(($.isArray(val) ? JSON.stringify(val) : "\"" + val.replace('"', '\\"') + "\""));
            $('code', $('pre.items', $container)).html(JSON.stringify($element.tagsinput('items')));


         }).trigger('change');
      });
   </script> --}}


{{-- tagsInput --}}

<link rel="stylesheet" href="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.css') }}">
{{-- <style>
    .bootstrap-tagsinput {
    background-color: #fff;
    border: 1px solid #ccc;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    display: inline-block;
    padding: 10px 6px;
    color: #555;
    vertical-align: middle;
    border-radius: 9px;
    max-width: 100%;
    line-height: 22px;
    cursor: text;
}
</style> --}}
<script src="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
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
       var start = moment('{{ date('Y-m-d') }}', 'YYYY-MM-DD HH:mm:ss');
       var end = moment('{{ date('Y-m-d') }}', 'YYYY-MM-DD HH:mm:ss');

       function cb(start, end) {
           $("form #duration").val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
           $('form input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm:ss'));
           $('form input[name="end_date"]').val(end.format('YYYY-MM-DD HH:mm:ss'));
       }

       $('form #duration').daterangepicker({
           /*autoApply: true,
           autoclose: true,*/
           autoApply: true,
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
       })
   })
</script>


<script>
    var inputElement = document.querySelector('.tags')
    new Tagify(inputElement)
</script>
