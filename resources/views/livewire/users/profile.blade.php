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
                                <iconify-icon class="leading-none text-xl"
                                    icon="ic:round-keyboard-arrow-down"></iconify-icon>
                            </span>
                        </button>
                        <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                            data-popper-placement="bottom-start"
                            style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 50px);">
                            <li>
                                <a href="#"
                                    class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                            dark:hover:text-white">
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
                            <img @if ($userImage) src="{{ $userImage->temporaryUrl() }}"
                            @elseif ($OLDuserImage) src="{{ $this->userImage }}"   
                            @else src="{{ asset('assets/images/users/user-1.png') }}" @endif
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
                                        icon="mdi:remove"></iconify-icon>
                                    <iconify-icon wire:loading wire:target="userImage,clearImage"
                                        icon="svg-spinners:ring-resize"></iconify-icon>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-2xl font-medium text-slate-900 dark:text-slate-200 mb-[3px]">
                            {{ auth()->user()->full_name }}
                        </div>
                        <div class="text-sm font-light text-slate-600 dark:text-slate-400">
                            {{ ucwords(auth()->user()->type) }}
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
                                <input type="email"
                                    class="form-control @error('email') !border-danger-500 @enderror"
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
                            @if ($uploadIDFile)
                                <span class="float-right cursor-pointer" wire:click='clearIDdocFile'>clear</span>
                            @endif
                        </div>

                        @if ($OLDuploadIDFile)
                            <button class="btn inline-flex justify-center btn-dark btn-sm"
                                wire:click="downloadIDDocument">Download ID</button>
                        @elseif($uploadIDFile)
                            <img src="{{ $uploadIDFile[0]->temporaryUrl() }}"
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
                                <input name="file" id="fileInput" type="file" class="dropzone dropzone-input"
                                    multiple wire:model="uploadIDFile" />
                            </div>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

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
                            @if ($uploadIDFile)
                                <span class="float-right cursor-pointer" wire:click='clearLicDocFile'>clear</span>
                            @endif
                        </div>

                        @if ($OLDuploadIDFile)
                            <button class="btn inline-flex justify-center btn-dark btn-sm"
                                wire:click="downloadIDDocument">Download ID</button>
                        @elseif($uploadIDFile)
                            <img src="{{ $uploadIDFile[0]->temporaryUrl() }}"
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
                                <input name="file" id="fileInput" type="file" class="dropzone dropzone-input"
                                    multiple wire:model="uploadIDFile" />
                            </div>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

        @if ($is_open_update_pass)
            <div>
                <div class="card h-full">
                    <header class="card-header">
                        <h4 class="card-title">Security</h4>
                    </header>
                    <form wire:submit.prevent="changePassword">
                        <div class="card-body p-6">
                            <ul class="list space-y-8">
                                <li class="flex space-x-3 rtl:space-x-reverse">
                                    <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                        <iconify-icon icon="solar:lock-password-broken"></iconify-icon>
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                            Current Password
                                        </div>
                                        <input type="password"
                                            class="form-control @error('currentPassword') !border-danger-500 @enderror"
                                            wire:model="currentPassword">
                                        @error('currentPassword')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </li>
                                <li class="flex space-x-3 rtl:space-x-reverse">
                                    <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                        <iconify-icon icon="solar:lock-password-bold-duotone"></iconify-icon>
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                            New Password
                                        </div>
                                        <input type="password"
                                            class="form-control @error('newPassword') !border-danger-500 @enderror"
                                            wire:model="newPassword">
                                        @error('newPassword')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror

                                    </div>
                                </li>
                                {{-- <li class="flex space-x-3 rtl:space-x-reverse">
                                    <div class="flex-none text-2xl text-slate-600 dark:text-slate-300">
                                        <iconify-icon icon="solar:lock-password-bold-duotone"></iconify-icon>
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="uppercase text-xs text-slate-500 dark:text-slate-300 mb-1 leading-[12px]">
                                            Confirm Password
                                        </div>
                                        <input type="password" class="form-control" wire:model="password_confirmation">
                                    </div>
                                </li> --}}
                                <!-- end single list -->
                                <li class="flex space-x-3 rtl:space-x-reverse float-right">
                                    <button class="btn inline-flex justify-center btn-success" type="submit">Change
                                        Password</button>
                                </li>
                                <!-- end single list -->
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
