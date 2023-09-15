@extends('layouts.custom-auth.app')

@section('content')
    <main>
        <section>
            <div class="bg-[#fafafa] w-full mb-16 container relative py-32 lg:py-20 mt-10 lg:mt-20">
                <div
                    class="w-full max-w-[500px] py-12 px-5 lg:px-7 mx-auto shadow-[0px_6px_30px_20px_rgba(0,0,0,0.03)] bg-white rounded-xl">
                    <h2 class="text-[#0A0A0A] text-2xl font-semibold">Forgot Password</h2>
                    <p class="text-[#404040] mt-7 mb-9">Enter the email address that is linked to your account.</p>
                    @if (session('status'))
                     <div class="alert alert-success" role="alert">
                         {{ session('status') }}
                     </div>
                    @endif
                    <form action="{{route('password.email')}}" method="POST">
                        @csrf
                        <!-- this code is commented by ahmad -->
                        <p class="text-[#616161] font-semibold mb-2 mt-5">
                            Email address
                        </p>
                        <div class="relative">
                            <input id="email" placeholder="Enter email" name="email"
                                class="py-2 px-3 rounded-xl border border-200 w-full placeholder:text-[#9E9E9E]"
                                type="email" />
                        </div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <!-- VALIDATION ERROR -->
                        <label data-mdb-ripple="true" data-mdb-ripple-color="#a8a8a8" data-mdb-ripple-duration="1100ms"
                            class="block relative text-[#616161] mt-1 pt-0 pb-3 text-sm">
                            <button data-mdb-ripple="true" data-mdb-ripple-color="dark" data-mdb-ripple-duration="1000ms"
                                type="submit"
                                class="bg-pri block hover:bg-blue-600 relative transition-all w-full text-white font-medium py-3 px-5 rounded-xl mt-6">
                                Submit
                            </button>
                            <!-- <a data-mdb-ripple="true" data-mdb-ripple-color="#cdcdcd" data-mdb-ripple-duration="1000ms"
                                   href="forgotpassword.html"
                                   class="text-pri relative hover:bg-gray-100 transition-all block justify-center mx-auto mt-1 font-medium text-center py-3 rounded-xl">Forgot
                                   Password?</a> -->
                            {{-- <p class="text-gray-500 text-center pt-5 border-t mt-9">
                                Not recieving otp?
                                <a href="#" class="text-pri font-bold inline">Send Me Back</a>
                            </p> --}}


                    </form>
                </div>

                <img class="absolute left-0 top-0" src={{asset("custom-auth/assets/images/left.svg")}} alt="" />
                <img class="absolute right-0 bottom-0" src={{asset("custom-auth/assets/images/right.svg")}} alt="" />
            </div>
        </section>
    </main>
@endsection
