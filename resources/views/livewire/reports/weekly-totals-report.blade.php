<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Weekly Totals (Orders) Report
            </h4>
        </div>

        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <div class="dropdown relative">
                <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                    id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter
                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                </button>
                <ul
                    class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                    <li wire:click='openZoneSec'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Zone
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="flex">
        <div class="dropdown relative mb-5">
            <button class="btn inline-flex justify-center btn-outline-dark items-center btn-sm" type="button"
                id="darkOutlineDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Year: {{ $selectedYear }}
                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
            </button>
            <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                style="">

                @foreach ($lastYears as $year)
                    <li wire:click='selectYear({{ $year }})'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                    dark:hover:text-white cursor-pointer">
                        {{ $year }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="dropdown relative ml-2 mb-5">
            <button class="btn inline-flex justify-center btn-outline-dark items-center btn-sm" type="button"
                id="darkOutlineDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Month: {{ \Carbon\Carbon::createFromFormat('m', $selectedMonth)->format('F') }}
                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
            </button>
            <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                style="">

                @foreach ($AllMonths as $month)
                    <li wire:click='selectMonth({{ $month }})'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                    dark:hover:text-white cursor-pointer">
                        {{ \Carbon\Carbon::createFromFormat('m', $month)->format('F') }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>




    <div class="card">

        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="searchTerm" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search zone here..."
                wire:model.live.debounce.400ms="searchTerm">
        </header>

        @if (count($selectedZonesNames))
            <header class="card-header cust-card-header noborder">
                <span wire:click='openZoneSec' class="badge bg-slate-900 text-white capitalize" type="button"
                    id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="cursor-pointer">
                        <span class="text-secondary-500 ">Zone:</span>&nbsp;
                        @foreach ($selectedZonesNames as $zz)
                            {{ $zz }},
                        @endforeach

                    </span>
                </span>
            </header>
        @endif

        <div class="card-body pb-6  overflow-x-auto">
            <div class="">
                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
                    <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                        <tr>


                            <th scope="col" class="table-th">Zone</th>
                            <th scope="col" class="table-th">Week 1</th>
                            <th scope="col" class="table-th">Week 2</th>
                            <th scope="col" class="table-th">Week 3</th>
                            <th scope="col" class="table-th">Week 4</th>
                            <th scope="col" class="table-th">Total Monthly Orders</th>


                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($groupedZoneReports as $zoneName => $weeks)
                            <tr class="even:bg-slate-100 dark:even:bg-slate-700">
                                <td class="table-td"><b>{{ $zoneName }}</b></td>
                                @for ($i = 1; $i <= 4; $i++)
                                    <td class="table-td">
                                        {{ $weeks->firstWhere('week', $i)->total_orders ?? 0 }}
                                    </td>
                                @endfor
                                <td class="table-td"><b>{{ $weeks->sum('total_orders') }}</b></td>
                            </tr>
                        @endforeach
                        <tfoot class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <th scope="col" class="table-th"></th>
                            <th scope="col" class="table-th">
                                {{$groupedZoneReports->where('week', 1)->sum('total_orders')}}
                            </th>
                            <th scope="col" class="table-th">
                                {{$groupedZoneReports->where('week', 2)->sum('total_orders')}}
                            </th>
                            <th scope="col" class="table-th">
                                {{$groupedZoneReports->where('week', 3)->sum('total_orders')}}
                            </th>
                            <th scope="col" class="table-th">
                                {{$groupedZoneReports->where('week', 4)->sum('total_orders')}}
                            </th>
                            </tfoot>
                    </tbody>

                </table>


                @if ($groupedZoneReports->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">There was no data found for
                                    this month</h2>
                                <p class="card-text">Try changing the filters for this view.
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif

            </div>
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
</div>
