@extends('layouts.admin')

@section('page-title') {{__(' Reports')}} @endsection
@section('links')
@if(\Auth::guard('client')->check())   
<li class="breadcrumb-item"><a href="{{route('client.home')}}">{{__('Home')}}</a></li>
 @else
 <li class="breadcrumb-item"><a href="{{route('home')}}">{{__('Home')}}</a></li>
 @endif
<li class="breadcrumb-item"> {{ __('Reports') }}</li>
@endsection
@section('action-button')
    @auth('web')
  
      

    @endauth
@endsection

@section('content')

    @if($currentWorkspace)

        <section class="section">

            <section id="tabs" class="project-tab">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <nav>
                                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">All Executives</a>
                                    {{-- <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">All Hods</a>
                                    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">All Projects</a> --}}
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">  
                                    <table class="table" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Project Name</th>
                                                <th>Employer</th>
                                                <th>Awards</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="#">Work 1</a></td>
                                                <td>Doe</td>
                                                <td>john@example.com</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Work 2</a></td>
                                                <td>Moe</td>
                                                <td>mary@example.com</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Work 3</a></td>
                                                <td>Dooley</td>
                                                <td>july@example.com</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                    <table class="table" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Project Name</th>
                                                <th>Employer</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="#">Work 1</a></td>
                                                <td>Doe</td>
                                                <td>john@example.com</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Work 2</a></td>
                                                <td>Moe</td>
                                                <td>mary@example.com</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Work 3</a></td>
                                                <td>Dooley</td>
                                                <td>july@example.com</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                                    <table class="table" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Contest Name</th>
                                                <th>Date</th>
                                                <th>Award Position</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="#">Work 1</a></td>
                                                <td>Doe</td>
                                                <td>john@example.com</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Work 2</a></td>
                                                <td>Moe</td>
                                                <td>mary@example.com</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Work 3</a></td>
                                                <td>Dooley</td>
                                                <td>july@example.com</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
    
        </section>

      
     
    @else
        <div class="container mt-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="page-error">
                        <div class="page-inner">
                            <h1>404</h1>
                            <div class="page-description">
                                {{ __('Page Not Found') }}
                            </div>
                            <div class="page-search">
                                <p class="text-muted mt-3">{{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.")}}</p>
                                <div class="mt-3">
                                    <a class="btn-return-home badge-blue" href="{{route('home')}}"><i class="fas fa-reply"></i> {{ __('Return Home')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(document).on('click', 'input[name="type"]', function () {
            var type = $('input[name="type"]:checked').val();

            if (type == 'shared') {
                $('.assign_to_selection').show();
            } else {
                $('.assign_to_selection').hide();
            }
        });


     
    </script>
@endpush
