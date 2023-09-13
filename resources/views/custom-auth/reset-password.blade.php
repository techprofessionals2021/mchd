@extends('layouts.custom-auth.app')

@section('content')
<main>
    <section>
        <div class="bg-[#fafafa] w-full mb-16 container relative py-32 lg:py-20 mt-10 lg:mt-20">
            <div
                class="w-full max-w-[500px] py-12 px-5 lg:px-7 mx-auto shadow-[0px_6px_30px_20px_rgba(0,0,0,0.03)] bg-white rounded-xl">
                <h2 class="text-[#0A0A0A] text-2xl font-semibold">Reset Password</h2>
                <p class="text-[#404040] mt-7 mb-9">Create your new Password</p>
                <form method="" action="">
                    <!-- this code is commented by ahmed -->
                    <!-- <input type="hidden" name="_token" value="FpHdcCilGNMjipeMcL4uJrmvKcZwPE0cjcJDsSq1"> <input
                        type="hidden" name="_method" value="post"> -->
                    <div id="my_name_6aC5S9cvisqrP6j7_wrap" style="display: none" aria-hidden="true">
                        <input id="my_name_6aC5S9cvisqrP6j7" name="" type="text" value="" autocomplete="nope"
                            tabindex="-1">
                        <input name="valid_from" type="text" value="" autocomplete="nope" tabindex="-1">
                    </div>
                    <!-- <p class="text-[#616161] font-semibold mb-2 mt-5">First Name
                    </p>
                    <input placeholder="Enter your First Name" name="firstname" value=""
                        class="py-2 px-3 rounded-xl border border-gray-200 w-full placeholder:text-[#9E9E9E]"
                        type="text" />
                    <p class="text-[#616161] font-semibold mb-2 mt-5">Last Name
                    </p>
                    <input placeholder="Enter your Last Name" name="lastname" value=""
                        class="py-2 px-3 rounded-xl border border-gray-200 w-full placeholder:text-[#9E9E9E]"
                        type="text" /> -->
                    <p class="text-[#616161] font-semibold mb-2 mt-5">
                        Email
                    </p>
                    <input placeholder="Enter your Email Address" name="email" value=""
                        class="py-2 px-3 rounded-xl border border-gray-200 w-full placeholder:text-[#9E9E9E]"
                        type="Email" />
                    <!-- VALIDATION ERROR -->
                    <p class="text-[#616161] font-semibold mb-2 mt-5">Password
                    </p>
                    <div class="relative">
                        <input id="password" placeholder="Enter password" name="password"
                            class="py-2 password px-3 rounded-xl border border-gray-200 w-full placeholder:text-[#9E9E9E]"
                            type="Password" />
                        <span class="absolute toggle-pw right-5 top-[50%] translate-y-[-50%] cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="10" viewBox="0 0 16 10"
                                fill="none">
                                <path
                                    d="M7.99935 1.33333C9.22517 1.32926 10.4273 1.67119 11.4674 2.31982C12.5076 2.96845 13.3436 3.89744 13.8793 5C12.7793 7.24667 10.5327 8.66667 7.99935 8.66667C5.46602 8.66667 3.21935 7.24667 2.11935 5C2.65509 3.89744 3.4911 2.96845 4.53127 2.31982C5.57143 1.67119 6.77353 1.32926 7.99935 1.33333ZM7.99935 0C4.66602 0 1.81935 2.07333 0.666016 5C1.81935 7.92667 4.66602 10 7.99935 10C11.3327 10 14.1793 7.92667 15.3327 5C14.1793 2.07333 11.3327 0 7.99935 0ZM7.99935 3.33333C8.44138 3.33333 8.8653 3.50893 9.17786 3.82149C9.49042 4.13405 9.66602 4.55797 9.66602 5C9.66602 5.44203 9.49042 5.86595 9.17786 6.17851C8.8653 6.49107 8.44138 6.66667 7.99935 6.66667C7.55732 6.66667 7.1334 6.49107 6.82084 6.17851C6.50828 5.86595 6.33268 5.44203 6.33268 5C6.33268 4.55797 6.50828 4.13405 6.82084 3.82149C7.1334 3.50893 7.55732 3.33333 7.99935 3.33333ZM7.99935 2C6.34602 2 4.99935 3.34667 4.99935 5C4.99935 6.65333 6.34602 8 7.99935 8C9.65268 8 10.9993 6.65333 10.9993 5C10.9993 3.34667 9.65268 2 7.99935 2Z"
                                    fill="#404040"></path>
                            </svg>
                        </span>
                    </div>
                    <!-- VALIDATION ERROR -->
                    <p class="text-[#616161] font-semibold mb-2 mt-5">
                        Confirm Password
                    </p>
                    <div class="relative">
                        <input id="password" placeholder="Confirm password" name="password_confirmation"
                            class="py-2 password px-3 rounded-xl border border-gray-200 w-full placeholder:text-[#9E9E9E]"
                            type="Password" />
                        <span class="absolute toggle-pw right-5 top-[50%] translate-y-[-50%] cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="10" viewBox="0 0 16 10"
                                fill="none">
                                <path
                                    d="M7.99935 1.33333C9.22517 1.32926 10.4273 1.67119 11.4674 2.31982C12.5076 2.96845 13.3436 3.89744 13.8793 5C12.7793 7.24667 10.5327 8.66667 7.99935 8.66667C5.46602 8.66667 3.21935 7.24667 2.11935 5C2.65509 3.89744 3.4911 2.96845 4.53127 2.31982C5.57143 1.67119 6.77353 1.32926 7.99935 1.33333ZM7.99935 0C4.66602 0 1.81935 2.07333 0.666016 5C1.81935 7.92667 4.66602 10 7.99935 10C11.3327 10 14.1793 7.92667 15.3327 5C14.1793 2.07333 11.3327 0 7.99935 0ZM7.99935 3.33333C8.44138 3.33333 8.8653 3.50893 9.17786 3.82149C9.49042 4.13405 9.66602 4.55797 9.66602 5C9.66602 5.44203 9.49042 5.86595 9.17786 6.17851C8.8653 6.49107 8.44138 6.66667 7.99935 6.66667C7.55732 6.66667 7.1334 6.49107 6.82084 6.17851C6.50828 5.86595 6.33268 5.44203 6.33268 5C6.33268 4.55797 6.50828 4.13405 6.82084 3.82149C7.1334 3.50893 7.55732 3.33333 7.99935 3.33333ZM7.99935 2C6.34602 2 4.99935 3.34667 4.99935 5C4.99935 6.65333 6.34602 8 7.99935 8C9.65268 8 10.9993 6.65333 10.9993 5C10.9993 3.34667 9.65268 2 7.99935 2Z"
                                    fill="#404040"></path>
                            </svg>
                        </span>
                    </div>
                    <!-- VALIDATION ERROR -->

                    <!-- <label data-mdb-ripple="true" data-mdb-ripple-color="#a8a8a8" data-mdb-ripple-duration="1000ms"
                        class="text-[#616161] relative mt-1 py-2 pb-3 pt-0 block text-sm">
                        <input type="checkbox" class="accent-red-500 mt-4 mr-3" value="agreed" name="agree_terms" />
                        I have read and agree to the
                        <a href="#" class="text-pri font-medium">Terms of services</a></label> -->
                    <button data-mdb-ripple="true" data-mdb-ripple-color="dark" data-mdb-ripple-duration="1000ms"
                        type="submit"
                        class="bg-pri hover:bg-blue-600 relative transition-all block w-full text-white font-medium py-3 px-5 rounded-xl mt-6 text-sm">
                        Reset Password
                    </button>
                    <!-- <a data-mdb-ripple="true" data-mdb-ripple-color="#cdcdcd" data-mdb-ripple-duration="1000ms"
                        href="forgotpassword.html"
                        class="text-pri block justify-center relative mx-auto text-sm font-medium hover:bg-gray-100 mt-1 transition-all rounded-xl text-center pt-3 py-3">Forgot
                        Password?</a> -->
                    <p class="text-gray-500 text-center pt-5 border-t mt-9">
                        Remember your password?
                        <a href="login.html" class="text-pri font-bold inline">Login</a>
                    </p>
                </form>
            </div>

            <img class="absolute left-0 top-0" src={{asset("custom-auth/assets/images/left.svg")}} alt="" />
            <img class="absolute right-0 bottom-0" src={{asset("custom-auth/assets/images/right.svg")}} alt="" />
        </div>
    </section>
</main>
@endsection
