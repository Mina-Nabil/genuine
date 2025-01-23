<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

        <div class="flex justify-between">
            <div>
                <h3
                    class=" text-slate-900 dark:text-white {{ preg_match('/[\p{Arabic}]/u', $material->name) ? 'text-right' : 'text-left' }}">
                    <b>{{ $material->name }}</b>
                </h3>
            </div>


            @can('update', $material)
                <div>
                    <button class="btn inline-flex justify-center btn-secondary btn-sm"
                        wire:click='openEditSection'>Edit</button>
                </div>
            @endcan
        </div>

        @if ($material->desc)
            <div class="card active">
                <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
                    <div class="items-center p-5 text-wrap">

                        <p class="card-text my-5">{!! $material->desc !!}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-5 mb-5 text-wrap">

            <div>
                <div class="card active relative">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
                        <div class="items-center p-5">

                            {{-- @can('update', $material)
                                <div class="dropstart absolute top-2 right-2">
                                    <button class="inline-flex justify-center items-center" type="button"
                                        id="tableDropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                        <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2"
                                            icon="heroicons-outline:dots-vertical"></iconify-icon>
                                    </button>
                                    <ul
                                        class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">

                                        <li wire:click="openEdittSection">
                                            <span
                                                class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                                <span>Edit</span>
                                            </span>
                                        </li>

                                    </ul>
                                </div>
                            @endcan --}}

                            <div class="grid grid-cols-2 mb-4">
                                <div class="border-r ml-5">
                                    <p class="mb-2"><b>Quantity</b></p>
                                    <h4 class=" flex items-center justify-start">
                                        {{ number_format($material->inventory->on_hand) }}
                                    </h4>
                                </div>
                                <div class="ml-5">
                                    <p class="mb-2"><b>Min Quantity Limit </b></p>
                                    <h4 class=" flex items-center justify-start">
                                        {{ number_format($material->min_limit) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card active relative mt-5 p-3">

                    <div class="my-2 flex justify-between items-center">
                        <p class="mb-0 text-xs"><b>Transactions</b></p>
                    </div>



                    @if ($material->transactions->isEmpty())
                        <p class="text-xs font-light text-slate-600 dark:text-slate-300 text-center m-5">
                            No transactions added for this material!
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
                                                            style="max-width:150px;font-size: 10px; font-weight: 600;">
                                                            {{ $transaction->remarks }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td
                                            class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                            @if ($transaction->quantity >= 0)
                                                +{{ $transaction->quantity }}
                                            @else
                                                {{ $transaction->quantity }}
                                            @endif
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

                <div class="card active relative mt-5 p-3">

                    <div class="my-2 flex justify-between items-center">
                        <p class="mb-0"><b>Supplier</b></p>
                    </div>



                    @if ($material->suppliers->isEmpty())
                        <p class="text-xs font-light text-slate-600 dark:text-slate-300 text-center m-5">
                            No suppliers assigned for this material!
                        </p>
                    @else
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 text-xs">
                            <thead class="">
                                <tr>

                                    <th scope="col"
                                        class=" table-th  border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        Supplier Name
                                    </th>

                                    <th scope="col"
                                        class="table-th  border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        Offered Price
                                    </th>

                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">

                                @foreach ($suppliers as $supplier)
                                    <tr>
                                        <td
                                            class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                            <a href="{{ route('supplier.show', $supplier->id) }}"
                                                class="hover-underline cursor-pinter">
                                                <b>{{ $supplier->name }}</b>
                                            </a>
                                        </td>

                                        <td
                                            class="table-td border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                            <b>{{ number_format($supplier->pivot->price, 2) }}</b> <small>EGP</small>
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
                    @if ($visibleCommentsCount < $material->comments()->count())
                        <button wire:click="loadMore"><small class="clickable-link">See More</small></button>
                    @endif

                    @if ($visibleCommentsCount > 5)
                        <button wire:click="showLess"><small class="clickable-link">Show Less</small></button>
                    @endif
                </div>

            </div>



        </div>

    </div>

    @can('update', $material)
        @if ($editInfoSection)
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
                                    Edit raw material
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

                                <div class="input-area">
                                    <label for="materialName" class="form-label">Name</label>
                                    <input class="form-control @error('materialName') !border-danger-500 @enderror"
                                        id="materialName" type="text" wire:model="materialName" autocomplete="off">
                                    @error('materialName')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-area">
                                    <label for="materialDesc" class="form-label">Description</label>
                                    <textarea class="form-control @error('materialDesc') !border-danger-500 @enderror" id="materialDesc"
                                        wire:model="materialDesc" autocomplete="off"></textarea>
                                    @error('materialDesc')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-area">
                                    <label for="materialMinLimit" class="form-label">Min Quantity Limit</label>
                                    <input class="form-control @error('materialMinLimit') !border-danger-500 @enderror"
                                        id="materialMinLimit" type="number" wire:model="materialMinLimit"
                                        autocomplete="off">
                                    @error('materialMinLimit')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="flex items-center justify-end p-6 border-t border-slate-200 rounded-b">
                                <button wire:click="updateMaterialInfo" data-bs-dismiss="modal"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateMaterialInfo">Submit</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateMaterialInfo"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    @endcan
</div>
