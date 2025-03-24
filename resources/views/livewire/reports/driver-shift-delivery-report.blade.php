<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Driver Shift Delivery Report
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <div class="dropdown relative">
                <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                    id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter
                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                </button>
                <ul
                    class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                            z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                    <li wire:click='openFilteryDeliveryDate'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                        Delivery Date
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mb-5">
        @if ($deliveryDate)
            <span class="badge bg-slate-900 text-white capitalize">
                <span class="cursor-pointer" wire:click='openFilteryDeliveryDate'>
                    <span class="text-secondary-500">Delivery Date:</span>
                    @foreach ($deliveryDate as $sDdate)
                        &nbsp;
                        {{ $sDdate->isToday()
                            ? 'Today'
                            : ($sDdate->isYesterday()
                                ? 'Yesterday'
                                : ($sDdate->isTomorrow()
                                    ? 'Tomorrow'
                                    : $sDdate->format('l d-m-Y'))) }}
                        @if (!$loop->last)
                            ,
                        @endif
                    @endforeach
                </span>
            </span>
        @endif
        @if ($driver)
            <span class="badge bg-slate-900 text-white capitalize">
                <span class="cursor-pointer">
                    <span class="text-secondary-500">Driver:</span>&nbsp;
                    {{ ucwords($driver->user->full_name) }} • {{ $driver->shift_title }}
                </span>
                &nbsp;&nbsp;<iconify-icon wire:click="clearProperty('driver')" icon="material-symbols:close"
                    class="cursor-pointer" width="1.2em" height="1.2em"></iconify-icon>
            </span>
        @endif
    </div>

    <div>
        <div class="md:flex-1 rounded-md overlay md:col-span-2 mb-5" style="min-width: 400px;">
            <div class="flex-1 rounded-md col-span-2">
                <div class="card-body flex flex-col justify-center bg-no-repeat bg-center bg-cover card p-4 active">
                    <div class="card-text flex flex-col justify-between h-full menu-open">
                        <span class="text-lg"></span><b>Assigned Drivers on @foreach ($deliveryDate as $sDdate)
                                {{ $sDdate->isToday()
                                    ? 'Today'
                                    : ($sDdate->isYesterday()
                                        ? 'Yesterday'
                                        : ($sDdate->isTomorrow()
                                            ? 'Tomorrow'
                                            : $sDdate->format('l d-m-Y'))) }}
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </b>
                        <div class="basicRadio">
                            <label class="flex items-center cursor-pointer">
                                <input wire:model.live="driverRadio" type="radio" class="hidden" name="driverRadios"
                                    value="all" @checked($driver == null)>
                                <span
                                    class="flex-none bg-white dark:bg-slate-500 rounded-full border inline-flex ltr:mr-2 rtl:ml-2 relative transition-all
                                    duration-150 h-[16px] w-[16px] border-slate-400 dark:border-slate-600 dark:ring-slate-700"></span>
                                <span @class([
                                    'text-sm',
                                    'leading-6',
                                    'capitalize',
                                    'text-secondary-500' => $driverRadio != 'all',
                                    'text-primary-500' => $driverRadio == 'all',
                                ])>
                                    All
                                </span>
                            </label>
                        </div>
                        @foreach ($todayShifts as $d)
                            <div class="basicRadio">
                                <label class="flex items-center cursor-pointer">
                                    <input wire:model.live="driverRadio" type="radio" class="hidden"
                                        name="driverRadios" value="{{ $d->id }}" @checked($driver?->id === $d->id)>
                                    <span
                                        class="flex-none bg-white dark:bg-slate-500 rounded-full border inline-flex ltr:mr-2 rtl:ml-2 relative transition-all
                                        duration-150 h-[16px] w-[16px] border-slate-400 dark:border-slate-600 dark:ring-slate-700"></span>
                                    <span @class([
                                        'text-sm',
                                        'leading-6',
                                        'capitalize',
                                        'text-secondary-500' => $driver?->id != $d->id,
                                        'text-primary-500' => $driver?->id == $d->id,
                                    ])>
                                        {{ ucwords($d->user->full_name) }} • {{ $d->shift_title }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($driverTotals as $driverId => $data)
        <div class="card active mb-5">
            <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base">
                <div class="flex justify-between p-5 items-center mb-4">
                    <h5 class="text-lg font-medium text-slate-900 dark:text-white">
                        {{ ucwords($data['driver']->user->full_name) }} • {{ $data['driver']->shift_title }}
                    </h5>
                    <div class="flex gap-4">
                        <div class="text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Total Weight:</span>
                            <span class="font-bold">{{ number_format($data['total_weight'], 3) }} KG</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Total Items:</span>
                            <span class="font-bold">{{ $data['total_items'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Product</th>
                                <th scope="col" class="table-th">Quantity</th>
                                <th scope="col" class="table-th">Weight (KG)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                            @foreach ($data['product_totals'] as $product)
                                <tr>
                                    <td class="table-td text-lg"><b>{{ $product['name'] }}</b></td>
                                    <td class="table-td">{{ $product['quantity'] }}</td>
                                    <td class="table-td">{{ number_format($product['weight'], 3) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    @if ($Edited_deliveryDate_sec)
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
                                Filter delivery date
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="removeSelectedDate,Edited_deliveryDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </h3>
                            <button wire:click="closeFilteryDeliveryDate" type="button"
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
                                    <label for="Edited_deliveryDate" class="form-label">Delivery date*</label>
                                    <p class="text-gray-600 text-xs mb-2">
                                        *You can select multiple dates by clicking on the date. Once done, click
                                        "Submit" to apply the filter.
                                    </p>
                                    <input name="Edited_deliveryDate" id="Edited_deliveryDate" type="date"
                                        class="form-control w-full mt-2 @error('Edited_deliveryDate') !border-danger-500 @enderror"
                                        wire:model.live="Edited_deliveryDate" autocomplete="off">
                                </div>
                                @error('Edited_deliveryDate')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            @foreach ($selectedDeliveryDates as $index => $date)
                                <span class="badge bg-slate-900 text-white capitalize">
                                    <span class="cursor-pointer">
                                        {{ $date->isToday()
                                            ? 'Today'
                                            : ($date->isYesterday()
                                                ? 'Yesterday'
                                                : ($date->isTomorrow()
                                                    ? 'Tomorrow'
                                                    : $date->format('l d-m-Y'))) }}
                                    </span>

                                    @if (count($selectedDeliveryDates) > 1)
                                        &nbsp;&nbsp;<iconify-icon wire:click="removeSelectedDate({{ $index }})"
                                            icon="material-symbols:close" class="cursor-pointer" width="1.2em"
                                            height="1.2em"></iconify-icon>
                                    @endif
                                </span>
                            @endforeach
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="setFilteryDeliveryDate" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="setFilteryDeliveryDate">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="setFilteryDeliveryDate"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
