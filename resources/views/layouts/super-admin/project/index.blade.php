@extends('layouts.super-admin.super-admin')

@section('page-title')
    {{ __('Projects') }}
@endsection
@section('links')

    <li class="breadcrumb-item"> {{ __('Projects') }}</li>
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
            <div class="row">
                <div class="col-md-12 mt-2">

                    <div class="table-responsive">
                        <table class="table table-bordered data-table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    {{-- <th>Actions</th> --}}


                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($project as $item)
                                <tr>

                                <td>{{$item->id}} </td>
                                <td>{{$item->name}} </td>
                                <td>{{$item->status}} </td>
                                <td>{{$item->description}} </td>
                                {{-- <td> --}}
                                    {{-- <a href="#" class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center"  data-toggle="popover"  title="' . __('Edit Task') . '"  data-ajax-popup="true" data-size="lg" data-title="' . __('Edit Task') . '" data-url="' . route(
                                        'tasks.edit',
                                        [
                                            $currentWorkspace->slug,
                                            $task->project_id,
                                            $task->id,
                                        ]
                                    ) . '"><i class="ti ti-pencil"></i></a> --}}

                                    {{-- <a href="#" class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center" data-ajax-popup="true" data-size="lg" data-title="{{ __('Edit Project') }}" data-url="{{route('projects.edit',[@$item->workspaceData->slug ?? 0 ,$item->id])}}">
                                        <i class="ti ti-pencil"></i>
                                     </a>

                                    <a href="#" class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center bs-pass-para" data-toggle="popover" title="' . __('Delete') . '" data-confirm="' . __('Are You Sure?') . '" data-confirm-yes="delete-form-' . $task->id . '">
                                        <i class="ti ti-trash"></i></a> --}}
                                {{-- </td> --}}


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
  </script>
@endpush
