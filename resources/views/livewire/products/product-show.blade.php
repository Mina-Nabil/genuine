<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

        <div class="flex justify-between">
            <div>
                <h3
                    class=" text-slate-900 dark:text-white {{ preg_match('/[\p{Arabic}]/u', $product->name) ? 'text-right' : 'text-left' }}">
                    <b>{{ $product->name }} • {{ $product->category->name }}</b>
                </h3>
            </div>


            @can('update', $product)
                <div>
                    <button class="btn inline-flex justify-center btn-secondary btn-sm"
                        wire:click='openEditSection'>Edit</button>
                </div>
            @endcan
        </div>

        @if ($product->desc)
            <div class="card active">
                <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
                    <div class="items-center p-5 text-wrap">

                        <p class="card-text my-5">{!! $product->desc !!}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-5 mb-5 text-wrap">

            <div>
                <div class="card active relative">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
                        <div class="items-center p-5">

                            @can('update', $product)
                                <div class="dropstart absolute top-2 right-2">
                                    <button class="inline-flex justify-center items-center" type="button"
                                        id="tableDropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                        <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                            icon="heroicons-outline:dots-vertical"></iconify-icon>
                                    </button>
                                    <ul
                                        class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">

                                        <li wire:click="openEditPriceWeightSection">
                                            <span
                                                class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                                <span>Edit</span>
                                            </span>
                                        </li>

                                    </ul>
                                </div>
                            @endcan

                            <div class="grid grid-cols-2 mb-4">
                                <div class="border-r ml-5">
                                    <p class="mb-2"><b>Price</b></p>
                                    <h4 class=" flex items-center justify-start">
                                        {{ number_format($product->price) }}&nbsp;<small class="text-xs"> EGP</small>
                                    </h4>
                                </div>
                                <div class="ml-5">
                                    <p class="mb-2"><b>Weight </b></p>
                                    <h4 class=" flex items-center justify-start">
                                        {{ number_format($product->weight) }}&nbsp;<small class="text-xs"> GM</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card active relative mt-5 p-3">
                    <div class="my-2 flex justify-between items-center">
                        <p class="mb-0 text-xs"><b>Inventory</b></p>

                        @can('controlTransactions', $product)
                            <button wire:click='openTransSection'
                                class="btn inline-flex justify-center btn-outline-light btn-sm">
                                <iconify-icon icon="ic:baseline-plus" width="1.2em" height="1.2em"></iconify-icon>Add
                                transaction
                            </button>
                        @endcan
                    </div>

                    <div class="card-body flex flex-col justify-between border rounded-lg h-full menu-open p-0"
                        style="border-color:rgb(224, 224, 224)">
                        <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
                            <div class="items-center p-5">
                                <div class="grid grid-cols-3">
                                    <div class="border-r ml-5">
                                        <p class="mb-2 text-xs toolTip onTop  cursor-pointer" wire:ignore
                                            data-tippy-content="Total inventory in stock."><b>On Hand</b></p>
                                        <h6 class=" flex items-center justify-start">
                                            {{ number_format($product->inventory->on_hand) }}
                                        </h6>
                                    </div>
                                    <div class="border-r ml-5">
                                        <p class="mb-2 text-xs toolTip onTop  cursor-pointer" wire:ignore
                                            data-tippy-content="Inventory committed to unfulfilled orders."><b>Commited
                                            </b></p>
                                        <h6 class=" flex items-center justify-start">
                                            {{ number_format($product->inventory->committed) }}</h6>
                                    </div>
                                    <div class="ml-5">
                                        <p class="mb-2 text-xs toolTip onTop  cursor-pointer" wire:ignore
                                            data-tippy-content="Inventory available for sale."><b>Available </b></p>
                                        <h6 class=" flex items-center justify-start">
                                            {{ number_format($product->inventory->available) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="my-2 flex justify-between items-center mt-5">
                        <p class="mb-0 text-xs"><b>Transactions</b></p>
                        {{-- <button wire:click='openTransSection'
                            class="btn inline-flex justify-center btn-outline-light btn-sm">
                            <iconify-icon icon="ic:baseline-plus" width="1.2em" height="1.2em"></iconify-icon>Add
                            transaction
                        </button> --}}
                    </div>



                    @if ($product->transactions->isEmpty())
                        <p class="text-xs font-light text-slate-600 dark:text-slate-300 text-center m-5">
                            No transactions added for this product!
                        </p>
                    @else
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 text-xs">
                            <thead class="">
                                <tr>

                                    <th scope="col"
                                        class=" table-th  border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        Created
                                    </th>

                                    <th scope="col"
                                        class="table-th  border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        Quantity
                                    </th>

                                    <th scope="col"
                                        class=" table-th  border border-slate-100 dark:bg-slate-800 dark:border-slate-700 p-1 ">
                                        Changes
                                    </th>

                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td
                                            class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                            <div class="flex items-center">
                                                <div class="flex-1 text-start">
                                                    <h4 class="text-sm font-medium text-slate-600 whitespace-nowrap">
                                                        {{ $transaction->user->fullname }}
                                                    </h4>
                                                    <div class="text-xs font-normal text-slate-600 dark:text-slate-400">
                                                        @if ($transaction->created_at->isToday())
                                                            Today at {{ $transaction->created_at->format('h:i A') }}
                                                        @elseif($transaction->created_at->isYesterday())
                                                            Yesterday at
                                                            {{ $transaction->created_at->format('h:i A') }}
                                                        @else
                                                            on {{ $transaction->created_at->format('M d, Y') }}
                                                        @endif
                                                    </div>
                                                    @if ($transaction->remarks)
                                                        <hr class="mt-1 mb-1">
                                                        <div class="text-xs font-normal text-slate-600 dark:text-slate-400 text-wrap flex item-center"
                                                            style="max-width:150px;font-size:12px;">
                                                            {{ $transaction->remarks }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td
                                            class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                            {{ $transaction->quantity }}
                                        </td>

                                        <td
                                            class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                            <div class=" flex item-center justify-between">
                                                {{ $transaction->before }}
                                                <iconify-icon icon="ep:right" width="1.2em"
                                                    height="1.2em"></iconify-icon>
                                                {{ $transaction->after }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $transactions->links('vendor.livewire.simple-bootstrap') }}
                    @endif


                </div>
            </div>

            <div>
                <h3 class="card-title text-slate-900 dark:text-white">Timeline</h3>
                <ol class="timeline">
                    <li class="timeline-item">
                        <span class="timeline-item-icon | avatar-icon">
                            <span class="block w-full h-full object-cover text-center leading-10 text-lg user-initial">
                                {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                            </span>
                        </span>
                        <div class="new-comment">
                            <input type="text" wire:model="addedComment" wire:keydown.enter="addComment"
                                placeholder="Add a comment..." />
                        </div>
                    </li>

                    @forelse ($comments as $comment)
                        @if ($comment->level === 'info')
                            <li class="timeline-item">
                                <span class="timeline-item-icon | faded-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24">
                                        <path fill="none" d="M0 0h24v24H0z" />
                                        <path fill="currentColor"
                                            d="M12.9 6.858l4.242 4.243L7.242 21H3v-4.243l9.9-9.9zm1.414-1.414l2.121-2.122a1 1 0 0 1 1.414 0l2.829 2.829a1 1 0 0 1 0 1.414l-2.122 2.121-4.242-4.242z" />
                                    </svg>
                                </span>
                                <div class="timeline-item-description">
                                    <span class="avatar | small">
                                        <span class="block w-full h-full object-cover text-center text-lg user-initial"
                                            style="font-size: 12px">
                                            {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                                        </span>
                                    </span>
                                    <span><a href="#">{{ $comment->user->full_name }}</a> {{ $comment->title }}
                                        <time datetime="21-01-2021">
                                            @if ($comment->created_at->isToday())
                                                Today {{ $comment->created_at->format('h:i A') }}
                                            @elseif($comment->created_at->isYesterday())
                                                Yesterday {{ $comment->created_at->format('h:i A') }}
                                            @else
                                                on {{ $comment->created_at->format('M d, Y') }}
                                            @endif
                                        </time></span>
                                </div>
                            </li>
                        @elseif($comment->level === 'comment')
                            <li class="timeline-item">
                                <span class="timeline-item-icon | filled-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24">
                                        <path fill="none" d="M0 0h24v24H0z" />
                                        <path fill="currentColor"
                                            d="M6.455 19L2 22.5V4a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H6.455zM7 10v2h2v-2H7zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2z" />
                                    </svg>
                                </span>

                                <div class="timeline-item-wrapper w-full">
                                    <div class="timeline-item-description">
                                        <span class="avatar | small">
                                            <span
                                                class="block w-full h-full object-cover text-center text-lg user-initial"
                                                style="font-size: 12px">
                                                {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                                            </span>
                                        </span>
                                        <span><a href="#">{{ $comment->user->full_name }}</a> commented <time
                                                datetime="20-01-2021">
                                                @if ($comment->created_at->isToday())
                                                    Today at {{ $comment->created_at->format('h:m') }}
                                                @elseif($comment->created_at->isYesterday())
                                                    Yesterday at {{ $comment->created_at->format('h:m') }}
                                                @else
                                                    on {{ $comment->created_at->format('M d, Y') }}
                                                @endif
                                            </time>
                                        </span>
                                    </div>
                                    <div class="comment">
                                        <p>{{ $comment->title }}</p>
                                    </div>
                                </div>

                            </li>
                        @endif
                    @empty
                        <li class="timeline-item">
                            <span class="timeline-item-icon | faded-icon">
                                <iconify-icon icon="material-symbols:info-outline" width="1.2em"
                                    height="1.2em"></iconify-icon>
                            </span>
                            <div class="timeline-item-description">
                                <span><a href="#">No comments added yet.</span>
                            </div>
                        </li>
                    @endforelse
                </ol>
                <div class="flex justify-between">
                    @if ($visibleCommentsCount < $product->comments()->count())
                        <button wire:click="loadMore"><small class="clickable-link">See More</small></button>
                    @endif

                    @if ($visibleCommentsCount > 5)
                        <button wire:click="showLess"><small class="clickable-link">Show Less</small></button>
                    @endif
                </div>

            </div>



        </div>

    </div>

    @can('create', App\Models\Products\Product::class)
        @if ($editProductSection)
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
                                    Edit product
                                </h3>
                                <button wire:click="closeEditSection" type="button"
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
                                        <label for="productName" class="form-label">Name</label>
                                        <input id="productName" type="text"
                                            class="form-control @error('productName') !border-danger-500 @enderror"
                                            wire:model="productName" autocomplete="off">
                                    </div>
                                    @error('productName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="productDesc" class="form-label">Description</label>
                                        <input id="productDesc" type="text"
                                            class="form-control @error('productDesc') !border-danger-500 @enderror"
                                            wire:model="productDesc" autocomplete="off">
                                    </div>
                                    @error('productDesc')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="updateTitleDesc" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateTitleDesc">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateTitleDesc"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan


    @can('update', $product)
        @if ($editProductPriceWeightSection)
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
                                    Edit product
                                </h3>
                                <button wire:click="closeEditPriceWeightSection" type="button"
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
                                {{-- <div class="from-group">
                                <div class="input-area">
                                    <label for="productPrice" class="form-label">Price</label>
                                    <input id="productPrice" type="number"
                                        class="form-control @error('productPrice') !border-danger-500 @enderror"
                                        wire:model="productPrice" autocomplete="off">
                                </div>
                                @error('productPrice')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div> --}}

                                <div class="input-area">
                                    <label for="productPrice" class="form-label">Price</label>
                                    <div class="relative">
                                        <input class="form-control @error('productPrice') !border-danger-500 @enderror"
                                            id="productPrice" type="number" wire:model="productPrice"
                                            autocomplete="off">
                                        <span
                                            class="absolute right-3 text-sm top-1/2 -translate-y-1/2 w-9 h-full border-none flex item-center justify-end">
                                            EGP
                                        </span>
                                    </div>
                                    @error('productPrice')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-area">
                                    <label for="productWeight" class="form-label">Weight</label>
                                    <div class="relative">
                                        <input class="form-control @error('productWeight') !border-danger-500 @enderror"
                                            id="productWeight" type="number" wire:model="productWeight"
                                            autocomplete="off">
                                        <span
                                            class="absolute right-3 text-sm top-1/2 -translate-y-1/2 w-9 h-full border-none flex item-center justify-end">
                                            Grams
                                        </span>
                                    </div>
                                    @error('productWeight')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="updatePriceWeight" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updatePriceWeight">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updatePriceWeight"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

    @can('controlTransactions', $product)
        @if ($addTransSection)
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
                                    New Transaction
                                </h3>
                                <button wire:click="closeTransSection" type="button"
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
                                        <label for="transQuantity" class="form-label">Quantity*</label>
                                        <input id="transQuantity" type="number"
                                            class="form-control @error('transQuantity') !border-danger-500 @enderror"
                                            wire:model="transQuantity" autocomplete="off">
                                        @error('transQuantity')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                        <small class="text-gray-500">Enter the quantity of the transaction. This can be a
                                            positive number or a negative value if you're recording a deduction or a
                                            return.</small>
                                    </div>

                                </div>

                                <div class="from-group">
                                    <div class="input-area">
                                        <label for="transRemark" class="form-label">Remark</label>
                                        <textarea id="transRemark" class="form-control @error('transRemark') !border-danger-500 @enderror"
                                            wire:model="transRemark" autocomplete="off"></textarea>
                                        @error('transRemark')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                        <small class="text-gray-500">Optional: Provide any additional notes or remarks
                                            related to this transaction. This can help clarify the reason for the
                                            entry.</small>
                                    </div>

                                </div>


                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="addTransaction" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="addTransaction">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="addTransaction"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan

</div>
