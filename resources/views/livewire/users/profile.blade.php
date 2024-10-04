<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 800px">
        <div
            class="profiel-wrap px-[35px] pb-10 md:pt-[84px] pt-10 rounded-lg bg-white dark:bg-slate-800 lg:flex lg:space-y-0
                space-y-6 justify-between items-end relative z-[1]">
            <div
                class="bg-slate-900 dark:bg-slate-700 absolute left-0 top-0 md:h-1/2 h-[150px] w-full z-[-1] rounded-t-lg">
            </div>
            <div class="profile-box flex-none md:text-start text-center">
                <div class="md:flex items-end md:space-x-6 rtl:space-x-reverse">
                    <div class="flex-none">
                        <div
                            class="md:h-[186px] md:w-[186px] h-[140px] w-[140px] md:ml-0 md:mr-0 ml-auto mr-auto md:mb-0 mb-4 rounded-full ring-4
                                ring-slate-100 relative">
                            <img src="assets/images/users/user-1.jpg" alt=""
                                class="w-full h-full object-cover rounded-full">
                            <a href="profile-setting"
                                class="absolute right-2 h-8 w-8 bg-slate-50 text-slate-600 rounded-full shadow-sm flex flex-col items-center
                                    justify-center md:top-[140px] top-[100px]">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </a>
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
            <!-- end profile box -->
            {{-- <div class="profile-info-500 md:flex md:text-start text-center flex-1 max-w-[516px] md:space-y-0 space-y-4">
                <div class="flex-1">
                    <div class="text-base text-slate-900 dark:text-slate-300 font-medium mb-1">
                        $32,400
                    </div>
                    <div class="text-sm text-slate-600 font-light dark:text-slate-300">
                        Total Balance
                    </div>
                </div>
                <!-- end single -->
                <div class="flex-1">
                    <div class="text-base text-slate-900 dark:text-slate-300 font-medium mb-1">
                        200
                    </div>
                    <div class="text-sm text-slate-600 font-light dark:text-slate-300">
                        Board Card
                    </div>
                </div>
                <!-- end single -->
                <div class="flex-1">
                    <div class="text-base text-slate-900 dark:text-slate-300 font-medium mb-1">
                        3200
                    </div>
                    <div class="text-sm text-slate-600 font-light dark:text-slate-300">
                        Calender Events
                    </div>
                </div>
                <!-- end single -->
            </div> --}}
            <!-- profile info-500 -->
        </div>
        <div
            class="profiel-wrap px-[35px] pb-10 md:pt-[60px] pt-10 rounded-lg bg-white dark:bg-slate-800 lg:flex lg:space-y-0 space-y-6 justify-between items-end relative z-[1]">

            <div class="profile-box flex-none md:text-start text-center">
                <div class="md:flex items-end md:space-x-6 rtl:space-x-reverse">
                    <div class="flex-1">
                        <div class="text-2xl font-medium text-slate-900 dark:text-slate-200 mb-[3px]">
                            {{ auth()->user()->username }}
                        </div>
                        <div class="text-sm font-light text-slate-600 dark:text-slate-400">
                            {{ auth()->user()->type }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- end profile box -->
            <!-- profile info-500 -->
        </div>
        <div>
            <div class="card h-full">
                <header class="card-header flex justify-between">
                    <h4 class="card-title">Info</h4>
                    @if ($changes)
                        <button type="submit" wire:click="saveInfo"
                            class="btn inline-flex justify-center btn-success rounded-[25px] btn-sm mr-3">Save</button>
                    @endif
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
