<div>
    @section('head_content')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @endsection

    <div class="flex justify-between flex-wrap items-right content-right mb-5">
        {{-- 
        <div class="py-[18px] px-4 rounded-[6px] bg-[#E5F9FF] dark:bg-slate-900">
            <div class="flex items-center space-x-6 rtl:space-x-reverse">
                <div class="flex-1">
                    <div class="text-slate-800 dark:text-slate-300 text-sm mb-1 font-medium">

                    </div>
                    <div class="text-slate-900 dark:text-white text-lg font-medium">

                    </div>
                </div>
            </div>
        </div> --}}

        <div class="card-body rounded-md bg-slate-800 dark:bg-slate-800 shadow-base menu-open dark"
            style="direction: rtl; text-align: right;min-width: 150px">
            <div class="items-center px-5 py-2">
                <h3 class="card-title text-slate-900 dark:text-white arabic-font">حسابك</h3>
                <p class="text-lg my-2" style="{{ $user->balance > 0 ? 'color:#00ff5d;' : 'color:#e50000;' }}">
                    <b>{{ number_format($user->balance, 2) }}</b>&nbsp;ج.م
                </p>
            </div>
        </div>

        <div class="md:mb-6 space-x-3 float-right rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block  rtl:pl-4 arabic-font ">
                حســاب مــندوب
            </h4>

            <div class="mb-2 flex justify-end mt-5">
                @if ($user)
                    <div class="space-y-2">
                        <div class="dropdown relative" style="display: contents">
                            <span class="badge bg-slate-900 text-white capitalize"
                                @if (!auth()->user()->is_driver) type="button"
                            id="secondaryFlatDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" @endif>
                                <span class="cursor-pointer">
                                    {{-- <span class="text-secondary-500 ">Driver:</span>&nbsp; --}}
                                    {{ ucwords($user->full_name) }}

                                </span>
                            </span>
                            <ul
                                class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                    z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                @foreach ($drivers as $driveruser)
                                    <li wire:click='ChangeUser({{ $driveruser->id }})'
                                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white cursor-pointer">
                                        {{ $driveruser->full_name }}</b>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <input type="text" class="form-control w-auto d-inline-block cursor-pointer" style="width:auto"
                name="datetimes" id="reportrange" />
        </div>


    </div>

    <header class="dark-card-header noborder bg-dark space-x-3"
        style="margin: 0; margin-bottom:20px; direction: rtl; text-align: right;">
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm arabic-font">
                اوردرات
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $totalOrderDelivery }}
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm arabic-font">
                عدد اوردرات
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $countOrderDelivery }}
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm arabic-font">
                ايام
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $totalStartDayDelivery }}
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm arabic-font">
                رجوع
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $totalReturn }}
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm arabic-font">
                كارته
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $sumOfRoadFees }}
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm arabic-font">
                سلفه
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $sumOfAdvance }}
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm">
                x2
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $sumOfX2 }}
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm arabic-font">
                مرتب
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $sumOfSalary }}
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="text-slate-300 dark:text-slate-200 text-sm arabic-font">
                مشتريات
            </h4>
            <div class="text-sm font-medium text-success-500">
                {{ $sumOfPurchases }}
            </div>
        </div>
    </header>

    @can('updateBalance', $user)
        <button wire:click='openAddDriverTrans'
            class="btn flex justify-center gap-2 content-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1 btn-sm arabic-font"
            style="font-weight: 300; padding: 5px;">
            <span>اضافة معاملة</span>
            <iconify-icon icon="material-symbols:add" width="15" height="15"></iconify-icon>
        </button>
    @endcan



    <div class="grid gap-5" style="direction: rtl; text-align: right;">
        @foreach ($transactions as $transaction)
            <div class="py-[18px] px-4 rounded-[6px] card ring-1 ring-secondary-500 " {{-- style="background-color:{{ $transaction->amount > 0 ? '#00ff2224;' : '#ff00001f;' }} " --}}>
                <div class="flex items-center space-x-6 rtl:space-x-reverse">
                    <div class="flex-1">
                        <div class="text-slate-800 dark:text-slate-300 text-sm mb-1 font-medium system-font">
                            <b>{{ $transaction->description }}</b>
                        </div>
                        <div class="flex justify-between">
                            <div class="text-slate-900 dark:text-white text-lg font-medium system-font"
                                style="{{ $transaction->amount > 0 ? 'color:#00a53c;' : 'color:#e50000;' }}">
                                <b>&nbsp;{{ number_format($transaction->amount, 2) }}</b>&nbsp;ج.م
                            </div>
                            <div class="text-xs" style="place-content: space-around;">
                                {{ $transaction->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($transactions->isEmpty())
            {{-- START: empty filter result --}}
            <div class="card p-5">
                <div class="card-body rounded-md bg-white dark:bg-slate-800">
                    <div class="items-center text-center p-5">
                        <h2>
                            <iconify-icon icon="icon-park-outline:search"></iconify-icon>
                        </h2>
                        <h2 class="card-title text-slate-900 dark:text-white mb-3">لم يتم العثور على أي معاملات</h2>
                    </div>
                </div>
            </div>
            {{-- END: empty filter result --}}
        @endif
    </div>

    @if ($isOpenAddDriverTrans)
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
                                Add Payment
                            </h3>
                            <button wire:click="closeAddDriverTrans" type="button"
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

                                <div class="form-group mb-5">
                                    <label for="driverAmount" class="form-label">
                                        Amount
                                    </label>
                                    <input type="number" id="driverAmount" class="form-control"
                                        wire:model="driverAmount">
                                    @error('driverAmount')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-5">
                                    <label for="driverPymtNote" class="form-label">
                                        Payment Note
                                    </label>
                                    <input type="text" id="driverPymtNote"
                                        class="form-control @error('driverPymtNote') !border-danger-500 @enderror"
                                        wire:model="driverPymtNote" list="paymentNotes">

                                    <datalist id="paymentNotes">
                                        @foreach ($WITHDRAWAL_TYPES as $t)
                                            <option value="{{ $t }}"></option>
                                        @endforeach
                                    </datalist>

                                    @error('driverPymtNote')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                            <button wire:click="addDriverTrnasaction" data-bs-dismiss="modal"
                                class="btn inline-flex justify-center text-white bg-black-500">
                                <span wire:loading.remove wire:target="addDriverTrnasaction">Submit</span>
                                <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                    wire:loading wire:target="addDriverTrnasaction"
                                    icon="line-md:loading-twotone-loop"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    @endif





    <div style="position: sticky ; bottom:0;width:100%; z-index:10;"
        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
        {{ $transactions->links('vendor.livewire.simple-bootstrap') }}
    </div>

    <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                Livewire.dispatch('dateRangeSelected', {
                    data: [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')]
                });

            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'اليوم': [moment(), moment()],
                    'أمس': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'آخر 7 أيام': [moment().subtract(6, 'days'), moment()],
                    'آخر 30 يومًا': [moment().subtract(29, 'days'), moment()],
                    'هذا الشهر': [moment().startOf('month'), moment().endOf('month')],
                    'الشهر الماضي': [moment().subtract(1, 'months').startOf('month'), moment().subtract(1,
                        'months').endOf('month')],
                    'آخر 3 أشهر': [moment().subtract(3, 'months'), moment()],
                }
            }, cb);

            cb(start, end);
        });
    </script>
</div>
