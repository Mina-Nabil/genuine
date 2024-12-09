<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Followup Report
            </h4>
        </div>
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <div>

                <span wire:click='openSetZoneSec' class="badge bg-slate-900 text-white capitalize" type="button" id="secondaryFlatDropdownMenuButton"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="cursor-pointer">
                        <span class="text-secondary-500 ">Zone:</span>&nbsp;
                        {{ $zone->name }}

                    </span>
                </span>

                {{-- <div class="dropdown relative" style="display: contents">
                    <span class="badge bg-slate-900 text-white capitalize" type="button"
                        id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="cursor-pointer">
                            <span class="text-secondary-500 ">Zone:</span>&nbsp;
                            {{ $zone->name }}

                        </span>
                    </span>
                    <ul class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                        z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                        style="z-index: 999;height: 235px;overflow-y: auto;">
                        @foreach ($zones as $one_zone)
                            <li wire:click='setZone({{ $one_zone->id }})'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                {{ $one_zone->name }}
                            </li>
                        @endforeach

                    </ul>
                </div> --}}

                <div class="dropdown relative" style="display: contents">
                    <span class="badge bg-slate-900 text-white capitalize" type="button"
                        id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="cursor-pointer">
                            <span class="text-secondary-500 ">Year:</span>&nbsp;
                            {{ $year }}

                        </span>
                    </span>
                    <ul class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                        z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                        style="z-index: 999;">
                        @foreach ($years as $one_year)
                            <li wire:click='setYear({{ $one_year }})'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                {{ $one_year }}
                            </li>
                        @endforeach

                    </ul>
                </div>

                <div class="dropdown relative" style="display: contents">
                    <span class="badge bg-slate-900 text-white capitalize" type="button"
                        id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="cursor-pointer">
                            <span class="text-secondary-500 ">Month:</span>&nbsp;
                            {{ \Carbon\Carbon::createFromFormat('m', $selectedMonth)->monthName }}

                        </span>
                    </span>
                    <ul class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                        z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                        style="z-index: 999;height: 235px;overflow-y: auto;">
                        @foreach ($months as $one_months)
                            <li wire:click='setMonth({{ $one_months }})'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                {{ \Carbon\Carbon::createFromFormat('m', $one_months)->monthName }}
                            </li>
                        @endforeach

                    </ul>
                </div>

                <div class="dropdown relative" style="display: contents">
                    <span class="badge bg-slate-900 text-white capitalize" type="button"
                        id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="cursor-pointer">
                            <span class="text-secondary-500 ">Week:</span>&nbsp;
                            {{ $selectedWeek }}

                        </span>
                    </span>
                    <ul class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                        z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                        style="z-index: 999;">
                        @foreach ($weeksToSelect as $one_weeksToSelect)
                            <li wire:click='setWeek({{ $one_weeksToSelect }})'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                Week {{ $one_weeksToSelect }}
                            </li>
                        @endforeach

                    </ul>
                </div>



            </div>
        </header>

        <div class="card-body px-6 pb-6  overflow-x-auto">
            <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
                    <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                        <tr>
                            <th scope="col"
                                class="table-th flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                style="position: sticky; left: -25px;  z-index: 10;">
                                Customer Name
                            </th>
                            @foreach ($weeks as $week)
                                <th scope="col" class="table-th">
                                    {{ getWeekOfMonth(\Carbon\Carbon::parse($week)->format('Y-m-d')) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                        @foreach ($customerWeights as $customerName => $weights)
                            <tr>
                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                    style="position: sticky; left: -25px; z-index: 10;">
                                    <div class="flex-1 text-start">
                                        <h4 class="text-lg font-medium text-slate-600 whitespace-nowrap">
                                            <a href="{{ route('customer.show', $weights['customer_id']) }}"
                                                target="_blanck" class="hover-underline">
                                                <b>{{ $customerName }}</b>
                                            </a>

                                        </h4>
                                        <div class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                            Target: <b>{{ $weights['monthly_weight_target'] / 1000 ?? 0 }}</b> KG
                                        </div>
                                        <div wire:click='reorderLastOrder({{ $weights['last_order_id'] }})'
                                            class="text-xs font-normal text-slate-600 dark:text-slate-400 clickable-link">
                                            <span><iconify-icon icon="radix-icons:reload"></iconify-icon> Repeat Last
                                                Order</span>
                                        </div>
                                    </div>
                                </td>
                                @foreach ($weeks as $week)
                                    <td class="table-td">
                                        <b>{{ $weights['weekly_weights'][$week] / 1000 ?? 0 }}</b>
                                        <small>KG</small>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                @if (empty($customerWeights))
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No products with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/products') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all products</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        {{-- <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $customerWeights->links('vendor.livewire.simple-bootstrap') }}
        </div> --}}

    </div>

    @if ($setZoneSection)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Set Zone
                            </h3>
                            <button wire:click="closeSetZoneSec" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6 space-y-4">

                            <div class="input-area">
                                <input type="text" placeholder="Search zones..."
                                    class="form-control @error('searchZoneText') !border-danger-500 @enderror"
                                    wire:model.live='searchZoneText'>
                            </div>

                            <div class="overflow-x-auto -mx-6" style="max-height: 300px;">
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden ">
                                        <table
                                            class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                            <tbody
                                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                                @foreach ($zones as $one_zone)
                                                    <tr wire:click='setZone({{ $one_zone->id }})'
                                                        class="hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer">
                                                        <td class="table-td">
                                                            <p>
                                                                <b>{{ $one_zone->name }}</b>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>
