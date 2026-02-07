<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Pets
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Pets\Pet::class)
                <button wire:click="openNewPetSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                    Add Pet
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
                                            <img src="{{ asset("assets/images/icon/ck-white.svg") }}" alt=""
                                                class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    </label>
                                </div>
                                Name
                            </th>
                            @if ($selectAll)
                                @if ($selectedAllPets)
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedPets) }} pet selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllPets'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedPets) }} pet
                                        selected .. <span class="clickable-link" wire:click='selectAllPets'>Select
                                            All Pets</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">Type</th>
                                <th scope="col" class="table-th">Age </th>
                                <th scope="col" class="table-th">Owner</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($pets as $pet)
                            <tr>

                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="selectedPets" value="{{ $pet->id }}"
                                                class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="{{ asset("assets/images/icon/ck-white.svg") }}" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href=""> <span class="hover-underline">
                                            <b>
                                                {{ $pet->name }}
                                            </b>
                                        </span>
                                    </a>

                                </td>

                                <td class="table-td">
                                    <div class=" flex items-center">
                                        @if ($pet->type === \App\Models\Pets\Pet::TYPE_DOG)
                                            <iconify-icon icon="mdi:dog" width="1.2em" height="1.2em"
                                                class="mr-1"></iconify-icon>
                                        @elseif($pet->type === \App\Models\Pets\Pet::TYPE_CAT)
                                            <iconify-icon icon="mdi:cat" width="1.2em" height="1.2em"
                                                class="mr-1"></iconify-icon>
                                        @endif
                                        {{ ucwords($pet->type) }}
                                    </div>
                                </td>

                                <td class="table-td">
                                    <span
                                        class="text-success-500"><b>{{ \Carbon\Carbon::parse($pet->bdate)->diff(\Carbon\Carbon::now())->format('%y') }}</b></span>
                                    YEAR
                                    <span
                                        class="text-success-500"><b>{{ \Carbon\Carbon::parse($pet->bdate)->diff(\Carbon\Carbon::now())->format('%m') }}</b></span>
                                    MONTH
                                    <span
                                        class="text-success-500"><b>{{ \Carbon\Carbon::parse($pet->bdate)->diff(\Carbon\Carbon::now())->format('%d') }}</b></span>
                                    DAY
                                </td>

                                <td class="table-td">
                                    {{ $pet->customer->name }}
                                </td>



                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @if ($pets->isEmpty())
                    {{-- START: empty filter result --}}
                    <div class="card m-5 p-5">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800">
                            <div class="items-center text-center p-5">
                                <h2>
                                    <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                                </h2>
                                <h2 class="card-title text-slate-900 dark:text-white mb-3">No pets with the
                                    applied
                                    filters</h2>
                                <p class="card-text">Try changing the filters or search terms for this view.
                                </p>
                                <a href="{{ url('/pets') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all pets</a>
                            </div>
                        </div>
                    </div>
                    {{-- END: empty filter result --}}
                @endif


            </div>


        </div>
        <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
            {{ $pets->links('vendor.livewire.simple-bootstrap') }}
        </div>

    </div>



    @can('create', App\Models\Pets\Pet::class)
        @if ($newPetSection)
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
                                    Add new pet
                                </h3>
                                <button wire:click="closeNewPetSec" type="button"
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
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="bdate" class="form-label">Birth date</label>
                                            <input id="bdate" type="date"
                                                class="form-control @error('bdate') !border-danger-500 @enderror"
                                                wire:model="bdate" autocomplete="off">
                                        </div>
                                        <div class="input-area">
                                            <label for="type" class="form-label">Type</label>
                                            <select name="type" id="type"
                                                class="form-control w-full mt-2 @error('type') !border-danger-500 @enderror"
                                                wire:model="type" autocomplete="off">
                                                <option value="">None</option>
                                                @foreach ($PET_TYPES as $PET_TYPE)
                                                    <option value="{{ $PET_TYPE }}">
                                                        {{ ucwords(str_replace('_', ' ', $PET_TYPE)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @error('bdate')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror

                                    @error('type')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="customerID" class="form-label">Customer</label>
                                        @if ($selectedCustomer)
                                            <badge class="btn inline-flex justify-between btn-outline-dark btn-sm mt-2"
                                                style="width: 100%">
                                                <span class="flex items-center">
                                                    <span>{{ $selectedCustomer->name . ' • ' . $selectedCustomer->phone }}</span>
                                                </span>
                                                <span wire:click="clearCustomer" class="clickable-link">
                                                    Remove
                                                </span>
                                            </badge>
                                        @else
                                            <input type="text" class="form-control @error('selectedCustomer.id') !border-danger-500 @enderror" wire:model.live="searchCustomers"
                                                placeholder="Search Customers...">
                                            @if ($fetchedCustomers)
                                                @foreach ($fetchedCustomers as $singleCustomer)
                                                    <button
                                                        class="btn inline-flex justify-between btn-outline-primary btn-sm mt-2"
                                                        style="width: 100%"
                                                        wire:click="selectCustomer({{ $singleCustomer->id }})">
                                                        <span class="flex items-center">
                                                            <span>{{ $singleCustomer->name . ' • ' . $singleCustomer->phone }}</span>
                                                        </span>
                                                        <span>
                                                            Select
                                                        </span>
                                                    </button>
                                                @endforeach
                                            @endif
                                        @endif
                                        @error('selectedCustomer.id')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror

                                    </div>

                                </div>


                                <script></script>


                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="addNewPet" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addNewPet">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addNewPet"
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
