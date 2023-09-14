<header class="shadow sticky top-0 bg-white z-20">
    <div class="container">
        <div class="pt-4 lg:mt-0 pb-4 lg:pb-5 flex items-center justify-between">
            <div>
                <a href="#">
                    <img class="lg:-mt-5" src={{asset("custom-auth/uploads/logo/logo/20230320172321.png")}} alt="" />
                </a>
            </div>
            <div class="hidden lg:block">
                <ul class="p-0 m-0 w-full items-center flex gap-4">
                    <li class="relative">
                        <a href="#" class="text-sm xl:text-base font-medium px-1  text-sec ">Home</a>
                    </li>
                    <li class="relative">
                        <a href="#" class="text-sm xl:text-base font-medium px-1  text-sec ">FAQ’s</a>
                    </li>
                    <li class="relative">
                        <a href="#" class="text-sm xl:text-base font-medium px-1  text-sec ">Contact Us</a>
                    </li>
                    <li class="relative">
                        <div class="dropdown relative w-full rounded-xl">
                            <button
                                class="dropdown-toggle bg-transparent text-sec text-sm xl:text-base leading-tight focus:outline-none focus:ring-0 transition duration-150 ease-in-out flex items-center font-medium gap-1 justify-between w-full whitespace-nowrap capitalize"
                                type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <span id="dropdown1-text">English</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="3" stroke="#9E9E9E" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <ul id="dropdown1"
                                class="dropdown-menu min-w-max absolute hidden bg-white text-base z-50 float-left left-0 -ml-4 py-2 list-none text-left rounded-lg w-full shadow-lg mt-1 bg-clip-padding border-none"
                                aria-labelledby="dropdownMenuButton1">
                                <li class="w-full">
                                    <a href=""
                                        class="dropdown-item text-sm py-2 px-4 font-normal inline-block w-full whitespace-nowrap bg-transparent text-gray-700 hover:bg-gray-100 capitalize">
                                        English
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href=""
                                        class="dropdown-item text-sm py-2 px-4 font-normal inline-block w-full whitespace-nowrap bg-transparent text-gray-700 hover:bg-gray-100 capitalize">
                                        العربية
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="relative">
                        <a href="" class="bg-pri py-3 px-4 rounded-xl text-xs xl:text-base text-white font-medium">
                            <img class="inline px-1"
                                src={{asset("custom-auth/assets/images/ticket.svg")}} alt="" />Submit
                            Ticket</a>
                    </li>
                    <li class="relative">
                        <a href="{{route('register')}}"
                            class="text-pri text-sm xl:text-base font-medium pl-3 border-l py-3">Register</a>
                    </li>
                    <li class="relative">
                        <a href="{{route('login')}}" class="text-pri text-sm xl:text-base font-medium">Login</a>
                    </li>
                </ul>
            </div>
            <!-- HAMBURGER -->
            <div type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
                aria-controls="offcanvasExample" class="flex lg:hidden flex-col gap-1 cursor-pointer pl-2S">
                <span class="bg-pri rounded h-1 block w-6"></span>
                <span class="bg-pri rounded h-1 block w-6"></span>
                <span class="bg-pri rounded h-1 block w-6"></span>
            </div>
        </div>
    </div>
    <div class="flex space-x-2">
        <div>
            <div class="offcanvas offcanvas-start fixed bottom-0 flex flex-col max-w-full bg-white invisible bg-clip-padding shadow-sm outline-none transition duration-300 ease-in-out text-gray-700 top-0 left-0 border-none w-96"
                tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                <div class="offcanvas-header flex items-center justify-end p-4">
                    <button type="button"
                        class="btn-close mt-1 box-content w-4 h-4 p-2 -my-5 -mr-2 text-black border-none rounded-xl opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
                        data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body flex-grow p-4 overflow-y-auto">
                    <div>
                        <ul class="p-0 m-0 flex flex-col gap-5">
                            <li>
                                <a href="#" class="text-sm xl:text-base font-medium  text-sec ">Home</a>
                            </li>
                            <li>
                                <a href="#" class="text-sm xl:text-base font-medium  text-sec ">FAQ’s</a>
                            </li>
                            <li>
                                <a href="#" class="text-sm xl:text-base font-medium  text-sec ">Contact Us</a>
                            </li>
                            <li>
                                <div class="dropdown max-w-[70px] relative w-full rounded-xl">
                                    <button
                                        class="dropdown-toggle bg-transparent text-sec text-sm xl:text-base leading-tight focus:outline-none focus:ring-0 transition duration-150 ease-in-out flex items-center font-medium gap-1 justify-between w-full whitespace-nowrap capitalize"
                                        type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <span id="dropdown2-text">English</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="3" stroke="#9E9E9E" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                    <ul id="dropdown2"
                                        class="dropdown-menu min-w-max absolute hidden bg-white text-base z-50 float-left left-0 -ml-4 py-2 list-none text-left rounded-lg w-full shadow-lg mt-1 bg-clip-padding border-none capitalize"
                                        aria-labelledby="dropdownMenuButton1">
                                        <li class="w-full">
                                            <a href="#"
                                                class="dropdown-item text-sm py-2 px-4 font-normal inline-block w-full whitespace-nowrap bg-transparent text-gray-700 hover:bg-gray-100">
                                                English
                                            </a>
                                        </li>
                                        <li class="w-full">
                                            <a href="#"
                                                class="dropdown-item text-sm py-2 px-4 font-normal inline-block w-full whitespace-nowrap bg-transparent text-gray-700 hover:bg-gray-100">
                                                العربية
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="{{route('register')}}"
                                    class="text-pri text-sm xl:text-base font-medium py-3">Register</a>
                            </li>
                            <li>
                                <a href="{{route('login')}}" class="text-pri text-sm xl:text-base font-medium">Login</a>
                            </li>
                            <li>
                                <a href="#"
                                    class="bg-pri py-3 px-4 rounded-xl text-xs xl:text-base text-white font-medium">
                                    <img class="inline px-1"
                                        src="../assets/images/ticket.svg"
                                        alt="" />Submit Ticket</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
