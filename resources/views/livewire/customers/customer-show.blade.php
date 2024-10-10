<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

        <div>
            <b>{{ $customer->name }}</b><iconify-icon class="ml-3" style="position: absolute" wire:loading
                wire:target="changeSection" icon="svg-spinners:180-ring"></iconify-icon>
        </div>
        <div class="card-body flex flex-col col-span-2" wire:ignore>
            <div class="card-text h-full">
                <div>
                    <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0" id="tabs-tab"
                        role="tablist">
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
                <div class="md:flex-1 rounded-md overlay min-w-\[var\(500px\)\] md:col-span-2"
                    style="min-width: 400px;">
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
                                                                    <button
                                                                        wire:click="deleteThisPet({{ $pet->id }})"
                                                                        class="text-slate-600 dark:text-white block font-Inter text-left font-normal w-full px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                                        Delete</button>
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

                                <button wire:click="toggleAddCar"
                                    class="btn inline-flex justify-center btn-light rounded-[25px] btn-sm float-right">Add
                                    pet</button>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>



    </div>
</div>
