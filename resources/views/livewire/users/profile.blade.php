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
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
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
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
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
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">Passwords do not match</span>
                                        @endif
                                        @error('newPasswordConfirm')
                                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
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
                                        wire:loading wire:target="editPassword"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
