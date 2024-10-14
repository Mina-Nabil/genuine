<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Notifications

            </h4>
            <!---->
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </div>
    <div class="">
        <div class="h-full card">
            <div class="p-0  h-full relative card-body">

                <!-- BEGIN: Email Header -->
                <div class="md:flex justify-between items-center sticky bg-white dark:bg-slate-800 top-0 pt-6 pb-4 px-6 z-[3] border-b border-slate-100 dark:border-slate-700 rounded-t-md">
                    <div class="flex items-center rtl:space-x-reverse">
                        <div class="open-sidebar md:h-8 md:w-8 h-6 w-6 bg-slate-100 dark:bg-slate-900 dark:text-slate-400 flex flex-col justify-center items-center ltr:mr-3 rlt:ml-3 lg:hidden md:text-base text-sm rounded-full cursor-pointer">
                            <iconify-icon icon="heroicons-outline:menu-alt-2"></iconify-icon>
                        </div>
                        <div class="max-w-[180px] flex items-center space-x-1 rtl:space-x-reverse leading-[0]">
                            <div>
                                <input type="text" id="email-search" placeholder="Search Notification" class="bg-transparent text-sm font-regular text-slate-600 dark:text-slate-300 transition duration-150 rounded px-2 py-1 focus:outline-none">
                            </div>
                        </div>
                    </div>
                    <div class="md:block hidden">
                        <div class="relative">
                            <div class="dropdown relative">
                                <button class="text-xl text-center block w-full " type="button" id="emailDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <iconify-icon icon="heroicons-outline:dots-vertical"></iconify-icon>
                                </button>
                                <ul class=" dropdown-menu min-w-[160px] absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                    <li>
                                        <a href="#" class="w-full text-slate-600 dark:text-white font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 space-x-2 rtl:space-x-reverse inline-flex items-center dark:hover:text-white">
                                            <span class=" text-lg leading-[0]"><iconify-icon icon="heroicons-outline:sort-ascending"></iconify-icon>
                                            </span>
                                            <span>Reset Sort</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="w-full text-slate-600 dark:text-white font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 space-x-2 rtl:space-x-reverse inline-flex items-center dark:hover:text-white">
                                            <span class=" text-lg leading-[0]"><iconify-icon icon="heroicons-outline:sort-ascending"></iconify-icon>
                                            </span>
                                            <span>Sort A-Z</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="w-full text-slate-600 dark:text-white font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 space-x-2 rtl:space-x-reverse inline-flex items-center dark:hover:text-white">
                                            <span class=" text-lg leading-[0]"><iconify-icon icon="heroicons-outline:sort-descending"></iconify-icon>
                                            </span>
                                            <span>Sort Z-A
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Email Header -->
                <div class="h-full all-todos overflow-x-hidden" data-simplebar="data-simplebar" style="min-height: 120px;">
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700 -mb-6 h-full email-list">

                        <!-- BEGIN: Email List -->

                        @if (!auth()->user()->notifications->isEmpty())
                            @foreach (auth()->user()->notifications as $notification)
                                <li data-status="sent,spam,personal,business" data-stared="false" class="flex px-7 space-x-6 group md:py-6 py-3 relative cursor-pointer hover:bg-slate-50 dark:hover:bg-transparent group items-center rtl:space-x-reverse">

                                    <div onclick="location.href = '{{ $notification->route }}';" class="flex items-center space-x-3 rtl:space-x-reverse">
                                        <div class="flex-none">
                                            <div class="h-8 w-8 rounded-full text-white bg-blue-500">
                                                <!-- Customize the background color -->
                                                <span class="block w-full h-full object-cover text-center text-lg leading-8">
                                                    {{ strtoupper(substr($notification->sender?->first_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="read-unread-name flex-1 text-sm min-w-max">
                                            {{ $notification->sender?->first_name }} {{ $notification->sender?->last_name }}
                                        </div>
                                    </div>

                                    <p onclick="location.href = '{{ $notification->route }}';" class="truncate">
                                        <span class="read-unread-name text-sm">
                                            {{ $notification->title }} -
                                        </span>
                                        <span class="text-sm text-slate-600 dark:text-slate-300 font-normal">
                                            {{ $notification->message }}
                                        </span>
                                    </p>
                                    <div class="grow"></div>
                                    <span>
                                        <span class="flex-1 flex space-x-4 items-center rtl:space-x-reverse">
                                            <span class="flex-none space-x-2 text-xs text-slate-900 dark:text-slate-300 rtl:space-x-reverse">
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                            </span>
                                        </span>
                                        <div>
                                            <span class="absolute ltr:right-0 rtl:left-0 top-1/2 -translate-y-1/2 dark:text-slate-300 group-hover:bg-slate-100 dark:group-hover:bg-slate-800 bg-white h-full flex items-center justify-center opacity-0 invisible group-hover:opacity-100 group-hover:visible " style="width:100px">
                                                @if ($notification->seen_at)
                                                    <iconify-icon wire:ignore wire:click="setAsNotSeen({{ $notification->id }})" icon="ep:notification" class="toolTip onTop transition duration-150 hover:tedangerxt--500 mr-3" data-tippy-content="Set as Unseen"></iconify-icon>
                                                @endif
                                                <iconify-icon wire:ignore wire:click="deleteNotf({{ $notification->id }})" icon="heroicons-outline:trash" class="toolTip onTop transition duration-150 hover:text-danger-500" data-tippy-content="delete"></iconify-icon>
                                            </span>
                                        </div>


                                    </span>
                                </li>
                            @endforeach
                        @else
                            <!-- END: Email List -->
                            <li data-status="sent,spam,personal,business" data-stared="false" class="flex px-7 space-x-6 group md:py-6 py-3 relative cursor-pointer hover:bg-slate-50 dark:hover:bg-transparent group items-center rtl:space-x-reverse">
                                <div class="py-[18px] px-6 font-normal font-Inter text-sm rounded-md bg-info-500 bg-opacity-[14%] text-info-500 w-full">
                                    <div class="flex items-start space-x-3 rtl:space-x-reverse">
                                        <div class="flex-1">
                                            You have no notifications at the moment.
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
