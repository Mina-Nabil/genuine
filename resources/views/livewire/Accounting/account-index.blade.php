<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Accounts
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            {{-- @can('create', \App\Models\Accounting\AccountType::class) --}}
            <button wire:click="openAddNewModal" class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                New Account
            </button>
            {{-- @endcan --}}
        </div>
    </div>

    <div class="flex">
        <div class="dropdown relative mb-3 mr-2">
            <button class="btn btn-sm inline-flex justify-center btn-dark items-center" type="button" id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Nature: {{ $account_nature ?? 'All' }}
                <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
            </button>
            <ul class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                        z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                <li wire:click="updateNature(null)">
                    <a href="#" class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                            dark:hover:text-white">
                        All
                    </a>
                </li>
                @foreach ($natures as $nature)
                    <li wire:click="updateNature('{{ $nature }}')">
                        <a href="#" class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white">
                            {{ ucwords($nature) }}
                        </a>
                    </li>
                @endforeach

            </ul>
        </div>
    </div>

    <div class="mb-5">
        <iconify-icon wire:loading wire:target="searchText" class="loading-icon text-lg" icon="line-md:loading-twotone-loop"></iconify-icon>
        <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search..." wire:model="searchText">
    </div>

    <div class="card">
        {{-- <header class=" card-header noborder">
            <h4 class="card-title">Table Head
            </h4>
        </header> --}}
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class=" table-th ">
                                        Action
                                    </th>
                                    <th scope="col" class=" table-th ">
                                        Code
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Name
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Desc
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        BLNC
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        $BLNC
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Nature
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Main
                                    </th>

                                    <th scope="col" class=" table-th ">
                                        Curr.
                                    </th>

                            
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700 no-wrap">

                                @foreach ($accounts as $account)
                                    @include('livewire.components.account-row', ['account' => $account, 'level' => 0])
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @if ($isAddNewModalOpen || $accountID)
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto show" tabindex="-1" aria-labelledby="vertically_center" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog relative w-auto pointer-events-none" style="max-width: 800px;">
                <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                    <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                            <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                @if ($isAddNewModalOpen)
                                    Add New Account
                                @else
                                    Edit Account
                                @endif

                            </h3>

                            <button @if ($isAddNewModalOpen) wire:click="closeAddNewModal" @else wire:click="closeEditModal" @endif type="button"
                                class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                                <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-4">
                            {{-- <div class="form-group mb-5">
                                <label for="acc_code" class="form-label">Code</label>
                                <input type="text" id="acc_code" class="form-control mt-2 w-full {{ $errors->has('acc_code') ? '!border-danger-500' : '' }}" wire:model.defer="acc_code" max="100">
                                @error('acc_code')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div> --}}
                            <div class="form-group mb-5">
                                <label for="acc_name" class="form-label">Name</label>
                                <input type="text" id="acc_name" class="form-control mt-2 w-full {{ $errors->has('acc_name') ? '!border-danger-500' : '' }}" wire:model.defer="acc_name" max="100">
                                @error('acc_name')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-5">
                                <label for="acc_desc" class="form-label">Description</label>
                                <textarea id="acc_desc" class="form-control mt-2 w-full {{ $errors->has('acc_desc') ? '!border-danger-500' : '' }}" wire:model.defer="acc_desc"></textarea>
                                @error('acc_desc')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-5">
                                <label for="nature" class="form-label">Nature</label>
                                <select id="nature" class="form-control mt-2 w-full {{ $errors->has('nature') ? '!border-danger-500' : '' }}" wire:model.defer="nature">
                                    <option value="">Select Nature</option>
                                    @foreach ($natures as $nature)
                                        <option value="{{ $nature }}">{{ ucwords($nature) }}</option>
                                    @endforeach
                                </select>
                                @error('nature')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-5">
                                <label for="mainAccountId" class="form-label">Main Account</label>
                                <select id="mainAccountId" class="form-control mt-2 w-full {{ $errors->has('mainAccountId') ? '!border-danger-500' : '' }}" wire:model="mainAccountId">
                                    <option value="">Select Type</option>
                                    @foreach ($main_accounts as $main_account)
                                        <option value="{{ $main_account->id }}">{{ ucwords($main_account->name) . ' • ' . ucwords($main_account->type) }}</option>
                                    @endforeach
                                </select>
                                @error('mainAccountId')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>

                            @php
                                $accounts_printed_arr = [];
                            @endphp
                            @if ($mainAccountId)
                                <div class="form-group mb-5">
                                    <label for="parent_account_id" class="form-label">Parent Account</label>
                                    <select class="form-control mt-2 w-full {{ $errors->has('parent_account_id') ? '!border-danger-500' : '' }}" wire:model.defer="parent_account_id">
                                        <option value="">Select Type</option>
                                        {{-- @foreach ($filteredAccounts as $filteredAccount)
                                            <option value="{{ $filteredAccount->id }}">{{ ucwords($filteredAccount->name) . ucwords($filteredAccount->desc ? ' • ' . $filteredAccount->desc : '') }}</option>
                                        @endforeach --}}
                                        @if ($filteredAccounts)
                                            @foreach ($filteredAccounts as $account)
                                                {{ printAccountChildren('', $account, $accounts_printed_arr) }}
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('parent_account_id')
                                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            <div class="mb-4">
                                <label for="defaultCurrency" class="block text-sm font-medium text-gray-700">Default Currency</label>
                                <select id="defaultCurrency" wire:model="defaultCurrency" class="form-control">
                                    @foreach ($CURRENCIES as $currency)
                                        <option value="{{ $currency }}">{{ $currency }}</option>
                                    @endforeach
                                </select>
                                @error('defaultCurrency') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-5">
                                <label for="acc_desc" class="form-label">Desciption</label>
                                <input type="number" id="acc_desc" class="form-control mt-2 w-full {{ $errors->has('acc_desc') ? '!border-danger-500' : '' }}" wire:model.defer="acc_desc" step="0.01">
                                @error('acc_desc')
                                    <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                            @if ($isAddNewModalOpen)
                                <button wire:click="save" class="btn inline-flex justify-center text-white bg-black-500">
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="save" icon="line-md:loading-twotone-loop"></iconify-icon>
                                    <span wire:loading.remove="save">Submit</span>
                                </button>
                            @else
                                <button wire:click="saveEdit" class="btn inline-flex justify-center text-white bg-black-500">
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="saveEdit" icon="line-md:loading-twotone-loop"></iconify-icon>
                                    <span wire:loading.remove="saveEdit">Submit</span>
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


</div>
