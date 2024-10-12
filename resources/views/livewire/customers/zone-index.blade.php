<div>


    <div class="space-y-5 profile-page mx-auto" style="max-width: 800px">
        <div class="flex justify-between flex-wrap items-center">
            <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
                <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                    Zones
                </h4>
            </div>
            <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
                @can('create', App\Models\Customers\Zone::class)
                    <button wire:click="openNewZoneSec"
                        class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                        Add zone
                    </button>
                @endcan

            </div>
        </div>
        <div class="card">
            <header class="card-header cust-card-header noborder">
                <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                    icon="line-md:loading-twotone-loop"></iconify-icon>
                <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                    wire:model.live.debounce.400ms="search">
            </header>

            <div class="card-body px-6 pb-6  overflow-x-auto">
                <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col"
                                    class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model.live="selectAll" class="hidden"
                                                id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="assets/images/icon/ck-white.svg" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    Name
                                </th>
                                @if ($selectAll)
                                    @if ($selectedAllZones)
                                        <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                                icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                            {{ count($selectedZones) }} zone selected ..
                                            <span class="clickable-link" wire:click='undoSelectAllZones'>Undo</span>
                                        </th>
                                    @else
                                        <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                                icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                            {{ count($selectedZones) }} zone
                                            selected .. <span class="clickable-link" wire:click='selectAllZones'>Select
                                                All Zones</span></th>
                                    @endif
                                @else
                                    <th scope="col" class="table-th">Delivery Rate</th>
                                    <th scope="col" class="table-th">Action</th>
                                @endif

                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                            @foreach ($zones as $zone)
                                <tr>

                                    <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                        style="position: sticky; left: -25px;  z-index: 10;">
                                        <div class="checkbox-area">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" wire:model="selectedZones"
                                                    value="{{ $zone->id }}" class="hidden" id="select-all">
                                                <span
                                                    class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                    <img src="assets/images/icon/ck-white.svg" alt=""
                                                        class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                            </label>
                                        </div>
                                        <a href=""> <span class="hover-underline">
                                                <b>
                                                    {{ $zone->name }}
                                                </b>
                                            </span>
                                        </a>
                                    </td>

                                    <td class="table-td">
                                        EGP {{ $zone->delivery_rate }}
                                    </td>

                                    <td>
                                        @can('update', $zone)
                                        <div class="dropstart relative">
                                            <button class="inline-flex justify-center items-center" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                                    icon="heroicons-outline:dots-vertical"></iconify-icon>
                                            </button>
                                            <ul
                                                class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                                    <li wire:click="updateThisZone({{ $zone ->id }})">
                                                        <span
                                                            class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                                            <span>Edit</span>
                                                        </span>
                                                    </li>
                                            </ul>
                                        </div>
                                        @endcan
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


    @can('create', App\Models\Customers\Zone::class)
        @if ($newZoneSection)
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
                                    Create new zone
                                </h3>
                                <button wire:click="closeNewZoneSec" type="button"
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
                                        <label for="name" class="form-label">Name</label>
                                        <input id="name" type="text"
                                            class="form-control @error('name') !border-danger-500 @enderror"
                                            wire:model.lazy="name" autocomplete="off">
                                    </div>
                                    @error('name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="deliveryRate" class="form-label">Delivery rate</label>
                                        <input id="deliveryRate" type="number"
                                            class="form-control @error('deliveryRate') !border-danger-500 @enderror"
                                            wire:model.lazy="deliveryRate" autocomplete="off">
                                    </div>
                                    @error('deliveryRate')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="addNewZone" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addNewZone">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addNewZone"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @can('update', $updatedZone)
        @if ($updatedZone)
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
                                    Update zone
                                </h3>
                                <button wire:click="closeUpdateZoneSec" type="button"
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
                                        <label for="name" class="form-label">Name</label>
                                        <input id="name" type="text"
                                            class="form-control @error('name') !border-danger-500 @enderror"
                                            wire:model.lazy="name" autocomplete="off">
                                    </div>
                                    @error('name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="deliveryRate" class="form-label">Delivery rate</label>
                                        <input id="deliveryRate" type="number"
                                            class="form-control @error('deliveryRate') !border-danger-500 @enderror"
                                            wire:model.lazy="deliveryRate" autocomplete="off">
                                    </div>
                                    @error('deliveryRate')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="updateZone" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateZone">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateZone"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan


</div>
