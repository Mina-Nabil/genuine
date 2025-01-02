<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Customers
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Customers\Customer::class)
                <button wire:click="openNewCustomerSec"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm">
                    Add customer
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
                                @if ($selectedAllCustomers)
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon> A
                                        {{ count($selectedCustomers) }} customer selected ..
                                        <span class="clickable-link" wire:click='undoSelectAllCustomers'>Undo</span>
                                    </th>
                                @else
                                    <th colspan="4" class="table-th"><iconify-icon style="vertical-align: top;"
                                            icon="lucide:info" width="1.2em" height="1.2em"></iconify-icon>
                                        {{ count($selectedCustomers) }} customer
                                        selected .. <span class="clickable-link" wire:click='selectAllCustomers'>Select
                                            All Customers</span></th>
                                @endif
                            @else
                                <th scope="col" class="table-th">Phone</th>
                                <th scope="col" class="table-th">Zone</th>
                                <th scope="col" class="table-th">Address</th>
                                <th scope="col" class="table-th">Location</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                        @foreach ($customers as $customer)
                        <tr class="even:bg-slate-100 dark:even:bg-slate-700">

                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                    style="position: sticky; left: -25px;  z-index: 10;">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="selectedCustomers"
                                                value="{{ $customer->id }}" class="hidden" id="select-all">
                                            <span
                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                <img src="assets/images/icon/ck-white.svg" alt=""
                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                        </label>
                                    </div>
                                    <a href="{{ route('customer.show', $customer->id) }}"> <span
                                            class="hover-underline">
                                            <b>
                                                {{ $customer->name }}
                                            </b>
                                        </span>
                                    </a>

                                </td>


                                <td class="table-td">
                                    {{ $customer->phone }}
                                </td>

                                <td class="table-td">
                                    {{ $customer->zone?->name }}
                                </td>

                                <td class="table-td text-start overflow-hidden text-ellipsis whitespace-nowrap">
                                    <p title="{{ $customer->address }}"
                                        class="text-sm text-slate-600 dark:text-slate-300 overflow-hidden text-ellipsis whitespace-nowrap"
                                        style="max-width:300px">
                                        {{ $customer->address }}
                                    </p>
                                </td>

                                <td class="table-td">
                                    @if ($customer->location_url)
                                        <div
                                            class="flex items-center space-x-2 text-xs font-normal text-primary-600 dark:text-slate-300 rtl:space-x-reverse">
                                            <iconify-icon icon="dashicons:location"></iconify-icon>
                                            <a target="_blank" href="{{ $customer->location_url }}">Location</a>
                                        </div>
                                    @endif
                                </td>


                            </tr>
                        @endforeach

                    </tbody>

                </table>


                @if ($customers->isEmpty())
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
                                <a href="{{ url('/customers') }}"
                                    class="btn inline-flex justify-center mx-2 mt-3 btn-primary active btn-sm">View
                                    all customers</a>
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



    @can('create', App\Models\Customers\Customer::class)
        @if ($newCustomerSection)
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
                                    Create new customer
                                </h3>
                                <button wire:click="closeNewCustomerSec" type="button"
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
                                        <label for="fullName" class="form-label">Full name</label>
                                        <input id="fullName" type="text"
                                            class="form-control @error('fullName') !border-danger-500 @enderror"
                                            wire:model.lazy="fullName" autocomplete="off">
                                    </div>
                                    @error('fullName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="from-group">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                        <div class="input-area">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input id="phone" type="text"
                                                class="form-control @error('phone') !border-danger-500 @enderror"
                                                wire:model="phone" autocomplete="off">
                                        </div>
                                        <div class="input-area">
                                            <label for="zone_id" class="form-label">Zone</label>
                                            <select name="zone_id" id="zone_id"
                                                class="form-control w-full mt-2 @error('zone_id') !border-danger-500 @enderror"
                                                wire:model="zone_id" autocomplete="off">
                                                <option value="">None</option>
                                                @foreach ($ZONES as $SINGLE_ZONE)
                                                    <option value="{{ $SINGLE_ZONE->id }}">
                                                        {{ ucwords(str_replace('_', ' ', $SINGLE_ZONE->name)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @error('phone')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                    @error('zone')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="locationUrl" class="form-label">Location URL</label>
                                        <input id="locationUrl" type="text"
                                            class="form-control @error('locationUrl') !border-danger-500 @enderror"
                                            wire:model="locationUrl" autocomplete="off">
                                    </div>
                                    @error('locationUrl')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea id="address" type="text" class="form-control @error('address') !border-danger-500 @enderror"
                                            wire:model="address" autocomplete="off"></textarea>
                                    </div>
                                    @error('address')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Pet Section -->
                                {{-- <div>
                                    <h3 class="text-lg font-medium mb-4 flex justify-between"><span><b>Pets</b></span> <button type="button" wire:click="addPet" class="btn btn-dark btn-sm mt-2">
                                        Add Pet
                                    </button></h3>
                                    @foreach ($pets as $index => $pet)
                                        <div class="border p-4 rounded-md mb-4">
                                            <h6 class="font-semibold mb-2">Pet {{ $index + 1 }}</h6>
                                            <hr class="mb-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="input-area">
                                                    <label for="petCategory{{ $index }}"
                                                        class="form-label">Category</label>
                                                    <select id="petCategory{{ $index }}"
                                                        class="form-control @error('pets.' . $index . '.category') !border-danger-500 @enderror"
                                                        wire:model.live="pets.{{ $index }}.category">
                                                        @foreach ($PET_CATEGORIES as $PET_CATEGORY)
                                                            <option value="{{ $PET_CATEGORY }}">
                                                                {{ ucwords($PET_CATEGORY) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('pets.' . $index . '.category')
                                                        <span
                                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="input-area">
                                                    <label for="petType{{ $index }}"
                                                        class="form-label">Type</label>
                                                    <input id="petType{{ $index }}"
                                                        list="petTypeList{{ $index }}" type="text"
                                                        class="form-control @error('pets.' . $index . '.type') !border-danger-500 @enderror"
                                                        wire:model.live="pets.{{ $index }}.type"
                                                        autocomplete="off">
                                                    <datalist id="petTypeList{{ $index }}">
                                                        @foreach ($pets[$index]['types'] as $type)
                                                            <option value="{{ ucwords($type) }}"></option>
                                                        @endforeach
                                                    </datalist>
                                                    @error('pets.' . $index . '.type')
                                                        <span
                                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="from-group mt-2">
                                                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
                                                    <div class="input-area">
                                                        <label for="pet_years" class="form-label">Age Years</label>
                                                        <input id="pet_years" type="number"
                                                            class="form-control @error('pets.' . $index . 'pet_years') !border-danger-500 @enderror"
                                                            wire:model="pets.{{ $index }}.pet_years">
                                                        @error('pets.' . $index . 'pet_years')
                                                            <span
                                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="input-area">
                                                        <label for="pet_months" class="form-label">Months</label>
                                                        <input id="pet_months" type="number" 
                                                            class="form-control @error('pets.' . $index . 'pet_months') !border-danger-500 @enderror"
                                                            wire:model="pets.{{ $index }}.pet_months">
                                                        @error('pets.' . $index . 'pet_months')
                                                            <span
                                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="input-area">
                                                        <label for="pet_days" class="form-label">Days</label>
                                                        <input id="pet_days" type="number" 
                                                            class="form-control @error('pets.' . $index . 'pet_days') !border-danger-500 @enderror"
                                                            wire:model="pets.{{ $index }}.pet_days">
                                                        @error('pets.' . $index . 'pet_days')
                                                            <span
                                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4"> --}}
                                                {{-- <div class="input-area">
                                                    <label for="petBdate{{ $index }}"
                                                        class="form-label">Birthdate</label>
                                                    <input id="petBdate{{ $index }}" type="date"
                                                        class="form-control @error('pets.' . $index . '.bdate') !border-danger-500 @enderror"
                                                        wire:model.live="pets.{{ $index }}.bdate"
                                                        autocomplete="off">
                                                    @error('pets.' . $index . '.bdate')
                                                        <span
                                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                                    @enderror
                                                </div> 

                                                <div class="input-area">
                                                    <label for="petName{{ $index }}"
                                                        class="form-label">Name</label>
                                                    <input id="petName{{ $index }}" type="text"
                                                        class="form-control @error('pets.' . $index . '.name') !border-danger-500 @enderror"
                                                        wire:model.live="pets.{{ $index }}.name"
                                                        autocomplete="off">
                                                    @error('pets.' . $index . '.name')
                                                        <span
                                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                            <div class="flex justify-end">
                                                <button wire:click="removePet({{ $index }})"
                                                    class="btn inline-flex justify-center btn-outline-danger btn-sm mt-4">Remove
                                                    Pet</button>
                                            </div>

                                        </div>
                                    @endforeach

                                    
                                </div> --}}




                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                    <button wire:click="addNewCustomer" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addNewCustomer">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addNewCustomer"
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
