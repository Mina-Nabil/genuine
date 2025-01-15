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

                <span wire:click='openZoneSec' class="badge bg-slate-900 text-white capitalize" type="button"
                    id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="cursor-pointer">
                        <span class="text-secondary-500 ">Zone:</span>&nbsp;
                        @foreach ($selectedZonesNames as $zz)
                            {{ $zz }},
                        @endforeach

                    </span>
                </span>

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
                            <span class="text-secondary-500 ">Has Ordered? </span>&nbsp;
                            {{ $is_ordered ? 'Yes' : 'No' }}

                        </span>
                    </span>
                    <ul class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                        z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                        style="z-index: 999;">

                        <li wire:click='setIsOrdered(1)'
                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                            Yes
                        </li>
                        <li wire:click='setIsOrdered(0)'
                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                            No
                        </li>

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

            <header class="card-header cust-card-header noborder">
                <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                    icon="line-md:loading-twotone-loop"></iconify-icon>
                <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                    wire:model.live.debounce.400ms="search">
            </header>
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
                            @while ($start_week->isBefore($end_week))
                                <th scope="col" class="table-th">
                                    {{ getWeekOfMonth($start_week) }}
                                </th>
                                @php
                                    $start_week->addWeek();
                                @endphp
                            @endwhile
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                        @foreach ($customers as $i => $c)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700">
                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                    style="position: sticky; left: -25px;z-index:auto">
                                    <div class="flex-1 text-start">
                                        <div class="flex justify-between">
                                            <h4 class="text-lg font-medium text-slate-600 whitespace-nowrap">
                                                <a href="{{ route('customer.show', $c->id) }}" target="_blanck"
                                                    class="hover-underline">
                                                    <b>{{ $c->name }}</b>
                                                </a>

                                            </h4>

                                            <div class="dropdown relative">
                                                <button class="text-xl text-center block w-full " type="button"
                                                    id="tableDropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <iconify-icon
                                                        icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                </button>
                                                <ul
                                                    class=" dropdown-menu min-w-[120px] text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none dropdown-position" style=" ">

                                                    @if ($c->last_order_id)
                                                        <li>
                                                            <button
                                                                wire:click='reorderLastOrder({{ $c->last_order_id }})'
                                                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4  w-full text-left py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                                <iconify-icon
                                                                    icon="radix-icons:reload"></iconify-icon>&nbsp;
                                                                Repeat Last Order
                                                            </button>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <button wire:click='openAddFollowupSec({{ $c->id }})'
                                                            class="text-slate-600 dark:text-white block font-Inter font-normal px-4  w-full text-left py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                            <iconify-icon
                                                                icon="material-symbols:add"></iconify-icon>&nbsp;
                                                            Add follow-up</button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                            Target: <b>{{ $c->monthly_weight_target / 1000 ?? 0 }}</b> KG
                                        </div>

                                    </div>
                                </td>
                                @foreach ($c->ordersKGs as $weekly_weight)
                                    <td class="table-td">
                                        <b>{{ $weekly_weight / 1000 ?? 0 }}</b>
                                        <small>KG</small>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                @if (empty($customers))
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No customers with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $customers->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>

    @if ($Edited_Zone_sec)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Filter by Zones
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="removeSelectedZone,Edited_Zone"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </h3>
                            <button wire:click="closeZoneSec" type="button"
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

                            <div class="from-group">
                                <div class="input-area">
                                    <label for="Edited_Zone" class="form-label">Zone</label>
                                    <select
                                        class="form-control w-full mt-2 @error('Edited_Zone') !border-danger-500 @enderror"
                                        wire:model.live="Edited_Zone" autocomplete="off">
                                        <option selected readonly>Select Zones</option>
                                        @foreach ($saved_zones as $z)
                                            <option value="{{ $z->id }}">
                                                {{ $z->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('Edited_Zone')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            @foreach ($selectedZonesNames as $index => $zone)
                                <span class="badge bg-slate-900 text-white capitalize">
                                    <span class="cursor-pointer">
                                        {{ $zone }}
                                    </span>

                                    &nbsp;&nbsp;<iconify-icon wire:click="removeSelectedZone({{ $index }})"
                                        icon="material-symbols:close" class="cursor-pointer" width="1.2em"
                                        height="1.2em"></iconify-icon>
                                </span>
                            @endforeach

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setZones" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setZones">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setZones"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    @if ($addFollowupSection)
        {{-- add address section --}}
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Add Follow up
                            </h3>
                            <button wire:click="closeFollowupSection" type="button"
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
                            <div class="from-group">
                                <div class="input-area">
                                    <label for="firstName" class="form-label">Title</label>
                                    <input id="lastName" type="text"
                                        class="form-control @error('followupTitle') !border-danger-500 @enderror"
                                        wire:model.defer="followupTitle">
                                </div>
                                @error('followupTitle')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mt-3">
                                    <div class="input-area">
                                        <label for="firstName" class="form-label">Call Date</label>
                                        <input id="lastName" type="date"
                                            class="form-control @error('followupCallDate') !border-danger-500 @enderror"
                                            wire:model.defer="followupCallDate">
                                    </div>
                                    @error('followupCallDate')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    <div class="input-area">
                                        <label for="firstName" class="form-label"> Time</label>
                                        <input id="lastName" type="time"
                                            class="form-control @error('followupCallTime') !border-danger-500 @enderror"
                                            wire:model.defer="followupCallTime">
                                    </div>
                                    @error('followupCallTime')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-area mt-3">
                                    <label for="firstName" class="form-label">Description</label>
                                    <input id="lastName" type="text"
                                        class="form-control @error('followupDesc') !border-danger-500 @enderror"
                                        wire:model.defer="followupDesc">
                                </div>
                                @error('followupDesc')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="addFollowup" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="addFollowup">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addFollowup"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
