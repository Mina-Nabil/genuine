<!DOCTYPE html>
<!-- Template Name: DashCode - HTML, React, Vue, Tailwind Admin Dashboard Template Author: Codeshaper Website: https://codeshaper.net Contact: support@codeshaperbd.net Like: https://www.facebook.com/Codeshaperbd Purchase: https://themeforest.net/item/dashcode-admin-dashboard-template/42600453 License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project. -->
<html lang="zxx" dir="ltr" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>Genuine {{ $page_title ?? '' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/genuine-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- BEGIN: Theme CSS-->
    {{--
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    --}}
    <link rel="stylesheet" href="{{ asset('assets/css/rt-plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    {{ $page_css_head ?? '' }}
    @yield('head_content')

    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />


    <!-- End : Theme CSS-->
    <script src="{{ asset('assets/js/settings.js') }}" sync></script>
    {{-- @auth
      
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            @env(['development', 'staging'])

                // Enable pusher logging - not included in prod
                Pusher.logToConsole = true;
            @endenv

            var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}"
            });

            var channel = pusher.subscribe('user{{ Auth::user()->id }}-channel');
            channel.bind('notifications-event', function(data) {
                console.log(data)
                Swal.fire({
                    title: data.message.title,
                    toast: true,
                    position: 'bottom-end',
                    timer: 7000,
                    text: data.message.message,
                    icon: 'info',
                    confirmButtonText: 'Ok',
                    showDenyButton: data.message.route != null,
                    denyButtonText: "Check now!",
                    denyButtonColor: "#5580dd",
                }).then((result) => {
                    if (result.isDenied) {
                        window.location.replace(data.message.route)
                    }
                });
            });
        </script>
    @endauth --}}

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
</head>

<body class=" font-inter dashcode-app">
    <!-- [if IE]> <p class="browserupgrade"> You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security. </p> <![endif] -->
    <main class="app-wrapper">
        <!-- BEGIN: Sidebar -->
        <!-- BEGIN: Sidebar -->
        <div class="sidebar-wrapper group">
            <div id="bodyOverlay"
                class="w-screen h-screen fixed top-0 bg-slate-900 bg-opacity-50 backdrop-blur-sm z-10 hidden"></div>
            <div class="logo-segment">
                <a class="flex items-center" href="{{ url('/') }}">
                    <img src="{{ asset('assets/images/logo/genuine-logo-wide.png') }}" class="black_logo"
                        alt="logo">
                    <img src="{{ asset('assets/images/logo/genuine-logo-wide-white.png') }}" class="white_logo"
                        alt="logo">
                    {{-- <span
                        class="ltr:ml-3 rtl:mr-3 text-xl font-Inter font-bold text-slate-900 dark:text-white">Wise
                        Ins.</span> --}}
                </a>
                <!-- Sidebar Type Button -->
                <div id="sidebar_type" class="cursor-pointer text-slate-900 dark:text-white text-lg">
                    <span class="sidebarDotIcon extend-icon cursor-pointer text-slate-900 dark:text-white text-2xl">
                        <div
                            class="h-4 w-4 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-full transition-all duration-150 ring-2 ring-inset ring-offset-4 ring-black-900 dark:ring-slate-400 bg-slate-900 dark:bg-slate-400 dark:ring-offset-slate-700">
                        </div>
                    </span>
                    <span class="sidebarDotIcon collapsed-icon cursor-pointer text-slate-900 dark:text-white text-2xl">
                        <div
                            class="h-4 w-4 border-[1.5px] border-slate-900 dark:border-slate-700 rounded-full transition-all duration-150">
                        </div>
                    </span>
                </div>
                <button class="sidebarCloseIcon text-2xl">
                    <iconify-icon class="text-slate-900 dark:text-slate-200" icon="clarity:window-close-line">
                    </iconify-icon>
                </button>
            </div>
            <div id="nav_shadow"
                class="nav_shadow h-[60px] absolute top-[80px] nav-shadow z-[1] w-full transition-all duration-200 pointer-events-none
      opacity-0">
            </div>
            <div class="sidebar-menus bg-white dark:bg-slate-800 py-2 px-4 h-[calc(100%-80px)] overflow-y-auto z-50"
                id="sidebar_menus">
                <ul class="sidebar-menu">
                    @can('viewReports', App\Models\Orders\Order::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? url('/dashboard'): '#' }}"
                                class="navItem {{ $dashboard ?? '' }} {{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class="nav-icon" icon="icon-park-outline:ad-product"></iconify-icon>
                                    <span>Dashboard</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    <li class="sidebar-menu-title">Orders</li>


                    <li>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? url('/orders'): '#' }}"
                            class="navItem {{ $orders ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? '': 'disabled' }}">
                            <span class="flex items-center">
                                <iconify-icon class="nav-icon" icon="icon-park-outline:ad-product"></iconify-icon>
                                <span>Active</span>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? url('/orders/closed'): '#' }}"
                            class="navItem {{ $ordersClosed ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? '': 'disabled' }}">
                            <span class="flex items-center">
                                <iconify-icon class="nav-icon" icon="lsicon:order-done-outline"></iconify-icon>
                                <span>Closed</span>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? url('/orders/pastdue'): '#' }}"
                            class="navItem {{ $ordersPastDue ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? '': 'disabled' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="lsicon:order-abnormal-outline">
                                </iconify-icon>
                                <span>Debit</span>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? url('/orders/cancelled'): '#' }}"
                            class="navItem {{ $ordersCancelled ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? '': 'disabled' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="lsicon:order-abnormal-outline">
                                </iconify-icon>
                                <span>Cancelled Orders</span>
                            </span>
                        </a>
                    </li>

                    <li class="sidebar-menu-title">Logistics</li>
                    <li>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Users\Driver::class)? route('orders.driver.shift'): '#' }}"
                            class="navItem {{ $driverShift ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Users\Driver::class)? '': 'disabled' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="iconoir:delivery-truck">
                                </iconify-icon>
                                <span>يوميه مندوب</span>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? url('/report/daily-loading'): '#' }}"
                            class="navItem {{ $dailyLoading ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Orders\Order::class)? '': 'disabled' }}">
                            <span class="flex
                            items-center">
                                <iconify-icon class=" nav-icon" icon="material-symbols:event">
                                </iconify-icon>
                                <span>يوميه تحميل</span>
                            </span>
                        </a>
                    </li>

                    @can('viewOrderInventory', App\Models\Products\Order::class)
                        <li class="sidebar-menu-title">Inventory</li>
                        <li>
                            <a href="{{ auth()->user()->can('viewOrderInventory', App\Models\Products\Order::class)? url('/orders/inventory'): '#' }}"
                                class="navItem {{ $ordersInventory ?? '' }} {{ auth()->user()->can('viewOrderInventory', App\Models\Orders\Order::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="gridicons:product">
                                    </iconify-icon>
                                    <span>تحضير</span>
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Products\Inventory::class)? url('/productions'): '#' }}"
                                class="navItem {{ $productions ?? '' }}
                            {{ auth()->user()->can('viewAny', App\Models\Products\Inventory::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="la:industry">
                                    </iconify-icon>
                                    <span>المخزون الفعلي</span>
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Products\Inventory::class)? url('/inventories'): '#' }}"
                                class="navItem {{ $inventories ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Products\Inventory::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="ic:sharp-inventory">
                                    </iconify-icon>
                                    <span>انتاجيه اليوم</span>
                                </span>
                            </a>
                        </li>

                        {{-- <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Products\Inventory::class)? url('/transactions'): '#' }}"
                                class="navItem {{ $Trans ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Products\Inventory::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="grommet-icons:transaction">
                                    </iconify-icon>
                                    <span>معاملات</span>
                                </span>
                            </a>
                        </li> --}}
                    @endcan

                    <li class="sidebar-menu-title">Reports</li>
                    @can('viewAny', App\Models\Products\Customer::class)
                        <li>
                            <a href="{{ url('/report/followup') }}" class="navItem {{ $followupReport ?? '' }}">
                                <span class="flex
                            items-center">
                                    <iconify-icon class=" nav-icon" icon="material-symbols:order-approve-outline-sharp">
                                    </iconify-icon>
                                    <span>متابعه عملاء</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    <li>
                        <a href="{{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? url('/report/customers/transactions'): '#' }}"
                            class="navItem {{ $customerTransReport ?? '' }} {{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? '': 'disabled' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="grommet-icons:transaction">
                                </iconify-icon>
                                <span>معاملات ماليه</span>
                            </span>
                        </a>
                    </li>

                    @can('viewSalesReports', App\Models\Orders\Order::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewSalesReports', App\Models\Orders\Order::class)? url('/report/orders'): '#' }}"
                                class="navItem {{ $ordersReport ?? '' }} {{ auth()->user()->can('viewSalesReports', App\Models\Orders\Order::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class="nav-icon" icon="icon-park-outline:ad-product"></iconify-icon>
                                    <span>متابعه Sales</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewReports', App\Models\Orders\Order::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? url('/report/orders/totals'): '#' }}"
                                class="navItem {{ $dailyTotalsReport ?? '' }} {{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class="nav-icon" icon="fluent-mdl2:bookmark-report"></iconify-icon>
                                    <span>المبيعات اليوميه</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewReports', App\Models\Orders\Order::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? url('/report/orders/monthly'): '#' }}"
                                class="navItem {{ $monthlyTotalsReport ?? '' }} {{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class="nav-icon" icon="fluent-mdl2:analytics-report"></iconify-icon>
                                    <span>المبيعات الشهرية</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewReports', App\Models\Orders\Order::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? url('/report/orders/weekly'): '#' }}"
                                class="navItem {{ $weeklyTotalsReport ?? '' }} {{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class="nav-icon" icon="tabler:chart-area-line-filled"></iconify-icon>
                                    <span>مبيعات المناطق</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewReports', App\Models\Orders\Order::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? url('/report/orders/performance'): '#' }}"
                                class="navItem {{ $salesPerformanceReport ?? '' }} {{ auth()->user()->can('viewReports', App\Models\Orders\Order::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class="nav-icon" icon="carbon:sales-ops"></iconify-icon>
                                    <span>أداء المبيعات اليومي</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    {{-- <li>
                        <a href="{{ url('/report/customers') }}" class="navItem {{ $customerReport ?? '' }}">
                            <span class="flex
                            items-center">
                                <iconify-icon class=" nav-icon" icon="garden:customer-lists-fill-26">
                                </iconify-icon>
                                <span>تقرير عملاء</span>
                            </span>
                        </a>
                    </li> --}}

                    <li class="sidebar-menu-title">Calendar</li>

                    <li>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Tasks\Task::class)? url('/tasks'): '#' }}"
                            class="navItem {{ $tasks ?? '' }}  {{ auth()->user()->can('viewAny', App\Models\Tasks\Task::class)? '': 'disabled' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="ic:round-add-task">
                                </iconify-icon>
                                <span>Tasks</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/calendar') }}" class="navItem">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="grommet-icons:calendar">
                                </iconify-icon>
                                <span>To-do</span>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Customers\Followup::class)? url('/followups'): '#' }}"
                            class="navItem {{ $followups ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Customers\Followup::class)? '': 'disabled' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="icon-park-outline:cycle-arrow">
                                </iconify-icon>
                                <span>Follow-ups</span>
                            </span>
                        </a>
                    </li>



                    <li class="sidebar-menu-title">Database</li>

                    @can('viewAny', App\Models\Products\Product::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Products\Product::class)? url('/products'): '#' }}"
                                class="navItem {{ $products ?? '' }} 
                            {{ auth()->user()->can('viewAny', App\Models\Products\Product::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="flowbite:tag-outline">
                                    </iconify-icon>
                                    <span>Products</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewAny', App\Models\Customers\Customer::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Customers\Customer::class)? url('/customers'): '#' }}"
                                class="navItem {{ $customers ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Customers\Customer::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="mdi:user">
                                    </iconify-icon>
                                    <span>Customers</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewAny', App\Models\Customers\Combo::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Products\Combo::class)? url('/combos'): '#' }}"
                                class="navItem {{ $combos ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Products\Combo::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="mage:box-3d-plus">
                                    </iconify-icon>
                                    <span>Offers</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewAny', App\Models\Customers\User::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Users\User::class)? route('profile', auth()->id()): '#' }}"
                                class="navItem {{ $profile ?? '' }}  {{ auth()->user()->can('viewAny', App\Models\Users\User::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="ph:user-bold">
                                    </iconify-icon>
                                    <span>Profile</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewAny', App\Models\Customers\User::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Users\User::class)? url('/users'): '#' }}"
                                class="navItem {{ $users ?? '' }}  {{ auth()->user()->can('viewAny', App\Models\Users\User::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="material-symbols-light:account-tree-rounded">
                                    </iconify-icon>
                                    <span>Users</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('viewAny', App\Models\Customers\Zone::class)
                        <li>
                            <a href="{{ auth()->user()->can('viewAny', App\Models\Customers\Zone::class)? url('/zones'): '#' }}"
                                class="navItem {{ $zones ?? '' }} {{ auth()->user()->can('viewAny', App\Models\Customers\Zone::class)? '': 'disabled' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="ri:time-zone-line">
                                    </iconify-icon>
                                    <span>Zones</span>
                                </span>
                            </a>
                        </li>
                    @endcan

                </ul>
            </div>
        </div>
        <!-- End: Sidebar -->
        <!-- End: Sidebar -->

        <!-- End: Settings -->
        <div class="flex flex-col justify-between min-h-screen">
            <div>
                <!-- BEGIN: Header -->
                <!-- BEGIN: Header -->
                <div class="z-[9]" id="app_header">
                    <div
                        class="app-header z-[999] ltr:ml-[248px] rtl:mr-[248px] bg-white dark:bg-slate-800 shadow-sm dark:shadow-slate-700">
                        <div class="flex justify-between items-center h-full">
                            <div
                                class="flex items-center md:space-x-4 space-x-2 xl:space-x-0 rtl:space-x-reverse vertical-box">
                                <a href="{{ url('/') }}" class="mobile-logo xl:hidden inline-block">
                                    <img src="{{ asset('assets/images/logo/genuine-icon.png') }}" class="black_logo"
                                        alt="logo">
                                    <img src="{{ asset('assets/images/logo/genuine-icon-white.png') }}"
                                        class="white_logo" alt="logoo">
                                </a>
                                <button class="smallDeviceMenuController hidden md:inline-block xl:hidden">
                                    <iconify-icon
                                        class="leading-none bg-transparent relative text-xl top-[2px] text-slate-900 dark:text-white"
                                        icon="heroicons-outline:menu-alt-3"></iconify-icon>
                                </button>
                                <button
                                    class="flex items-center xl:text-sm text-lg xl:text-slate-400 text-slate-800 dark:text-slate-300 px-1
        rtl:space-x-reverse search-modal"
                                    data-bs-toggle="modal" data-bs-target="#searchModal">
                                    <iconify-icon icon="heroicons-outline:search"></iconify-icon>
                                    <span class="xl:inline-block hidden ml-3">Search...
                                    </span>
                                </button>

                            </div>
                            <!-- end vertcial -->
                            <div class="items-center space-x-4 rtl:space-x-reverse horizental-box">
                                <a href="{{ url('/') }}">
                                    <span class="xl:inline-block hidden">
                                        <img src="{{ asset('assets/images/logo/logo.svg') }}" class="black_logo "
                                            alt="logo">
                                        <img src="{{ asset('assets/images/logo/logo.svg') }}assets/images/logo/logo-white.svg"
                                            class="white_logo" alt="logo">
                                    </span>
                                    <span class="xl:hidden inline-block">
                                        <img src="{{ asset('assets/images/logo/logo-c.svg') }}" class="black_logo "
                                            alt="logo">
                                        <img src="{{ asset('assets/images/logo/logo-c-white.svg') }}"
                                            class="white_logo " alt="logo">
                                    </span>
                                </a>
                                <button
                                    class="smallDeviceMenuController  open-sdiebar-controller xl:hidden inline-block">
                                    <iconify-icon
                                        class="leading-none bg-transparent relative text-xl top-[2px] text-slate-900 dark:text-white"
                                        icon="heroicons-outline:menu-alt-3"></iconify-icon>
                                </button>

                            </div>
                            <!-- end horizental -->



                            <div class="main-menu active">
                                <ul>
                                    <li>
                                        <a href="{{ url('/') }}" class="navItem @yield('home')">
                                            <div class="flex space-x-2 items-start rtl:space-x-reverse ">
                                                <iconify-icon icon=ic:home class="leading-[1] text-base">
                                                </iconify-icon>
                                                <span class="leading-[1]">
                                                    Home
                                                </span>
                                            </div>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ url('/calendar') }}" class="navItem @yield('calendar')">
                                            <div class="flex space-x-2 items-start rtl:space-x-reverse ">
                                                <iconify-icon icon=ic:calendar class="leading-[1] text-base">
                                                </iconify-icon>
                                                <span class="leading-[1]">
                                                    Calendar
                                                </span>
                                            </div>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="{{ url('/tasks') }}" class="navItem @yield('tasks')">
                                            <div class="flex space-x-2 items-start rtl:space-x-reverse">
                                                <iconify-icon class="leading-[1] text-base" icon="ic:round-add-task">
                                                </iconify-icon>
                                                <span class="leading-[1]">
                                                    Tasks
                                                </span>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="menu-item-has-children">
                                        <!--  Single menu -->

                                        <!-- has dropdown -->



                                        <a href="javascript:void()">
                                            <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                <span class="icon-box">
                                                    <iconify-icon icon=heroicons-outline:home> </iconify-icon>
                                                </span>
                                                <div class="text-box">Settings</div>
                                            </div>
                                            <div
                                                class="flex-none text-sm ltr:ml-3 rtl:mr-3 leading-[1] relative top-1">
                                                <iconify-icon icon="heroicons-outline:chevron-down"> </iconify-icon>
                                            </div>
                                        </a>

                                        <ul class="sub-menu">

                                            <li>
                                                <a href="{{ url('/policies') }}" class="navItem @yield('policies')">
                                                    <div class="flex space-x-2 items-start rtl:space-x-reverse">
                                                        <iconify-icon icon=heroicons:presentation-chart-line
                                                            class="leading-[1] text-base"> </iconify-icon>
                                                        <span class="leading-[1]">
                                                            Policies
                                                        </span>
                                                    </div>
                                                </a>
                                            </li>



                                            <li>
                                                <a href="{{ url('/cars') }}" class="navItem @yield('cars')">
                                                    <div class="flex space-x-2 items-start rtl:space-x-reverse">
                                                        <iconify-icon icon=heroicons:shopping-cart
                                                            class="leading-[1] text-base"> </iconify-icon>
                                                        <span class="leading-[1]">
                                                            Cars
                                                        </span>
                                                    </div>
                                                </a>
                                            </li>



                                            <li>
                                                <a href="{{ url('/companies') }}" class="navItem @yield('companies')">
                                                    <div class="flex space-x-2 items-start rtl:space-x-reverse">
                                                        <iconify-icon icon=heroicons:building-storefront
                                                            class="leading-[1] text-base"> </iconify-icon>
                                                        <span class="leading-[1]">
                                                            Companies
                                                        </span>
                                                    </div>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ url('/customers') }}" class="navItem @yield('customers')">
                                                    <div class="flex space-x-2 items-start rtl:space-x-reverse">
                                                        <iconify-icon icon=raphael:customer
                                                            class="leading-[1] text-base"> </iconify-icon>
                                                        <span class="leading-[1]">
                                                            Customers
                                                        </span>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>


                                    {{-- <li>
                                        <a href="{{url('/')}}">
                                            <div class="flex space-x-2 items-start rtl:space-x-reverse">
                                                <iconify-icon icon=raphael:customer class="leading-[1] text-base">
                                                </iconify-icon>
                                                <span class="leading-[1]">
                                                    Customers
                                                </span>
                                            </div>
                                        </a>
                                    </li> --}}



                                </ul>
                            </div>
                            <!-- end top menu -->
                            <div
                                class="nav-tools flex items-center lg:space-x-5 space-x-3 rtl:space-x-reverse leading-0">

                                <!-- BEGIN: Toggle Theme -->
                                <div>
                                    <button id="themeMood"
                                        class="h-[28px] w-[28px] lg:h-[32px] lg:w-[32px] lg:bg-gray-500-f7 bg-slate-50 dark:bg-slate-900 lg:dark:bg-slate-900 dark:text-white text-slate-900 cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center">
                                        <iconify-icon class="text-slate-800 dark:text-white text-xl dark:block hidden"
                                            id="moonIcon" icon="line-md:sunny-outline-to-moon-alt-loop-transition">
                                        </iconify-icon>
                                        <iconify-icon class="text-slate-800 dark:text-white text-xl dark:hidden block"
                                            id="sunIcon" icon="line-md:moon-filled-to-sunny-filled-loop-transition">
                                        </iconify-icon>
                                    </button>
                                </div>
                                <!-- END: TOggle Theme -->

                                <!-- BEGIN: gray-scale Dropdown -->
                                <div>
                                    <button id="grayScale"
                                        class="lg:h-[32px] lg:w-[32px] lg:bg-slate-100 lg:dark:bg-slate-900 dark:text-white text-slate-900 cursor-pointer
            rounded-full text-[20px] flex flex-col items-center justify-center">
                                        <iconify-icon class="text-slate-800 dark:text-white text-xl"
                                            icon="mdi:paint-outline"></iconify-icon>
                                    </button>
                                </div>
                                <!-- END: gray-scale Dropdown -->


                                <!-- BEGIN: gray-scale Dropdown -->
                                <!-- END: gray-scale Dropdown -->


                                <!-- BEGIN: Notification Dropdown -->
                                @php
                                    $notfCount = auth()->user()->getUnseenNotfCount();
                                @endphp
                                <!-- Notifications Dropdown area -->
                                <div class="relative md:block hidden">
                                    <button
                                        class="lg:h-[32px] lg:w-[32px] lg:bg-slate-100 lg:dark:bg-slate-900 dark:text-white text-slate-900 cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <iconify-icon class="animate-tada text-slate-800 dark:text-white text-xl"
                                            icon="heroicons-outline:bell"></iconify-icon>
                                        @if ($notfCount)
                                            <span
                                                class="absolute -right-1 lg:top-0 -top-[6px] h-4 w-4 bg-red-500 text-[8px] font-semibold flex flex-col items-center justify-center rounded-full text-white z-[99]">
                                                {{ $notfCount }}</span>
                                        @endif
                                    </button>
                                    <!-- Notifications Dropdown -->
                                    <div class="dropdown-menu z-10 hidden bg-white shadow w-[335px] dark:bg-slate-800 border dark:border-slate-700 !top-[23px] rounded-md overflow-hidden lrt:origin-top-right rtl:origin-top-left"
                                        style="max-height: 390px; overflow: overlay;">
                                        <div class="flex items-center justify-between py-4 px-4">
                                            <h3 class="text-sm font-Inter font-medium text-slate-700 dark:text-white">
                                                Notifications</h3>
                                            <a class="text-xs font-Inter font-normal underline text-slate-500 dark:text-white"
                                                href="{{ url('/notifications') }}">See All</a>
                                        </div>
                                        @if (!auth()->user()->notifications->isEmpty())
                                            @foreach (auth()->user()->latest_notifications as $notification)
                                                {{-- BEGIN: ONE Notification --}}
                                                {{-- classes for unread notf. dark:bg-slate-700 dark:bg-opacity-70 text-slate-800 --}}

                                                <div
                                                    class="text-slate-600 dark:text-slate-300 block w-full px-4 py-2 text-sm">
                                                    <div class="flex ltr:text-left rtl:text-right relative">
                                                        <div class="flex-none ltr:mr-3 rtl:ml-3">
                                                            <div
                                                                class="lg:h-8 lg:w-8 h-7 w-7 rounded-full flex-1 ltr:mr-[10px] rtl:ml-[10px]">
                                                                <span
                                                                    class="block w-full h-full object-cover text-center text-lg leading-8 user-initial">
                                                                    {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1">
                                                            <a href="{{ $notification->route }}"
                                                                class="text-slate-600 dark:text-slate-300 text-sm font-medium mb-1 before:w-full before:h-full before:absolute before:top-0 before:left-0">
                                                                @if (!$notification->is_seen)
                                                                    *
                                                                @endif
                                                                {{ $notification->title }}
                                                            </a>
                                                            <div
                                                                class="text-slate-600 dark:text-slate-300 text-xs leading-4">
                                                                {{ $notification->message }}
                                                            </div>
                                                            {{ $notification->created_at->diffForHumans() }}

                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- End: ONE Notification --}}
                                            @endforeach
                                        @else
                                            <div
                                                class="text-slate-600 dark:text-slate-300 block w-full px-4 py-2 text-sm">
                                                <div class="flex ltr:text-left rtl:text-right relative">
                                                    <div class="flex-none ltr:mr-3 rtl:ml-3">
                                                        <p>You have no notifications at the moment.</p>
                                                    </div>
                                                </div>
                                            </div>

                                        @endif

                                    </div>
                                </div>
                                <!-- END: Notification Dropdown -->

                                <!-- BEGIN: Profile Dropdown -->
                                <!-- Profile DropDown Area -->
                                <div class="md:block hidden w-full">
                                    <button
                                        class="text-slate-800 dark:text-white focus:ring-0 focus:outline-none font-medium rounded-lg text-sm text-center
      inline-flex items-center"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div
                                            class="lg:h-8 lg:w-8 h-7 w-7 rounded-full flex-1 ltr:mr-[10px] rtl:ml-[10px]">
                                            <span
                                                class="block w-full h-full object-cover text-center text-lg leading-8 user-initial">
                                                {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span
                                            class="flex-none text-slate-600 dark:text-white text-sm font-normal items-center lg:flex hidden overflow-hidden text-ellipsis whitespace-nowrap">{{ ucwords(Auth::user()->username) }}</span>
                                        <svg class="w-[16px] h-[16px] dark:text-white hidden lg:inline-block text-base inline-block ml-[10px] rtl:mr-[10px]"
                                            aria-hidden="true" fill="none" stroke="currentColor"
                                            viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <!-- Dropdown menu -->
                                    <div
                                        class="dropdown-menu z-10 hidden bg-white divide-y divide-slate-100 shadow w-44 dark:bg-slate-800 border dark:border-slate-700 !top-[23px] rounded-md
      overflow-hidden">
                                        <ul class="py-1 text-sm text-slate-800 dark:text-slate-200">
                                            <li>
                                                <a href="{{ url('/') }}"
                                                    class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white font-inter text-sm text-slate-600
            dark:text-white font-normal">
                                                    <iconify-icon icon="heroicons-outline:user"
                                                        class="relative top-[2px] text-lg ltr:mr-1 rtl:ml-1">
                                                    </iconify-icon>
                                                    <span class="font-Inter">Dashboard</span>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ url('tasks/my') }}"
                                                    class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white font-inter text-sm text-slate-600
            dark:text-white font-normal">
                                                    <iconify-icon icon="heroicons-outline:clipboard-check"
                                                        class="relative top-[2px] text-lg ltr:mr-1 rtl:ml-1">
                                                    </iconify-icon>
                                                    <span class="font-Inter">Tasks</span>
                                                </a>
                                            </li>


                                            <li>
                                                <a href="{{ route('profile', auth()->id()) }}"
                                                    class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white font-inter text-sm text-slate-600
            dark:text-white font-normal">
                                                    <iconify-icon icon="iconamoon:profile-bold"
                                                        class="relative top-[2px] text-lg ltr:mr-1 rtl:ml-1">
                                                    </iconify-icon>
                                                    <span class="font-Inter">Profile</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('logout') }}"
                                                    class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white font-inter text-sm text-slate-600
            dark:text-white font-normal">
                                                    <iconify-icon icon="heroicons-outline:login"
                                                        class="relative top-[2px] text-lg ltr:mr-1 rtl:ml-1">
                                                    </iconify-icon>
                                                    <span class="font-Inter">Logout</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- END: Header -->
                                <button class="smallDeviceMenuController md:hidden block leading-0">
                                    <iconify-icon class="cursor-pointer text-slate-900 dark:text-white text-2xl"
                                        icon="heroicons-outline:menu-alt-3"></iconify-icon>
                                </button>
                                <!-- end mobile menu -->
                            </div>
                            <!-- end nav tools -->
                        </div>
                    </div>
                </div>

                <!-- BEGIN: Search Modal -->
                <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                    id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
                    <div class="modal-dialog relative w-auto pointer-events-none top-1/4">
                        <div
                            class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white dark:bg-slate-900 bg-clip-padding rounded-md outline-none text-current">
                            <form>
                                <div class="relative">
                                    <input type="text" class="form-control !py-3 !pr-12" placeholder="Search">
                                    <button
                                        class="absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full border-l text-xl border-l-slate-200 dark:border-l-slate-600 dark:text-slate-300 flex items-center justify-center">
                                        <iconify-icon icon="heroicons-solid:search"></iconify-icon>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- END: Search Modal -->
                <!-- END: Header -->
                <!-- END: Header -->
                <div class="content-wrapper transition-all duration-150 ltr:ml-[248px] rtl:mr-[248px]"
                    id="content_wrapper">
                    <div class="page-content">
                        <div class="transition-all duration-150 container-fluid" id="page_layout">
                            <div id="content_layout">



                                <!-- The actual SIMPLE-TOAST  -->
                                <div id="simpleToast">

                                </div>


                                {{-- <livewire:confirmation-modal /> --}}
                                <div class=" space-y-5">
                                    {{ $slot }}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white bg-no-repeat custom-dropshadow footer-bg dark:bg-slate-700 flex justify-around items-center
    backdrop-filter backdrop-blur-[40px] fixed left-0 bottom-0 w-full z-[9999] bothrefm-0 py-[12px] px-4 md:hidden">
                <a href="chat.html">
                    <div>
                        <span
                            class="relative cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center mb-1 dark:text-white
          text-slate-900 ">
                            <iconify-icon icon="heroicons-outline:mail"></iconify-icon>
                            <span
                                class="absolute right-[5px] lg:hrefp-0 -hrefp-2 h-4 w-4 bg-red-500 text-[8px] font-semibold flex flex-col items-center
            justify-center rounded-full text-white z-[99]">
                                10
                            </span>
                        </span>
                        <span class="block text-[11px] text-slate-600 dark:text-slate-300">
                            Messages
                        </span>
                    </div>
                </a>
                <a href="profile.html"
                    class="relative bg-white bg-no-repeat backdrop-filter backdrop-blur-[40px] rounded-full footer-bg dark:bg-slate-700
      h-[65px] w-[65px] z-[-1] -mt-[40px] flex justify-center items-center">
                    <div class="h-[50px] w-[50px] rounded-full relative left-[0px] hrefp-[0px] custom-dropshadow">
                        <img src="{{ asset('assets/images/users/user-1.jpg') }}" alt=""
                            class="w-full h-full rounded-full border-2 border-slate-100">
                    </div>
                </a>
                <a href="{{ url('notifications') }}">
                    <div>
                        <span
                            class=" relative cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center mb-1 dark:text-white
          text-slate-900">
                            <iconify-icon icon="heroicons-outline:bell"></iconify-icon>
                            <span
                                class="absolute right-[17px] lg:hrefp-0 -hrefp-2 h-4 w-4 bg-red-500 text-[8px] font-semibold flex flex-col items-center
            justify-center rounded-full text-white z-[99]">
                                2
                            </span>
                        </span>
                        <span class=" block text-[11px] text-slate-600 dark:text-slate-300">
                            Notifications
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </main>
    <!-- scripts -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/rt-plugins.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        // $(function() {
        //     var start = moment().subtract(4, 'months');
        //     var end = moment();

        //     function cb(start, end) {
        //         $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        //         // console.log('Start date: ' + start.format('YYYY-MM-DD'));
        //         // console.log('End date: ' + end.format('YYYY-MM-DD'));

        //         Livewire.emit('dateRangeSelected', start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        //     }

        //     $('#reportrange').daterangepicker({
        //         startDate: start,
        //         endDate: end,
        //         "alwaysShowCalendars": true,
        //         ranges: {
        //             'Today': [moment(), moment()],
        //             'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        //             'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        //             'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        //             'This Month': [moment().startOf('month'), moment().endOf('month')],
        //             // 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
        //             //     'month').endOf('month')],
        //             'Last 3 Months': [moment().subtract(3, 'months'), moment()],
        //         }
        //     }, cb);

        //     cb(start, end);


        // });
    </script>


    <script>
        @if (session('alert_msg'))
            console.log("{{ session('alert_msg') }}")
            Swal.fire("{{ session('alert_msg') }}")
        @endif

        const setAsSeen = (id) => {
            $.ajax({
                url: "{{ url('notifications/seen/') }}" + "/" + id,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                }
            })
        }
    </script>

    @yield('child_scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script> --}}
</body>

</html>
