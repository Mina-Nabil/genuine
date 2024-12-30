<div>
    <div class="space-y-5 profile-page mx-auto">
        <div class="flex justify-between flex-wrap items-center">
            <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
                <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                    Daily Loading Report
                </h4>
            </div>
        </div>
        <div class="card">
            <header class="card-header cust-card-header noborder pt-0">
                <div class="input-area flex no-wrap">
                    <input id="deliveryDate" type="date"
                        class="form-control @error('deliveryDate') !border-danger-500 @enderror"
                        wire:model.live="deliveryDate" autocomplete="off" min="{{ now()->toDateString() }}">
                </div>
            </header>

            <div class="card-body px-6 pb-6  overflow-x-auto">
                <div class=""> <!-- Add this wrapper to allow horizontal scroll -->
                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                        <thead class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                            <tr>
                                <th scope="col" class="table-th">Phone</th>
                                <th scope="col" class="table-th">Zone</th>
                                <th scope="col" class="table-th">Address</th>
                                <th scope="col" class="table-th">Location</th>

                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">
                                <tr>
                                    <td class="table-td">
                                        test
                                    </td>

                                    <td class="table-td">
                                        test
                                    </td>

                                    <td class="table-td">
                                        test
                                    </td>

                                    <td class="table-td">
                                        test
                                    </td>

                                </tr>

                        </tbody>

                    </table>


                    {{-- @if ($customers->isEmpty())
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
                    @endif --}}


                </div>


            </div>
        </div>
    </div>
</div>
