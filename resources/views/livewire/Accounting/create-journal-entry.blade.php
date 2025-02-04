<div>
    <div class="card">
        <div class="card-body">
            <div class="card-text h-full">
                <header class="border-b px-4 pt-4 pb-3 flex items-center border-primary-500">
                    <iconify-icon class="text-3xl inline-block ltr:mr-2 rtl:ml-2 text-primary-500" icon="fluent:quiz-new-20-regular"></iconify-icon>
                    <h3 class="card-title mb-0 text-primary-500">New Journal Entry</h3>
                </header>
                <div class="loader" wire:loading wire:target="selectTitle,addAnotherDebitAccount,addAnotherCreditAccount,removeDebitAccount,removeCreditAccount">
                    <div class="loaderBar"></div>
                </div>
                <div class="container-fluid  p-6">
                    <div class="from-group">

                        <div class="mb-4">
                            @if ($selectedTitle)
                                <div class="card ring-1 ring-primary-500">
                                    <div class="card-body p-6">
                                        <div class="flex-1 items-center">
                                            <div class="card-title mb-5">{{ $selectedTitle->name }}</div>
                                            <p class="card-text">{{ $selectedTitle->desc }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="input-area">
                                    <input placeholder="Search title..." type="text" id="title" class="mt-1 block w-full p-2 border rounded-md {{ $errors->has('title') ? '!border-danger-500' : 'border-gray-300' }}" wire:model="title" maxlength="100">
                                    @error('title')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if ($entry_titles)
                                <div class="text-sm mb-4 col-span-2  mt-2">
                                    @foreach ($entry_titles as $entry_title)
                                        <p class="flex items-center">
                                            <iconify-icon icon="fluent:rename-24-filled" class="mr-2"></iconify-icon>
                                            {{ $entry_title->name }}
                                            <span wire:click="selectTitle('{{ $entry_title->id }}')" class="cursor-pointer text-primary-500 ml-2">Select title</span>
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        </div>





                    </div>


                    @if ($selectedTitle)
                        <div class="my-5">
                            <div class="">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                    <div class=" card-body rounded-md bg-[#E5F9FF] dark:bg-slate-800 shadow-base menu-open p-5">
                                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Debit Accounts <small>({{ count($debit_accounts) }})</small>
                                        </h4>

                                        <div class="from-group">
                                            @foreach ($debit_accounts as $index => $account)
                                                @php
                                                    $debit_printed_arr = [];
                                                @endphp
                                                <div class="card-body rounded-md bg-[#E5F9FF] dark:bg-slate-700 shadow-base mb-5 p-2">
                                                    <div class="input-area col-span-2">
                                                        <select class="form-control mt-1 block w-full p-2 border rounded-md {{ $errors->has('debit_accounts.' . $index . '.account_id') ? '!border-danger-500' : 'border-gray-300' }}" wire:model.defer="debit_accounts.{{ $index }}.account_id">
                                                            <option value="">Select Debit Account</option>
                                                            @foreach ($debit_accounts_list as $account)
                                                                {{ printAccountChildren('', $account, $debit_printed_arr) }}
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-3">
                                                        <!-- Amount -->
                                                        <div class="input-area">
                                                            <input type="number" class="form-control w-full mt-2 @error('debit_accounts.' . $index . '.amount') !border-danger-500 @enderror" wire:model="debit_accounts.{{ $index }}.amount" placeholder="Amount">
                                                        </div>

                                                        <!-- Currency -->
                                                        <div class="input-area">
                                                            <select class="form-control w-full mt-2 @error('debit_accounts.' . $index . '.currency') !border-danger-500 @enderror" wire:model="debit_accounts.{{ $index }}.currency">
                                                                @foreach ($CURRENCIES as $CURRENCY)
                                                                    <option value="{{ $CURRENCY }}">{{ ucwords($CURRENCY) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                                        <!-- Currency Amount -->
                                                        <div class="input-area">
                                                            <input type="number" class="form-control w-full mt-2 @error('debit_accounts.' . $index . '.currency_amount') !border-danger-500 @enderror" wire:model="debit_accounts.{{ $index }}.currency_amount" placeholder="Currency Amount">
                                                        </div>

                                                        <!-- Currency Rate -->
                                                        <div class="input-area">
                                                            <input type="number" step="0.01" class="form-control w-full mt-2 @error('debit_accounts.' . $index . '.currency_rate') !border-danger-500 @enderror" wire:model="debit_accounts.{{ $index }}.currency_rate"
                                                                placeholder="Currency Rate">
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2 w-full">
                                                        <!-- Document URL -->
                                                        <div class="flex-1">
                                                            <input type="file" class="form-control w-full mt-2 @error('debit_accounts.' . $index . '.doc_url') !border-danger-500 @enderror" wire:model="debit_accounts.{{ $index }}.doc_url" placeholder="Document URL">
                                                        </div>


                                                        <!-- Remove Button -->
                                                        @if (count($this->debit_accounts) > 1)
                                                            <div class="flex-shrink-0">
                                                                <button class="action-btn" wire:click="removeDebitAccount({{ $index }})" type="button">
                                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            @endforeach

                                            <button wire:click="addAnotherDebitAccount" class="btn btn-sm mt-2 inline-flex justify-center btn-dark">
                                                Add Debit Account
                                            </button>
                                        </div>

                                    </div>
                                    <div class=" card-body rounded-md bg-[#E5F9FF] dark:bg-slate-800 shadow-base menu-open p-5">
                                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Credit Accounts <small>({{ count($credit_accounts) }})</small>
                                        </h4>
                                        <div class="from-group">
                                            @foreach ($credit_accounts as $index => $account)
                                                @php
                                                    $credit_printed_arr = [];
                                                @endphp
                                                <div class="card-body rounded-md bg-[#E5F9FF] dark:bg-slate-700 shadow-base mb-5 p-2">
                                                    <div class="input-area col-span-2">
                                                        <select class="form-control mt-1 block w-full p-2 border rounded-md {{ $errors->has('credit_accounts.' . $index . '.account_id') ? '!border-danger-500' : 'border-gray-300' }}" wire:model.defer="credit_accounts.{{ $index }}.account_id">
                                                            <option value="">Select Credit Account</option>
                                                            @foreach ($credit_accounts_list as $account)
                                                                {{ printAccountChildren('', $account, $credit_printed_arr) }}
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-3">
                                                        <!-- Amount -->
                                                        <div class="input-area">
                                                            <input type="number" class="form-control w-full mt-2 @error('credit_accounts.' . $index . '.amount') !border-danger-500 @enderror" wire:model="credit_accounts.{{ $index }}.amount" placeholder="Amount">
                                                        </div>

                                                        <!-- Currency -->
                                                        <div class="input-area">
                                                            <select class="form-control w-full mt-2 @error('credit_accounts.' . $index . '.currency') !border-danger-500 @enderror" wire:model="credit_accounts.{{ $index }}.currency">
                                                                @foreach ($CURRENCIES as $CURRENCY)
                                                                    <option value="{{ $CURRENCY }}">{{ ucwords($CURRENCY) }}</option>
                                                                @endforeach
                                                                <!-- Add more currencies as needed -->
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 ">
                                                        <!-- Currency Amount -->
                                                        <div class="input-area">
                                                            <input type="number" class="form-control w-full mt-2 @error('credit_accounts.' . $index . '.currency_amount') !border-danger-500 @enderror" wire:model="credit_accounts.{{ $index }}.currency_amount" placeholder="Currency Amount">
                                                        </div>

                                                        <!-- Currency Rate -->
                                                        <div class="input-area">
                                                            <input type="number" step="0.01" class="form-control w-full mt-2 @error('credit_accounts.' . $index . '.currency_rate') !border-danger-500 @enderror" wire:model="credit_accounts.{{ $index }}.currency_rate"
                                                                placeholder="Currency Rate">
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2 w-full">
                                                        <!-- Document URL -->
                                                        <div class="flex-1">
                                                            <input type="file" class="form-control w-full mt-2 @error('credit_accounts.' . $index . '.doc_url') !border-danger-500 @enderror" wire:model="credit_accounts.{{ $index }}.doc_url" placeholder="Document URL">
                                                        </div>


                                                        <!-- Remove Button -->
                                                        @if (count($this->credit_accounts) > 1)
                                                            <div class="flex-shrink-0">
                                                                <button class="action-btn" wire:click="removeCreditAccount({{ $index }})" type="button">
                                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            @endforeach

                                            <button wire:click="addAnotherCreditAccount" class="btn btn-sm mt-2 inline-flex justify-center btn-dark">
                                                Add Credit Account
                                            </button>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    @else
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        <div class="input-area">
                            <label for="cash_entry_type" class="form-label">Cash entry type</label>
                            <select id="cash_entry_type" class="mt-1 block w-full p-2 border rounded-md {{ $errors->has('cash_entry_type') ? '!border-danger-500' : 'border-gray-300' }}" wire:model="cash_entry_type">
                                <option value="">N/A</option>
                                @foreach ($CASH_ENTRY_TYPES as $CASH_ENTRY_TYPE)
                                    <option value="{{ $CASH_ENTRY_TYPE }}">{{ ucwords($CASH_ENTRY_TYPE) }}</option>
                                @endforeach
                            </select>
                            @error('cash_entry_type')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-between items-end space-x-6">
                            <div class="input-area w-full">
                                <label for="receiver_name" class="form-label">Receiver name</label>
                                <input type="text" id="receiver_name" @if (!$cash_entry_type) disabled @endif class="mt-1 block w-full p-2 border rounded-md {{ $errors->has('receiver_name') ? '!border-danger-500' : 'border-gray-300' }}" wire:model.defer="receiver_name"
                                    step="0.01">
                                @error('receiver_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="from-group mt-5">
                        <div class="mb-4">
                            <label for="notes" class="block text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea id="notes" class="mt-1 block w-full p-2 border rounded-md {{ $errors->has('notes') ? '!border-danger-500' : 'border-gray-300' }}" wire:model.defer="notes" rows="6"></textarea>
                            @error('notes')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-6">
                        <button wire:click="save" data-bs-dismiss="modal" class="btn inline-flex justify-center text-white bg-black-500" @disabled($this->selectedTitle == null)>
                            <span wire:loading.remove wire:target="save" >Submit</span>
                            <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" wire:loading wire:target="save" icon="line-md:loading-twotone-loop"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
