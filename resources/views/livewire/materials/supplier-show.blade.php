<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

        <div>
            <div class="flex justify-between">
                <h4>
                    <b>{{ $supplier->name }}</b><iconify-icon class="ml-3" style="position: absolute" wire:loading
                        wire:target="changeSection" icon="svg-spinners:180-ring"></iconify-icon>
                </h4>
                <div class="float-right grid-col-2">
                    <button wire:click='showEditCustomerSection'
                        class="btn inline-flex justify-center btn-outline-light btn-sm">Edit
                        info</button>
                </div>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Raw Materials Supplier</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-5 mb-5 text-wrap">
            <div class="md:flex-1 rounded-md overlay min-w-\[var\(500px\)\] sm:col-span-2" style="min-width: 400px;">
                <div class="card-body flex flex-col justify-center bg-cover card p-4 ">
                    <div class="card-text flex flex-col justify-between  menu-open">
                        <p>
                            <b>Supplier info</b>
                        </p>
                        <br>

                        <div>
                            <p><b>Phone</b></p>
                            <div class="flex">
                                <a href="tel:{{ $supplier->phone1 }}">{{ $supplier->phone1 ?? 'N/A' }}</a>
                                @if ($supplier->phone1)
                                    ,&nbsp;<a href="tel:{{ $supplier->phone2 }}">{{ $supplier->phone2 ?? 'N/A' }}</a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-5">
                            <p><b>Address</b></p>
                            <div class="flex">
                                <p>{{ $supplier->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-5">
                            <p><b>Contact</b></p>
                            <div class="flex">
                                <p>{{ $supplier->contact_name ?? 'N/A' }}</p>
                                @if ($supplier->contact_phone)
                                    :&nbsp;<a href="tel:{{ $supplier->contact_phone }}">{{ $supplier->contact_phone ?? 'N/A' }}</a>
                                @endif
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>

            <div>

                <div class="bg-info-500 rounded-md p-4 bg-opacity-[0.15] dark:bg-opacity-25 relative z-[1]">
                    <div class="overlay absolute left-0 top-0 w-full h-full z-[-1]">
                        <img src="{{ asset('assets/images/all-img/shade-2.png') }}" alt="" draggable="false"
                            class="w-full h-full object-contain">
                    </div>
                    <span class="block mb-6 text-sm text-slate-900 dark:text-white font-medium">
                        Balance
                    </span>
                    <span class="block mb- text-2xl text-slate-900 dark:text-white font-medium mb-6">
                        EGP {{ $supplier->balance }}
                    </span>
                    <div class="flex space-x-2 rtl:space-x-reverse">
                        <div class="flex-none text-xl  text-primary-500">
                            <iconify-icon icon="heroicons:arrow-trending-up"></iconify-icon>
                        </div>
                        <div class="flex-1 text-sm">
                            <span class="block mb-[2px] text-primary-500">
                                8.67%
                            </span>
                            <span class="block mb-1 text-slate-600 dark:text-slate-300">
                                From last week
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
