<div>

    @if ($changes)
        <div class="app-header z-[999] bg-white dark:bg-slate-800 shadow dark:shadow-slate-700 save-section">
            <div class="flex justify-between items-center h-full  float-right">
                <div class="flex items-center md:space-x-4 space-x-2 xl:space-x-0 rtl:space-x-reverse vertical-box">
                    <button type="submit" wire:click="saveInfo"
                        class="btn inline-flex justify-center  btn-light btn-sm mr-3">Save</button>
                </div>
                <!-- end nav tools -->
            </div>
        </div>
    @endif

    <div class="space-y-5 profile-page mx-auto" style="max-width: 800px">
        <div
            class="profiel-wrap px-[35px] pb-10 md:pt-[84px] pt-10 rounded-lg bg-white dark:bg-slate-800 lg:flex lg:space-y-0
                space-y-6 justify-between items-end relative z-[1]">
            <div
                class="bg-slate-900 dark:bg-slate-700 absolute left-0 top-0 md:h-1/2 h-[150px] w-full z-[-1] rounded-t-lg p-5">
                <div>
                    <div class="dropdown relative ">
                        <button
                            class="btn inline-flex justify-center btn-light items-center cursor-default relative !pr-14 btn-sm float-right"
                            type="button" id="lightsplitDropdownMenuButton" data-bs-toggle="dropdown"
                            aria-expanded="true">
                            More Actions
                            <span
                                class="cursor-pointer absolute ltr:border-l rtl:border-r border-slate-100 h-full ltr:right-0 rtl:left-0 px-2 flex
                                        items-center justify-center leading-none">
                                <iconify-icon class="leading-none text-xl" icon="ic:round-keyboard-arrow-down">
                                </iconify-icon>
                            </span>
                        </button>
                        <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                            data-popper-placement="bottom-start"
                            style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 50px);">
                            <li class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                            dark:hover:text-white cusor-pointer"
                                wire:click="openChangePass">

                                Change Password</a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="profile-box flex-none md:text-start text-center">
                <div class="md:flex items-end md:space-x-6 rtl:space-x-reverse">
                    <div class="flex-none">
                        <div
                            class="md:h-[186px] md:w-[186px] h-[140px] w-[140px] md:ml-0 md:mr-0 ml-auto mr-auto md:mb-0 mb-4 rounded-full ring-4
                                ring-slate-100 relative">
                            <img @if ($userImage) src="{{ $userImage->temporaryUrl() }}" @elseif ($OLDuserImage)
                                src="{{ $this->userImage }}" @else src="{{ asset('assets/images/users/user-1.png') }}" @endif
                                alt="" class="w-full h-full object-cover rounded-full">
                            @if (!$userImage)
                                <label for="userImage"
                                    class="absolute right-2 h-8 w-8 bg-slate-50 text-slate-600 rounded-full shadow-sm flex flex-col items-center justify-center md:top-[140px] top-[100px] cursor-pointer">
                                    <iconify-icon wire:loading.remove wire:target="userImage,clearImage"
                                        icon="heroicons:pencil-square"></iconify-icon>
                                    <iconify-icon wire:loading wire:target="userImage,clearImage"
                                        icon="svg-spinners:ring-resize"></iconify-icon>
                                </label>
                                <input wire:model="userImage" type="file" name="userImage" id="userImage"
                                    style="display:none" accept=".jpg, .jpeg, .png">
                            @else
                                <span wire:click="clearImage"
                                    class="absolute right-2 h-8 w-8 bg-slate-50 text-slate-600 rounded-full shadow-sm flex flex-col items-center justify-center md:top-[140px] top-[100px] cursor-pointer">
                                    <iconify-icon wire:loading.remove wire:target="userImage,clearImage"
                                        icon="mdi:remove">
                                    </iconify-icon>
                                    <iconify-icon wire:loading wire:target="userImage,clearImage"
                                        icon="svg-spinners:ring-resize"></iconify-icon>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-2xl font-medium text-slate-900 dark:text-slate-200 mb-[3px]">
                            {{ $user->full_name }}
                        </div>
                        <div class="text-sm font-light text-slate-600 dark:text-slate-400">
                            {{ ucwords($user->type) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div>
            <div class="card h-full">
                <header class="card-header flex justify-between">
                    <h4 class="card-title">Info</h4>
                </header>
                <div class="card-body p-6">
                    <ul class="list space-y-8">
                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                <iconify-icon icon="solar:user-broken"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    Username
                                </div>
                                <input type="text" wire:model.live="username"
                                    class="form-control @error('username') !border-danger-500 @enderror">
                                @error('username')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </li>
                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                <iconify-icon icon="icon-park-outline:edit-name"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    First Name
                                </div>
                                <input type="text"
                                    class="form-control @error('firstName') !border-danger-500 @enderror"
                                    wire:model.live="firstName">
                                @error('firstName')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    Last Name
                                </div>
                                <input type="text"
                                    class="form-control @error('lastName') !border-danger-500 @enderror"
                                    wire:model.live="lastName">
                                @error('lastName')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </li>

                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                <iconify-icon icon="heroicons:phone-arrow-up-right"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    PHONE
                                </div>
                                <input type="text" class="form-control @error('phone') !border-danger-500 @enderror"
                                    wire:model.live="phone">
                                @error('phone')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </li>
                        <!-- end single list -->
                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                <iconify-icon icon="heroicons:envelope"></iconify-icon>
                            </div>
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    Email
                                </div>
                                <input type="email" class="form-control @error('email') !border-danger-500 @enderror"
                                    wire:model.live="email">
                                @error('email')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </li>
                        <!-- end single list -->
                    </ul>
                </div>
            </div>
        </div>

        @if ($user->type === App\models\users\User::TYPE_DRIVER)
            <div>
                <div class="card">
                    <header class=" card-header noborder flex justify-bewteen">
                        <div>
                            <h4 class="card-title">Driver Shifts</h4>
                            <button wire:click='openUpdateDayFee' class="btn inline-flex items-center justify-center btn-dark btn-sm">
                                <span class="mr-3"> Day Fee </span>
                                <span class="inline-flex items-center justify-center bg-white text-slate-900 rounded-md font-Inter text-xs ltr:ml-1 rtl:mr-1 p-1">
                                    {{ number_format($user->driver_day_fees, 2) }}&nbsp;<small>EGP</small>
                                </span>
                            </button>
                        </div>
                        <button wire:click='openNewDriverSec'
                            class="btn inline-flex justify-center btn-outline-light btn-sm">Add Driver Shift</button>
                    </header>

                    <div class="card-body px-6 pb-6">
                        <div class="overflow-x-auto -mx-6">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden ">
                                    <table
                                        class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
                                        <thead class="bg-slate-800 dark:bg-slate-700 ">
                                            <tr>

                                                <th scope="col" class=" table-th text-slate-100">
                                                    <span class="text-slate-100">Shift Title</span>
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    <span class="text-slate-100">Time</span>
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    <span class="text-slate-100">Weight Limit</span>
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    <span class="text-slate-100">Order Limit</span>
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                    <span class="text-slate-100">Car</span>
                                                </th>

                                                <th scope="col" class=" table-th ">
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                            @forelse ($user->drivers as $shift)
                                                <tr>
                                                    <td class="table-td">
                                                        {{ $shift->shift_title }}
                                                        @if ($shift->is_available)
                                                            <span
                                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl ml-5">Available</span>
                                                        @endif

                                                    </td>
                                                    <td class="table-td">
                                                        <div class="flex-1 text-start flex justify-between">
                                                            <span>{{ $shift->start_time->format('h:i A') }}</span>
                                                            <span>-></span>
                                                            <span>{{ $shift->end_time->format('h:i A') }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="table-td">
                                                        @if ($shift->weight_limit)
                                                            {{ number_format($shift->weight_limit / 1000, 2) }}
                                                            <small>&nbsp;KG</small>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="table-td">
                                                        @if ($shift->order_quantity_limit)
                                                            {{ $shift->order_quantity_limit }}<small>&nbsp;Orders</small>
                                                        @else
                                                            -
                                                        @endif

                                                    </td>
                                                    <td class="table-td">
                                                        <div class="flex-1 text-start">
                                                            <h4
                                                                class="text-sm font-medium text-slate-600 whitespace-nowrap">
                                                                {{ $shift->car_model }}
                                                            </h4>
                                                            <div
                                                                class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                                                {{ $shift->car_type }}
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="table-td">
                                                        <div class="flex space-x-3 rtl:space-x-reverse">
                                                            <button
                                                                wire:click='openEditDriverSec({{ $shift->id }})'
                                                                class="action-btn" type="button">
                                                                <iconify-icon
                                                                    icon="heroicons:pencil-square"></iconify-icon>
                                                            </button>
                                                            <button
                                                                wire:click='openDeleteDriverConfirmation({{ $shift->id }})'
                                                                class="action-btn" type="button">
                                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    </td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="table-td " colspan="5">
                                                        <p class="text-center text-slate-500 p-5">
                                                            No driver shifts for this user.
                                                        </p>
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
            </div>
        @endif

        {{-- ID  --}}
        <div>
            <div class="card h-full">
                <header class="card-header flex items-center justify-between">
                    <h4 class="card-title flex items-center">
                        <iconify-icon icon="teenyicons:id-outline" class="mr-2"></iconify-icon>
                        ID
                    </h4>
                </header>

                <div class="card-body p-6">
                    <ul class="list space-y-4">
                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    ID Number
                                </div>
                                <input type="text" wire:model.live="idNumber"
                                    class="form-control @error('idNumber') !border-danger-500 @enderror">
                                @error('idNumber')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </li>

                        <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                            ID Document
                            <span class="float-right cursor-pointer" wire:click='clearIDdocFile'>clear</span>
                        </div>

                        @if ($OLDuploadIDFile)
                            <button class="btn inline-flex justify-center btn-dark btn-sm"
                                wire:click="downloadIDDocument">Download ID</button>
                        @elseif($uploadIDFile)
                            <img src="{{ $uploadIDFile->temporaryUrl() }}"
                                class="rounded-md border-4 border-slate-300 max-w-full w-full block" alt="image">
                        @else
                            <div class="border-dashed border dropzone-container cursor-pointer mt-2"
                                style="border-color: #aeaeae">
                                <p class="dropzone-para" wire:loading wire:target="uploadIDFile"
                                    style="font-size:20px">
                                    <iconify-icon icon="svg-spinners:tadpole"></iconify-icon>
                                </p>
                                <p class="dropzone-para" wire:loading.remove wire:target="uploadIDFile">Choose a
                                    file or drop it here...</p>
                                <input name="file" type="file" class="dropzone dropzone-input"
                                    wire:model="uploadIDFile" />
                            </div>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

        {{-- Drive Licence --}}
        <div>
            <div class="card h-full">
                <header class="card-header flex justify-between">
                    <h4 class="card-title flex items-center">
                        <iconify-icon icon="healthicons:truck-driver" class="mr-2"></iconify-icon>
                        Licence
                    </h4>
                </header>

                <div class="card-body p-6">
                    <ul class="list space-y-4">
                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    Licence Number
                                </div>
                                <input type="text" wire:model.live="driveLicienceNo"
                                    class="form-control @error('driveLicienceNo') !border-danger-500 @enderror">
                                @error('driveLicienceNo')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </li>

                        <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                            Licience Document
                            <span class="float-right cursor-pointer" wire:click='clearLicDocFile'>clear</span>
                        </div>

                        @if ($OLDuploadLicFile)
                            <button class="btn inline-flex justify-center btn-dark btn-sm"
                                wire:click="downloadLicDocument">Download Licence</button>
                        @elseif($uploadLicFile)
                            <img src="{{ $uploadLicFile->temporaryUrl() }}"
                                class="rounded-md border-4 border-slate-300 max-w-full w-full block" alt="image">
                        @else
                            <div class="border-dashed border dropzone-container cursor-pointer mt-2"
                                style="border-color: #aeaeae">
                                <p class="dropzone-para" wire:loading wire:target="uploadLicFile"
                                    style="font-size:20px">
                                    <iconify-icon icon="svg-spinners:tadpole"></iconify-icon>
                                </p>
                                <p class="dropzone-para" wire:loading.remove wire:target="uploadLicFile">Choose a
                                    file or drop it here...</p>
                                <input name="file" id="fileInput" type="file" class="dropzone dropzone-input"
                                    wire:model="uploadLicFile" />
                            </div>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

        {{-- Car Licence --}}
        <div>
            <div class="card h-full">
                <header class="card-header flex justify-between">
                    <h4 class="card-title flex items-center">
                        <iconify-icon icon="mdi:car-outline" class="mr-2"></iconify-icon>
                        Car Licence
                    </h4>
                </header>

                <div class="card-body p-6">
                    <ul class="list space-y-4">
                        <li class="flex space-x-3 rtl:space-x-reverse">
                            <div class="flex-1">
                                <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                    Car Licence Number
                                </div>
                                <input type="text" wire:model.live="carLicienceNo"
                                    class="form-control @error('carLicienceNo') !border-danger-500 @enderror">
                                @error('carLicienceNo')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </li>

                        <div class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                            Car Licience Document
                            <span class="float-right cursor-pointer" wire:click='clearCarLicDocFile'>clear</span>
                        </div>

                        @if ($OLDuploadCarLicFile)
                            <button class="btn inline-flex justify-center btn-dark btn-sm"
                                wire:click="downloadCarLicDocument">Download Licence</button>
                        @elseif($uploadCarLicFile)
                            <img src="{{ $uploadCarLicFile->temporaryUrl() }}"
                                class="rounded-md border-4 border-slate-300 max-w-full w-full block" alt="image">
                        @else
                            <div class="border-dashed border dropzone-container cursor-pointer mt-2"
                                style="border-color: #aeaeae">
                                <p class="dropzone-para" wire:loading wire:target="uploadCarLicFile"
                                    style="font-size:20px">
                                    <iconify-icon icon="svg-spinners:tadpole"></iconify-icon>
                                </p>
                                <p class="dropzone-para" wire:loading.remove wire:target="uploadCarLicFile">Choose a
                                    file or drop it here...</p>
                                <input name="file" id="fileInput" type="file" class="dropzone dropzone-input"
                                    wire:model="uploadCarLicFile" />
                            </div>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($is_open_update_pass)
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
                                    Change Password
                                </h3>

                                <button wire:click="closeChangePassSec" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    data-bs-dismiss="modal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                            11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <!-- Current Password Input -->
                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="oldPass" class="form-label">Current Password</label>
                                        <input id="oldPass" type="password"
                                            class="form-control @error('oldPass') !border-danger-500 @enderror"
                                            wire:model.live="oldPass">
                                        @error('oldPass')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- New Password Input -->
                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="password" class="form-label">New Password</label>
                                        <input id="password" type="password"
                                            class="form-control @error('password') !border-danger-500 @enderror @if (!$passwordsMatch && $newPasswordConfirm) !border-danger-500 @elseif($newPasswordConfirm) !border-success-500 @endif"
                                            wire:model.live="password">
                                        @error('password')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Confirm Password Input -->
                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="newPasswordConfirm" class="form-label">Confirm Password</label>
                                        <input id="newPasswordConfirm" type="password"
                                            class="form-control @if (!$passwordsMatch && $newPasswordConfirm) !border-danger-500 @elseif($newPasswordConfirm) !border-success-500 @endif"
                                            wire:model.live="newPasswordConfirm">
                                        @if (!$passwordsMatch && $newPasswordConfirm)
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">Passwords
                                                do not
                                                match</span>
                                        @endif
                                        @error('newPasswordConfirm')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="editPassword" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="editPassword">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="editPassword" icon="line-md:loading-twotone-loop">
                                    </iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        

    </div>

    @if ($is_open_update_day_fee)
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
                                    Update day delivery fees
                                </h3>

                                <button wire:click="closeUpdateDayFee" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    data-bs-dismiss="modal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                            11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="editedDayFee" class="form-label">Day fee</label>
                                        <input id="editedDayFee" wire:keydown.enter='updateDayFee' type="number"
                                            class="form-control @error('editedDayFee') !border-danger-500 @enderror"
                                            wire:model="editedDayFee">
                                        @error('editedDayFee')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="updateDayFee" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateDayFee">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateDayFee" icon="line-md:loading-twotone-loop">
                                    </iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @if ($isOpenNewDriverSec || $isEditDriverSec)
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
                                @if ($isOpenNewDriverSec)
                                    New Driver Shift
                                @else
                                    Edit Driver Shift
                                @endif

                            </h3>

                            <button wire:click="closeDriverSections" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                            11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <!-- Current Password Input -->
                            <div class="form-group">
                                <div class="input-area">
                                    <label for="shift_title" class="form-label">Shift title*</label>
                                    <input id="shift_title" type="text"
                                        class="form-control @error('shift_title') !border-danger-500 @enderror"
                                        wire:model.live="shift_title">
                                    @error('shift_title')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="from-group">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                    <div class="input-area">
                                        <label for="startTime" class="form-label">Start Time
                                        </label>
                                        <input id="startTime" type="time"
                                            class="form-control @error('startTime') !border-danger-500 @enderror"
                                            wire:model="startTime" autocomplete="off">
                                        @error('startTime')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="input-area">
                                        <label for="endTime" class="form-label">End Time</label>
                                        <input id="endTime" type="time"
                                            class="form-control @error('endTime') !border-danger-500 @enderror"
                                            wire:model="endTime" autocomplete="off">
                                        @error('endTime')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="weight_limit" class="form-label">Weight Limit (KG)</label>
                                        <input id="weight_limit" type="number"
                                            class="form-control @error('weight_limit') !border-danger-500 @enderror"
                                            wire:model.live="weight_limit">
                                        @error('weight_limit')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="order_quantity_limit" class="form-label">Orders Limit</label>
                                        <input id="order_quantity_limit" type="number"
                                            class="form-control @error('order_quantity_limit') !border-danger-500 @enderror"
                                            wire:model.live="order_quantity_limit">
                                        @error('order_quantity_limit')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="car_type" class="form-label">Car type
                                        </label>
                                        <select name="car_type" id="car_type"
                                            class="form-control w-full mt-2 @error('car_type') !border-danger-500 @enderror"
                                            wire:model="car_type" autocomplete="off">
                                            <option>None</option>
                                            @foreach ($carTypes as $OneCarType)
                                                <option value="{{ $OneCarType }}">
                                                    {{ ucwords(str_replace('_', ' ', $OneCarType)) }}</option>
                                            @endforeach
                                        </select>
                                        @error('car_type')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-area">
                                        <label for="car_model" class="form-label">Car model</label>
                                        <input id="car_model" type="text"
                                            class="form-control @error('car_model') !border-danger-500 @enderror"
                                            wire:model.live="car_model">
                                        @error('car_model')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @if ($isEditDriverSec)
                                <div class="form-group">
                                    <div class="input-area">
                                        <div class="checkbox-area">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input wire:model='is_available' type="checkbox" class="hidden"
                                                    name="checkbox" checked="checked">
                                                <span
                                                    class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                    <img src="{{ asset('assets/images/icon/ck-white.svg') }}"
                                                        alt=""
                                                        class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">
                                                    is Available ?</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>





                        <!-- Modal footer -->
                        @if ($isOpenNewDriverSec)
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="addDriver" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addDriver">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addDriver" icon="line-md:loading-twotone-loop">
                                    </iconify-icon>
                                </button>
                            </div>
                        @else
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="updateDriver" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateDriver">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateDriver" icon="line-md:loading-twotone-loop">
                                    </iconify-icon>
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($deleteDriverShiftId)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show"
            tabindex="-1" aria-labelledby="dangerModalLabel" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none">
                <div
                    class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding
                                rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-danger-500">
                            <h3 class="text-base font-medium text-white dark:text-white capitalize">
                                Delete Driver Shift
                            </h3>
                            <button wire:click="closeDeleteDriverConfirmation" type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center
                                            dark:hover:bg-slate-600 dark:hover:text-white"
                                data-bs-dismiss="modal">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                    11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            <h6 class="text-base text-slate-900 dark:text-white leading-6">
                                Are you sure ! you Want to <b><i>delete</i></b> this driver shift ?
                            </h6>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            <button wire:click="deleteDriverShift" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-danger-500">
                                <span wire:loading.remove wire:target="deleteDriverShift">Yes, Delete</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="deleteDriverShift"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
