<!DOCTYPE html>
<html lang="en">


<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="Looking For help?" name="description">
    <meta content="My Desk" name="author">
    <meta name="keywords" content="Why Choose US ?" />
    <meta name="csrf-token" content="FpHdcCilGNMjipeMcL4uJrmvKcZwPE0cjcJDsSq1">
    <title>MCHD</title>
    <!--Favicon -->
    <link rel="icon" href="../uploads/logo/favicons/20230307105941.png" type="image/x-icon" />



    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('custom-auth/new/css/slick.css') }}" />
    <link rel="stylesheet" href="{{ asset('custom-auth/new/css/slick-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('custom-auth/new/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('custom-auth/new/css/output.css') }}" />
    <style>
        :root {
            --primary: rgba(0, 74, 171, 0.9);
            --secondary: rgba(0, 74, 171, 1);
        }
    </style>
    <style>
        .cookie-consent {
            display: none;
        }
    </style>

    <!-- Google Fonts -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&amp;display=swap');
    </style>
    <script src={{ asset('custom-auth/assets/plugins/jquery/jquery.min5696.js?v=1694538437') }}></script>
</head>

<body class="">
    @include('partials.custom-auth.header')



    @yield('content')

    {{-- <div style="z-index: 1000" id="notify-cookies"
        class="w-full max-w-[330px] fixed bottom-0 bg-white mb-3 right-3 sm:right-7 sm:mb-8 sm:max-w-[370px] py-7 px-8 shadow-[0px_6px_30px_20px_rgba(0,0,0,0.03)] rounded-xl ltr">
        <h2 class="text-[#404040] font-semibold mb-5">
            We Care About Your Privacy
        </h2>
        <p class="pr-2 mb-4 text-[#616161]">
            Your Experience on this site will be improved by allowing cookies.
        </p>
        <button id="accept-cookies" data-mdb-ripple="true" data-mdb-ripple-color="#cdcdcd"
            data-mdb-ripple-duration="800ms" class="px-5 py-2 text-white bg-pri rounded-md text-sm">
            Accept
        </button>
    </div> --}}

    @include('partials.custom-auth.footer')

    <script src={{ asset('custom-auth/new/js/language-switch.js') }}></script>
    <script defer src={{ asset('custom-auth/new/js/index.min.js') }}></script>
    <script defer src={{ asset('custom-auth/new/js/slick.min.js') }}></script>
    <script defer src={{ asset('custom-auth/new/js/main.js') }}></script>
    <script defer>
        const togglePw = document.querySelector(".toggle-pw");
        const pwInput = document.querySelector("#password");

        togglePw.addEventListener("click", () => {
            pwInput.type === "password" ?
                (pwInput.type = "text") :
                (pwInput.type = "password");
        });
    </script>
    <script>
        // Custom js
        // IF COOKIES ARE ALREADY ACCEPTED, THEN THE NOTIFICATION WONT BE DISPLAYED
        if (localStorage.getItem("@_")) {
            document.getElementById("notify-cookies").style.display = "none";
        }

        // ACCEPT COOKIES
        document.getElementById("accept-cookies").addEventListener("click", () => {
            document.getElementById("notify-cookies").style.display = "none";
            localStorage.setItem("@_", true);
        });

        function handleDropdown(dropdownId) {
            const dropDownText = document.getElementById(`${dropdownId}-text`);
            const dropDownOptions = document.querySelectorAll(
                `#${dropdownId} .dropdown-item`
            );
            dropDownOptions?.forEach((item, index) => {
                item.addEventListener("click", (e) => {
                    dropDownText.textContent = dropDownOptions[index].textContent;
                });
            });
        }

        // Call the function for each dropdown
        handleDropdown("dropdown1");
        handleDropdown("dropdown2");
        // handleDropdown("dropdown3");
        $(document).ready(function() {})
    </script>
    <div class="theme-white js-cookie-consent cookie-consent">
        <div class="cookie-popup position-bottom hidden bg-white" style="display: block;">
            <div class="cookie-popup-inner p-5">
                <div class="cookie-popup-left">
                    <svg class="cookie-sprukoimage" xmlns="http://www.w3.org/2000/svg" width="80" height="80"
                        viewBox="0 0 24 24">
                        <path fill="#FAA54E"
                            d="M20.489 3.517C18.22 1.249 15.207 0 11.999 0S5.779 1.249 3.51 3.517c-4.68 4.68-4.68 12.297 0 16.978A11.88 11.88 0 0 0 11.95 24c.97 0 1.953-.11 2.929-.352l.752-.185-.478-.609a2.963 2.963 0 0 1-.652-1.844c0-.62.194-1.22.561-1.737l.14-.197-.067-.233a2.933 2.933 0 0 1-.134-.834 3.005 3.005 0 0 1 3.002-3.002c.253 0 .52.043.835.135l.232.067.197-.14a2.991 2.991 0 0 1 3.58.09l.61.48.185-.753c1.017-4.128-.162-8.378-3.153-11.37z" />
                        <path
                            d="M14.916 23.398a12.52 12.52 0 0 1-2.967.352 12.113 12.113 0 0 1-10.622-6.244 11.971 11.971 0 0 0 2.183 2.989A11.88 11.88 0 0 0 11.95 24c.97 0 1.953-.11 2.929-.352l.752-.185-.157-.2-.558.135z"
                            opacity=".05" />
                        <path fill="#FFF"
                            d="M3.51 3.767C5.778 1.499 8.793.25 12 .25s6.221 1.249 8.489 3.517A11.894 11.894 0 0 1 24 11.94a11.89 11.89 0 0 0-3.511-8.424C18.22 1.249 15.207 0 11.999 0S5.779 1.249 3.51 3.517A11.97 11.97 0 0 0 0 12.13a11.966 11.966 0 0 1 3.51-8.364z"
                            opacity=".2" />
                        <circle cx="3.994" cy="9.011" r="1" fill="#8F3E0F" />
                        <circle cx="9.5" cy="6.506" r="1.494" fill="#8F3E0F" />
                        <circle cx="15.994" cy="7" r="2" fill="#8F3E0F" />
                        <circle cx="19.994" cy="10.011" r="1" fill="#8F3E0F" />
                        <circle cx="8.494" cy="12.5" r="2.5" fill="#8F3E0F" />
                        <circle cx="9.994" cy="18" r="1" fill="#8F3E0F" />
                        <linearGradient id="a" x1="339.374" x2="340.493" y1="-231.621" y2="-230.502"
                            gradientTransform="rotate(180 172 -110.989)" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-opacity=".1" />
                            <stop offset="1" stop-opacity="0" />
                        </linearGradient>
                        <circle cx="3.994" cy="9.011" r="1" fill="url(#a)" />
                        <linearGradient id="b" x1="344.567" x2="346.24" y1="-234.439" y2="-232.767"
                            gradientTransform="rotate(180 177.506 -113.494)" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-opacity=".1" />
                            <stop offset="1" stop-opacity="0" />
                        </linearGradient>
                        <circle cx="9.5" cy="6.505" r="1.494" fill="url(#b)" />
                        <linearGradient id="c" x1="350.742" x2="352.98" y1="-234.264" y2="-232.026"
                            gradientTransform="rotate(180 184 -113)" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-opacity=".1" />
                            <stop offset="1" stop-opacity="0" />
                        </linearGradient>
                        <circle cx="15.994" cy="7" r="2" fill="url(#c)" />
                        <linearGradient id="d" x1="355.374" x2="356.493" y1="-230.621" y2="-229.502"
                            gradientTransform="rotate(180 188 -109.989)" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-opacity=".1" />
                            <stop offset="1" stop-opacity="0" />
                        </linearGradient>
                        <circle cx="19.994" cy="10.011" r="1" fill="url(#d)" />
                        <linearGradient id="e" x1="342.926" x2="345.724" y1="-229.08" y2="-226.283"
                            gradientTransform="rotate(180 176.5 -107.5)" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-opacity=".1" />
                            <stop offset="1" stop-opacity="0" />
                        </linearGradient>
                        <circle cx="8.494" cy="12.5" r="2.5" fill="url(#e)" />
                        <linearGradient id="f" x1="345.374" x2="346.493" y1="-222.632" y2="-221.513"
                            gradientTransform="rotate(180 178 -102)" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-opacity=".1" />
                            <stop offset="1" stop-opacity="0" />
                        </linearGradient>
                        <circle cx="9.994" cy="18" r="1" fill="url(#f)" />
                        <linearGradient id="g" x1="1.119" x2="22.802" y1="6.931" y2="17.042"
                            gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-color="#FFF" stop-opacity=".2" />
                            <stop offset="1" stop-color="#FFF" stop-opacity="0" />
                        </linearGradient>
                        <path fill="url(#g)"
                            d="M20.489 3.517C18.22 1.249 15.207 0 11.999 0S5.779 1.249 3.51 3.517c-4.68 4.68-4.68 12.297 0 16.978A11.88 11.88 0 0 0 11.95 24c.97 0 1.953-.11 2.929-.352l.752-.185-.478-.609a2.963 2.963 0 0 1-.652-1.844c0-.62.194-1.22.561-1.737l.14-.197-.067-.233a2.933 2.933 0 0 1-.134-.834 3.005 3.005 0 0 1 3.002-3.002c.253 0 .52.043.835.135l.232.067.197-.14a2.991 2.991 0 0 1 3.58.09l.61.48.185-.753c1.017-4.128-.162-8.378-3.153-11.37z" />
                    </svg>
                    <div class="cookie-popup-headline">We Care about your privacy </div>
                    <div class="cookie-popup-sub-headline ">
                        Your experience on this site will be improved by allowing cookies.
                    </div>
                </div>
                <div class="cookie-popup-right">
                    <a href="#"
                        class="cookie-popup-accept-cookies js-cookie-consent-agree cookie-consent__agree cursor-pointer d-block">Accept</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.laravelCookieConsent = (function() {

            const COOKIE_VALUE = 1;
            const COOKIE_DOMAIN = 'helpdesk.mchd-manager.com';

            function consentWithCookies() {
                setCookie('laravel_cookie_consent', COOKIE_VALUE, '7300');
                hideCookieDialog();
            }

            function cookieExists(name) {
                return (document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1);
            }

            function hideCookieDialog() {
                const dialogs = document.getElementsByClassName('js-cookie-consent');

                for (let i = 0; i < dialogs.length; ++i) {
                    dialogs[i].style.display = 'none';
                }
            }

            function setCookie(name, value, expirationInDays) {
                const date = new Date();
                date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + value +
                    ';expires=' + date.toUTCString() +
                    ';domain=' + COOKIE_DOMAIN +
                    ';path=/' +
                    ';samesite=lax';
            }

            if (cookieExists('laravel_cookie_consent')) {
                hideCookieDialog();
            }

            const buttons = document.getElementsByClassName('js-cookie-consent-agree');

            for (let i = 0; i < buttons.length; ++i) {
                buttons[i].addEventListener('click', consentWithCookies);
            }

            return {
                consentWithCookies: consentWithCookies,
                hideCookieDialog: hideCookieDialog
            };
        })();
    </script>

</body>



</html>
