<div>
    @section('head_content')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @endsection



    <div class="space-y-5 profile-page mx-auto" style="max-width: 800px">

        <div class="bg-no-repeat bg-cover bg-center p-5 rounded-[6px] relative"
            style="background-image: url({{ asset('assets/images/all-img/genuine-dashboard-banner.jpg') }})">
            <div class="max-w-[180px]">
                <h4 class="text-xl font-medium text-white mb-2">
                    <span class="block font-normal">
                        Good
                        @php
                            $hour = date('H'); // Get the current hour in 24-hour format
                        @endphp
                        @if ($hour >= 5 && $hour < 12)
                            morning
                        @elseif ($hour >= 12 && $hour < 18)
                            afternoon
                        @else
                            evening
                        @endif
                    </span>
                    <span class="block">{{ Auth::user()->full_name }}</span>
                </h4>
                <p class="text-sm text-white font-normal">
                    Welcome to Genuine
                </p>
            </div>
        </div>

        <input type="text" class="form-control w-auto d-inline-block cursor-pointer" style="width:auto"
            name="datetimes" id="reportrange" />

        <div class="grid grid-cols-12 gap-5 mb-5">
            <div class="col-span-12">
                <div class="grid md:grid-cols-4 grid-cols-1 gap-4">
                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-[#E5F9FF] dark:bg-slate-900	 text-info-500">
                                        <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>

                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Active Orders
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ $totalActiveOrdersCount }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-[#E5F9FF] dark:bg-slate-900	 text-info-500">
                                        <iconify-icon icon="heroicons:shopping-cart"></iconify-icon>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Total orders
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ $totalOrdersCount }} <small>Orders</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-[#FFEDE6] dark:bg-slate-900	 text-warning-500">
                                        <iconify-icon icon="mdi:weight-kilogram"></iconify-icon>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Weight sold
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ $totalOrdersWeight / 1000 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-[#EAE6FF] dark:bg-slate-900	 text-[#5743BE]">
                                        <iconify-icon icon="game-icons:take-my-money"></iconify-icon>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Total / Paid
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ number_format($totalAmount) }} / {{ number_format($totalPaid) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- END: Group Chart -->
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-5 mb-5">
            <div class="col-span-12">
                <div class="grid md:grid-cols-3 grid-cols-1 gap-4">
                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-success-100 dark:bg-slate-900	 text-success-500">
                                        <iconify-icon icon="mdi:cash"></iconify-icon>

                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Cash
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ number_format($cashBalance) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-success-100 dark:bg-slate-900	 text-success-500">
                                        <iconify-icon icon="mdi:bank-transfer"></iconify-icon>

                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Bank
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ number_format($bankBalance) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-success-100 dark:bg-slate-900	 text-success-500">
                                        <iconify-icon icon="mdi:wallet"></iconify-icon>

                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Wallet
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ number_format($walletBalance) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 gap-5 mb-5">
            <div class="col-span-12">
                <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-success-100 dark:bg-slate-900	 text-success-500">
                                        <iconify-icon icon="mdi:wallet"></iconify-icon>

                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Total
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ number_format($walletBalance + $cashBalance + $bankBalance) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body pt-4 pb-3 px-4">
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <div class="flex-none">
                                    <div
                                        class="h-12 w-12 rounded-full flex flex-col items-center justify-center text-2xl bg-danger-100 dark:bg-slate-900 text-danger-500">
                                        <iconify-icon icon="mdi:cash-minus"></iconify-icon>

                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-slate-600 dark:text-slate-300 text-sm mb-1 font-medium">
                                        Unpaid Invoices
                                    </div>
                                    <div class="text-slate-900 dark:text-white text-lg font-medium">
                                        {{ number_format($totalUnpaidInvoices) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <header class="card-header noborder">
                <h4 class="card-title">Analysis</h4>
            </header>
            <div class="card-body p-6 pt-0">

                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden ">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="  bg-slate-200 dark:bg-slate-700">
                                    <tr>

                                        <th scope="col" class=" table-th ">
                                            Driver
                                        </th>

                                        <th scope="col" class=" table-th ">
                                            Orders
                                        </th>

                                        <th scope="col" class=" table-th ">
                                            Days
                                        </th>

                                        <th scope="col" class=" table-th ">
                                            Weight
                                        </th>

                                        <th scope="col" class=" table-th ">
                                            Zone List
                                        </th>

                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                    @forelse ($usersStatistics as $user)
                                        <tr>
                                            <td class="table-td">
                                                <div class="flex items-center">
                                                    <div class="flex-none">
                                                        <div class="w-8 h-8 rounded-[100%] ltr:mr-3 rtl:ml-3">
                                                            <span
                                                                class="block w-full h-full object-cover text-center text-lg leading-8 user-initial">
                                                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 text-start">
                                                        <h4
                                                            class="text-sm font-medium text-slate-600 whitespace-nowrap">
                                                            {{ $user->full_name }}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="table-td"> <b>{{ $user->total_orders }}</b>
                                                <small>Orders</small>
                                            </td>

                                            <td class="table-td"> <b>{{ $user->total_days }}</b>
                                                <small>Days</small>
                                            </td>

                                            <td class="table-td"> <b>{{ $user->total_weight / 1000 }}</b>
                                                <small>KG</small>
                                            </td>

                                            <td class="table-td">
                                                @php
                                                    $zones = explode(',', $user->zone_names); // Convert the comma-separated zone names into an array
                                                @endphp
                                                @foreach ($zones as $zone)
                                                    <span>{{ $zone }}</span>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="card m-5 p-5">
                                                    <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                                        <div class="items-center text-center p-5">
                                                            <h2>
                                                                <iconify-icon
                                                                    icon="icon-park-solid:analysis"></iconify-icon>
                                                            </h2>
                                                            <h2 class="card-title text-slate-900 dark:text-white mb-3">
                                                                There was no data found for this date range</h2>
                                                            <p class="card-text">Try changing the date range.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse




                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>



            </div>
        </div>

        <div class="card">
            <header class="card-header">
                <h4 class="card-title flex justify-start content-center gap-2">
                    <span>Raw Materials</span>
                    @if (!$materialsUnderLimit->isEmpty())
                        <span><span class="badge bg-danger-500 text-white capitalize rounded-3xl">Below Minimum
                                Limit</span></span>
                    @endif
                </h4>
                <div>
                </div>
            </header>
            <div class="card-body p-6">
                @if (!$materialsUnderLimit->isEmpty())
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                        <li
                            class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
                            <div class="flex justify-between">
                                <span>Material</span>
                                <span>Available / Minimum Limit</span>
                            </div>
                        </li>

                        @foreach ($materialsUnderLimit as $material)
                            <li
                                class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase
                            hover:bg-slate-200 dark:hover:bg-slate-700">
                                <div class="flex justify-between">
                                    <span class="hover-underline">
                                        <a href="{{ route('material.show', $material->id) }}">
                                            <b>{{ $material->name }}</b>
                                        </a>
                                    </span>
                                    <div>
                                        <b>
                                            <span class="text-danger-500">{{ $material->inventory->on_hand }}</span>
                                            <span>/ {{ $material->min_limit }}</span>
                                        </b>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="flex justify-center p-5">
                        <div class="text-center">
                            <iconify-icon class="text-success-500" icon="hugeicons:shopping-basket-done-03"
                                width="50" height="50"></iconify-icon>
                            <div class="text-gray-500 text-sm mt-2">No materials below limit found.</div>
                        </div>
                    </div>
                @endif



            </div>
        </div>

        <div class="card">
            <header class=" card-header noborder">
                <h4 class="card-title">Supplier Raw Materials (Expired) Pricing
                </h4>
            </header>
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden ">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class=" border-t border-slate-100 dark:border-slate-800">
                                    <tr>

                                        <th scope="col" class=" table-th ">
                                            Supplier
                                        </th>

                                        <th scope="col" class=" table-th ">
                                            Material
                                        </th>

                                        <th scope="col" class=" table-th ">
                                            Expiration Date
                                        </th>

                                        <th scope="col" class=" table-th ">
                                            Price
                                        </th>

                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                    @if (!$nearlyExpiredMaterials->isEmpty() || !$expiredMaterials->isEmpty())

                                        @if (!$nearlyExpiredMaterials->isEmpty())
                                            <tr>
                                                <td class="table-td" colspan="4">
                                                    <span
                                                        class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl w-full">Nearly
                                                        Expired</span>
                                                </td>
                                            </tr>
                                            @foreach ($nearlyExpiredMaterials as $nearlyExpiredMaterial)
                                                <tr>
                                                    <td class="table-td">
                                                        <span class="hover-underline">
                                                            <a
                                                                href="{{ route('material.show', $nearlyExpiredMaterial->rawMaterial->id) }}">
                                                                {{ $nearlyExpiredMaterial->rawMaterial->name }}
                                                            </a>
                                                        </span>
                                                    </td>
                                                    <td class="table-td">
                                                        <span class="hover-underline">
                                                            <a
                                                                href="{{ route('supplier.show', $nearlyExpiredMaterial->supplier->id) }}">
                                                                {{ $nearlyExpiredMaterial->supplier->name }}
                                                            </a>
                                                        </span>
                                                    </td>
                                                    <td class="table-td">
                                                        <span class="text-warning-500">
                                                            <b>
                                                                {{ \Carbon\Carbon::parse($nearlyExpiredMaterial->expiration_date)->format('l d/m/Y') }}
                                                            </b>
                                                        </span>
                                                    </td>
                                                    <td class="table-td">
                                                        {{ number_format($nearlyExpiredMaterial->price, 2) }}
                                                        <small>EGP</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if (!$expiredMaterials->isEmpty())
                                            <tr>
                                                <td class="table-td" colspan="4">
                                                    <span
                                                        class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl w-full">Expired</span>
                                                </td>
                                            </tr>
                                            @foreach ($expiredMaterials as $expiredMaterial)
                                                <tr>
                                                    <td class="table-td">
                                                        <span class="hover-underline">
                                                            <a
                                                                href="{{ route('material.show', $expiredMaterial->rawMaterial->id) }}">
                                                                {{ $expiredMaterial->rawMaterial->name }}
                                                            </a>
                                                        </span>
                                                    </td>
                                                    <td class="table-td">
                                                        <span class="hover-underline">
                                                            <a
                                                                href="{{ route('supplier.show', $expiredMaterial->supplier->id) }}">
                                                                {{ $expiredMaterial->supplier->name }}
                                                            </a>
                                                        </span>
                                                    </td>
                                                    <td class="table-td ">
                                                        <span class="text-danger-500">
                                                            <b>
                                                                {{ \Carbon\Carbon::parse($expiredMaterial->expiration_date)->format('l d/m/Y') }}
                                                            </b>
                                                        </span>
                                                    </td>
                                                    <td class="table-td">
                                                        {{ number_format($expiredMaterial->price, 2) }}
                                                        <small>EGP</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @else
                                        <tr>
                                            <td class="table-td" colspan="4" class="text-center">
                                                <div class="flex justify-center p-5">
                                                    <div class="text-center">
                                                        <iconify-icon class="text-success-500"
                                                            icon="hugeicons:task-done-01" width="50"
                                                            height="50"></iconify-icon>
                                                        <div class="text-gray-500 text-sm mt-2">No nearly expired or
                                                            expired materials found.</div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif



                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                Livewire.dispatch('dateRangeSelected', {
                    data: [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')]
                });

            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'months').startOf('month'), moment().subtract(1,
                        'months').endOf('month')],
                    'Last 3 Months': [moment().subtract(3, 'months'), moment()],
                }
            }, cb);

            cb(start, end);
        });
    </script>
</div>
