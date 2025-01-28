<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Driver Shift
            </h4>
        </div>
    </div>

    <div class="mb-2">

        @if ($user)
            <div class="space-y-2">
                <div class="dropdown relative" style="display: contents">
                    <span class="badge bg-slate-900 text-white capitalize"
                        @if (!auth()->user()->is_driver) type="button"
                    id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" @endif>
                        <span class="cursor-pointer">
                            <span class="text-secondary-500 ">Driver:</span>&nbsp;
                            {{ ucwords($user->full_name) }}

                        </span>
                    </span>
                    <ul
                        class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                        @foreach ($drivers as $driveruser)
                            <li wire:click='ChangeUser({{ $driveruser->user->id }})'
                                class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                {{ $driveruser->user->full_name }}</b>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

    </div>
</div>

</div>