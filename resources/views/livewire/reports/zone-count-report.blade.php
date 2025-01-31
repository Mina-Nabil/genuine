<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 800px">

        <div class="flex justify-between flex-wrap items-center">

            <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
                <div class=column>
                    <h4
                        class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                        Zone Count Report
                    </h4>
                </div>
            </div>
        </div>

        <div class="card">
            <header class="card-header cust-card-header noborder">
                <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                    icon="line-md:loading-twotone-loop"></iconify-icon>
                <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                    wire:model.live.debounce.400ms="search">
            </header>

            <header class="card-header cust-card-header noborder">
                <div>
                    @if ($creation_date_from || $creation_date_to)
                        <span class="badge bg-slate-900 text-white capitalize">
                            <span class="cursor-pointer" wire:click='openFilterCreationDate'>
                                <span class="text-secondary-500 ">Customer Creation Date:</span>&nbsp;
                                {{ $creation_date_from ? 'From ' . \Carbon\Carbon::parse($creation_date_from)->format('l, F j, Y') : '' }}
                                @if ($creation_date_from && $creation_date_to)
                                    -
                                @endif
                                {{ $creation_date_to ? 'To ' . \Carbon\Carbon::parse($creation_date_to)->format('l, F j, Y') : '' }}
                            </span>
                        </span>
                    @endif
                </div>
            </header>

            <div class="card-body px-6 pb-6  overflow-x-auto">
                <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col"
                                    class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                    Zone
                                </th>
                                <th scope="col" class="table-th">Customers Count</th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                            @foreach ($zones as $zone)
                                <tr class="even:bg-slate-100 dark:even:bg-slate-700">

                                    <td class="table-td">
                                        <b>
                                            {{ $zone->zone_name }}
                                        </b>
                                        </span>
                                    </td>

                                    <td class="table-td">
                                        {{ $zone->customers_count }}
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                    @if ($zones->isEmpty())
                        {{-- START: empty filter result --}}
                        <div class="card m-5 p-5">
                            <div class="card-body rounded-md bg-white dark:bg-slate-800">
                                <div class="items-center text-center p-5">
                                    <h2>
                                        <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                    </h2>
                                    <h2 class="card-title text-slate-900 dark:text-white mb-3">No zones with the
                                        applied
                                        filters</h2>
                                    <p class="card-text">Try changing the filters or search terms for this view.
                                    </p>
                                    <a href="{{ url('/zones') }}"
                                        class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                        all zones</a>
                                </div>
                            </div>
                        </div>
                        {{-- END: empty filter result --}}
                    @endif
                </div>
            </div>
            <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                {{ $zones->links('vendor.livewire.simple-bootstrap') }}
            </div>

        </div>
    </div>
    @if ($Edited_creation_date_from_sec)
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
                                Filter by Customer Creation Date
                            </h3>
                            <button wire:click="closeFilterCreationDate" type="button"
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
                            <div class="form-group">
                                <label for="edited_creation_date_from" class="form-label">Creation Date From</label>
                                <input type="date" id="edited_creation_date_from" class="form-control"
                                    wire:model="edited_creation_date_from">
                            </div>

                            <div class="form-group mt-4">
                                <label for="edited_creation_date_to" class="form-label">Creation Date To</label>
                                <input type="date" id="edited_creation_date_to" class="form-control"
                                    wire:model="edited_creation_date_to">
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilterCreationDate" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilterCreationDate">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilterCreationDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

</div>
