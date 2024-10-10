<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

        <div class="flex justify-between">
            <div>
                <b>{{ $customer->name }}</b><iconify-icon class="ml-3" style="position: absolute" wire:loading
                    wire:target="changeSection" icon="svg-spinners:180-ring"></iconify-icon>
            </div>
            <div class="float-right grid-col-2">
                <button wire:click='showEditCustomerSection' class="btn inline-flex justify-center btn-outline-light btn-sm">Edit
                    info</button>
            </div>
        </div>
        <div class="card-body flex flex-col col-span-2" wire:ignore>
            <div class="card-text h-full">
                <div>
                    <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0"
                        id="tabs-tab" role="tablist">
                        <li class="nav-item" role="presentation" wire:click="changeSection('profile')">
                            <a href="#tabs-profile-withIcon"
                                class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'profile') active @endif dark:text-slate-300"
                                id="tabs-profile-withIcon-tab" data-bs-toggle="pill"
                                data-bs-target="#tabs-profile-withIcon" role="tab"
                                aria-controls="tabs-profile-withIcon" aria-selected="false">
                                <iconify-icon class="mr-1" icon="heroicons-outline:user"></iconify-icon>
                                Profile</a>
                        </li>
                        <li class="nav-item" role="presentation" wire:click="changeSection('pets')">
                            <a href="#tabs-messages-withIcon"
                                class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'pets') active @endif dark:text-slate-300"
                                id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                                data-bs-target="#tabs-messages-withIcon" role="tab"
                                aria-controls="tabs-messages-withIcon" aria-selected="false">
                                <iconify-icon class="mr-1" icon="mingcute:car-line"></iconify-icon>
                                Pets</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-5 mb-5 text-wrap">
            @if ($section === 'profile')
                <div class="md:flex-1 rounded-md overlay min-w-\[var\(500px\)\] sm:col-span-2"
                    style="min-width: 400px;">


                    <div class="card-body flex flex-col justify-center bg-cover card p-4 ">
                        <div class="card-text flex flex-col justify-between  menu-open">
                            <p>
                                <b>Customer info</b>
                            </p>
                            <br>

                            <p><b>Contact Phone</b></p>
                            <a href="">{{ $customer->phone ?? 'N/A' }}</a>
                            <br>
                        </div>
                    </div>

                    <div class="card-body flex flex-col justify-center bg-cover card p-4 mt-4">
                        <div class="card-text flex flex-col justify-between  menu-open">
                            <p>
                                <b>Location info</b>
                            </p>
                            <br>

                            <p><b>Zone</b></p>
                            <p>{{ $customer->zone->name ?? 'N/A' }}</p>
                            <br>

                            <p><b>Location URL</b></p>
                            <a class="clickable-link" target="_blank"
                                href="{{ $customer->location_url ?? 'N/A' }}">{{ $customer->location_url ?? 'N/A' }}</a>
                            <br>

                            <p><b>Address</b></p>
                            <a href="">{{ $customer->address ?? 'N/A' }}</a>
                            <br>
                        </div>
                    </div>


                </div>



                <div class="md:flex-1 rounded-md overlay min-w-[310px] sm:col-span-2">

                    {{-- note section --}}
                    {{-- <div
                        class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                        <div class="card-text flex flex-col justify-between h-full menu-open">
                            <p>
                                <b>Note</b>
                                <span class="float-right cursor-pointer text-slate-500" wire:click="openEditNote">
                                    <iconify-icon icon="material-symbols:edit-outline"></iconify-icon>
                                </span>
                            </p>
                            <p class="text-wrap">{{ $customer->name }}</p>
                        </div>
                    </div> --}}

                </div>
            @endif

            @if ($section === 'pets')
                <div class="md:flex-1 rounded-md overlay md:col-span-2" style="min-width: 400px;">
                    <div class="flex-1 rounded-md col-span-2">
                        <div
                            class="card-body flex flex-col justify-center  bg-no-repeat bg-center bg-cover card p-4 active">
                            <div class="card-text flex flex-col justify-between h-full menu-open">
                                <p class="mb-2">
                                    <b>Owned Pets ({{ $customer->pets->count() }})</b>

                                </p>

                                @if ($customer->pets->isEmpty())
                                    <p class="text-center m-5 text-primary">No pets added to this customer.</p>
                                @else
                                    @foreach ($customer->pets as $pet)
                                        <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0 mb-5"
                                            style="border-color:rgb(224, 224, 224)">
                                            <div class="break-words flex items-center my-1 m-4">

                                                <h3 class="text-base capitalize py-3">
                                                    <ul class="m-0 p-0 list-none">
                                                        <li
                                                            class="inline-block relative top-[3px] text-base font-Inter ">
                                                            {{ $pet->name }}
                                                            <iconify-icon icon="heroicons-outline:chevron-right"
                                                                class="relative text-slate-500 text-sm rtl:rotate-180"></iconify-icon>
                                                        </li>
                                                    </ul>
                                                </h3>

                                                <div class="ml-auto">
                                                    <div class="relative">
                                                        <div class="dropdown relative">
                                                            <button class="text-xl text-center block w-full "
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <iconify-icon
                                                                    icon="heroicons-outline:dots-vertical"></iconify-icon>
                                                            </button>
                                                            <ul
                                                                class=" dropdown-menu min-w-[120px] absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                                                <li>
                                                                    <button
                                                                        wire:click="editThisPet({{ $pet->id }})"
                                                                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4  w-full text-left py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                                        Edit</button>
                                                                </li>
                                                                <li>
                                                                    <button wire:click="removePet({{ $pet->id }})"
                                                                        class="text-slate-600 dark:text-white block font-Inter text-left font-normal w-full px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                                        Remove</button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr><br>
                                            <div class="grid grid-cols-2 mb-4">
                                                <div class="border-r ml-5">
                                                    <p><b>Type</b></p>
                                                    <p class=" flex items-center">
                                                        @if ($pet->type === \App\Models\Pets\Pet::TYPE_DOG)
                                                            <iconify-icon icon="mdi:dog" width="1.2em" height="1.2em"
                                                                class="mr-1"></iconify-icon>
                                                        @elseif($pet->type === \App\Models\Pets\Pet::TYPE_CAT)
                                                            <iconify-icon icon="mdi:cat" width="1.2em" height="1.2em"
                                                                class="mr-1"></iconify-icon>
                                                        @endif
                                                        {{ ucwords($pet->type) }}
                                                    </p>
                                                </div>
                                                <div class="ml-5">
                                                    <p class="mr-2"><b>Age </b></p>
                                                    <p>
                                                        <span
                                                            class="text-success-500"><b>{{ \Carbon\Carbon::parse($pet->bdate)->diff(\Carbon\Carbon::now())->format('%y') }}</b></span>
                                                        YEAR
                                                        <span
                                                            class="text-success-500"><b>{{ \Carbon\Carbon::parse($pet->bdate)->diff(\Carbon\Carbon::now())->format('%m') }}</b></span>
                                                        MONTH
                                                        <span
                                                            class="text-success-500"><b>{{ \Carbon\Carbon::parse($pet->bdate)->diff(\Carbon\Carbon::now())->format('%d') }}</b></span>
                                                        DAY
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <button wire:click="showAddPetsSection"
                                    class="btn inline-flex justify-center btn-light rounded-[25px] btn-sm float-right">Add
                                    pet</button>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    @can('assign', App\Models\Pets\Pet::class)
        @if ($isOpenAddPetsSec)
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
                                    Create new user
                                </h3>
                                <button wire:click="closeAddPetsSection" type="button"
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

                                <header class="card-header cust-card-header noborder px-0">
                                    <iconify-icon wire:loading wire:target="searchPets" class="loading-icon text-lg"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                    <input type="text" class="form-control !pl-9 mr-1 basis-1/4"
                                        placeholder="Search Pets..." wire:model.live.debounce.400ms="searchPets">
                                </header>

                                <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                    <thead
                                        class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                        <tr>
                                            <th scope="col"
                                                class="table-th  flex items-center border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700"
                                                style="position: sticky; left: -25px;  z-index: 10;">
                                                Name
                                            </th>

                                            <th scope="col" class="table-th">Type</th>
                                            <th scope="col" class="table-th">Age </th>


                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                        @foreach ($All_pets as $single_pet)
                                            <tr>

                                                <td class="table-td flex items-center sticky-column bg-white dark:bg-slate-800 colomn-shadow"
                                                    style="position: sticky; left: -25px;  z-index: 10;">
                                                    <div class="checkbox-area">
                                                        <label class="inline-flex items-center cursor-pointer">
                                                            <input type="checkbox"
                                                                wire:model="selectedPets.{{ $single_pet->id }}"
                                                                value="{{ $single_pet->id }}" class="hidden">
                                                            <span
                                                                class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                                <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
                                                                    alt=""
                                                                    class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                        </label>
                                                    </div>
                                                    <a href=""> <span class="hover-underline">
                                                            <b>
                                                                {{ $single_pet->name }}
                                                            </b>
                                                        </span>
                                                    </a>

                                                </td>

                                                <td class="table-td">
                                                    <div class=" flex items-center">
                                                        @if ($single_pet->type === \App\Models\Pets\Pet::TYPE_DOG)
                                                            <iconify-icon icon="mdi:dog" width="1.2em" height="1.2em"
                                                                class="mr-1"></iconify-icon>
                                                        @elseif($single_pet->type === \App\Models\Pets\Pet::TYPE_CAT)
                                                            <iconify-icon icon="mdi:cat" width="1.2em" height="1.2em"
                                                                class="mr-1"></iconify-icon>
                                                        @endif
                                                        {{ ucwords($single_pet->type) }}
                                                    </div>
                                                </td>

                                                <td class="table-td">
                                                    <span
                                                        class="text-success-500"><b>{{ \Carbon\Carbon::parse($single_pet->bdate)->diff(\Carbon\Carbon::now())->format('%y') }}</b></span>
                                                    YEAR
                                                    <span
                                                        class="text-success-500"><b>{{ \Carbon\Carbon::parse($single_pet->bdate)->diff(\Carbon\Carbon::now())->format('%m') }}</b></span>
                                                    MONTH
                                                    <span
                                                        class="text-success-500"><b>{{ \Carbon\Carbon::parse($single_pet->bdate)->diff(\Carbon\Carbon::now())->format('%d') }}</b></span>
                                                    DAY
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="assignPets" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="assignPets">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="assignPets"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    {{-- @can('update', $customer) --}}
    @if ($EditCustomerSection)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            style="display: block" style="z-index: 999999999999999;" tabindex="-1"
            aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                Edit Customer
                            </h3>
                            <button wire:click="closeEditCustomerSection" type="button"
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

                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="editCustomer" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="editCustomer">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="editCustomer"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- @endcan --}}

</div>
