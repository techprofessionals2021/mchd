{{-- <x-guest-layout>
    <x-auth-card> --}}
        @extends('layouts.guest')   
        @section('page-title')
            {{ __('Copylink') }}
        @endsection
        @section('content')
            <div class="card">
                <div class="row align-items-center text-start" style="margin-bottom: 120px !important">
                    <div class="col-xl-6">
                        <div class="card-body">
                            <div class="mb-3">
                                <h2 class="h3">{{ __('Password required') }}</h2>
                                <h6>{{ __('This document is password-protected. Please enter a password.') }}</h6>

                            </div>

                            <span class="clearfix"></span>
                            <form method="POST"  action="{{ route('projects.link', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($projectID)]) }}">
                                @csrf
                                <div class="form-group mb-2">
                                    <label class="form-control-label">{{ __('Password') }}</label>
                                    <div class="input-group input-group-merge">

                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <a href="#" data-toggle="password-text" data-target="#password">
                                                    <i class="fas fa-eye-slash" id="togglePassword"></i>
                                                </a>
                                            </span>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="btn-inner--text">{{ __('Save') }}</span>

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-xl-6 img-card-side">
                        <div class="auth-img-content">
                            <img src="{{ asset('assets/images/auth/img-auth-3.svg') }}" alt="" class="img-fluid">
                            <h3 class="text-white mb-4 mt-5">{{ __('“Attention is the new currency”') }}</h3>
                            <p class="text-white">
                                {{ __('The more effortless the writing looks, the more effort the writer actually put into the process.') }}
                            </p>
                        </div>
                    </div>

                    <div class="">


                        {{-- @section('language-bar')

                   

                    <div class="row mt-4">
                        <div class="">
                            @section('language-bar')
                            <a href="#" class="btn-primary">
                                <select name="language" id="language" class=" btn-primary btn " onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                                    @foreach (App\Models\Utility::languages() as $language)
                                    <option class="login_lang" @if ($lang == $language) selected @endif value="{{ route('verification.notice',$language) }}">{{Str::upper($language)}}</option>
                                @endforeach
                            </select>
                        </a>
                        @endsection
                    </div>
            </div>
                @endsection --}}
                    </div>
                </div>
            @endsection
            @push('custom-scripts')

            
                <script src="{{ asset('assets/custom/libs/jquery/dist/jquery.min.js') }}"></script>
                <script>
                    $(document).ready(function() {
                        $("#form_data").submit(function(e) {
                            $("#login_button").attr("disabled", true);
                            return true;
                        });
                    });
                </script>
                <script>
                    const togglePassword = document.querySelector("#togglePassword");
                    const password = document.querySelector("#password");
                    togglePassword.addEventListener("click", function () {
                        // toggle the type attribute
                        const type = password.getAttribute("type") === "password" ? "text" : "password";
                        password.setAttribute("type", type);
                        
                        // toggle the icon
                        this.classList.toggle("fa-eye");
                        this.classList.toggle("fa-eye-slash");
                    });
                   
                </script>
                @if (env('RECAPTCHA_MODULE') == 'on')
                    {!! NoCaptcha::renderJs() !!}
                @endif
            @endpush
    {{-- </x-auth-card>
</x-guest-layout> --}}

