<div>
    <div class="space-y-5 profile-page mx-auto" style="max-width: 1000px">

        <div class="flex justify-between">
            <div>
                <h3
                class=" text-slate-900 dark:text-white {{ preg_match('/[\p{Arabic}]/u', $product->name) ? 'text-right' : 'text-left' }}">
                <b>{{ $product->name }}</b>
            </h3>
            </div>
            <div>
                <button class="btn inline-flex justify-center btn-secondary btn-sm">Edit</button>
            </div>
        </div>

        <div class="card active">
            <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
                <div class="items-center p-5">
                    
                    <p class="card-text my-5">Lorem ipsum dolor sit amet, consec tetur adipiscing elit, sed do
                        eiusmod tempor incididun ut labore et dolor magna aliqua.</p>
                    <a href="card.html" class="underline btn-link active">{{ $product->category->name }}</a>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-5 mb-5 text-wrap">

            <div>
                <div class="card active">
                    <div class="card-body rounded-md bg-white dark:bg-slate-800 shadow-base menu-open">
                        <div class="items-center p-5">
                            <div class="grid grid-cols-2 mb-4">
                                <div class="border-r ml-5">
                                    <p class="mb-2"><b>Price</b></p>
                                        <h4 class=" flex items-center justify-start">{{ $product->price }}&nbsp;<small class="text-xs"> EGP</small> </h4>
                                </div>
                                <div class="ml-5">
                                    <p  class="mb-2"><b>Weight </b></p>
                                    <h4 class=" flex items-center justify-start">{{ $product->weight }}&nbsp;<small class="text-xs"> GM</small> </h4>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                        <span
                                            class="block w-full h-full object-cover text-center text-lg user-initial" style="font-size: 12px">
                                            {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                                        </span>
                                    </span>
                                    <span><a href="#">{{ $comment->user->full_name }}</a> {{ $comment->title }}
                                        <time datetime="21-01-2021">
                                            @if ($comment->created_at->isToday())
                                                Today
                                            @elseif($comment->created_at->isYesterday())
                                                Yesterday
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
                                                class="block w-full h-full object-cover text-center text-lg user-initial" style="font-size: 12px">
                                                {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                                            </span>
                                        </span>
                                        <span><a href="#">{{ $comment->user->full_name }}</a> commented <time
                                                datetime="20-01-2021">
                                                @if ($comment->created_at->isToday())
                                                    Today
                                                @elseif($comment->created_at->isYesterday())
                                                    Yesterday
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
</div>
