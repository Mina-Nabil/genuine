@php
    // Define an array of colors for each level
    $colors = [
        0 => '#FF6347', // Level 0: Tomato
        1 => '#FFD700', // Level 1: Gold
        2 => '#ADFF2F', // Level 2: GreenYellow
        3 => '#00FA9A', // Level 3: MediumSpringGreen
        4 => '#1E90FF', // Level 4: DodgerBlue
        5 => '#9370DB', // Level 5: MediumPurple
        6 => '#FF69B4', // Level 6: HotPink
        7 => '#FF4500', // Level 7: OrangeRed
    ];
@endphp

<tr>
    <td class="table-td ">
        <div class="flex justify-between @if (!$account->children_accounts->isNotEmpty()) float-right @endif">
            @if ($account->children_accounts->isNotEmpty())
                @if (in_array($account->id, $showChildAccounts))
                    <button class="action-btn mr-2" type="button" wire:click="hideThisChildAccount({{ $account->id }})">
                        <iconify-icon icon="mingcute:up-fill"></iconify-icon>
                    </button>
                @else
                    <button class="action-btn mr-2" type="button" wire:click="showThisChildAccount({{ $account->id }})">
                        <iconify-icon icon="mingcute:down-fill"></iconify-icon>
                    </button>
                @endif
            @endif

            @can('update',$account)
            <button class="action-btn float-right" type="button" wire:click="openEditModal({{ $account->id }})">
                <iconify-icon icon="bxs:edit"></iconify-icon>
            </button>
            @endcan
        </div>
    </td>
    <td class="table-td" style="position: sticky; left: 0; background-color: white; z-index: 10;">
        <b>{{ $account->full_code }}</b>
    </td>
    <td class="table-td @if ($level != 0) padding-y-0 @endif" style="position: sticky; left: 100px; background-color: white; z-index: 10; display: flex; align-items: center; height: 100%;">
        {{-- Display bars based on the level --}}
        @for ($i = 0; $i < $level; $i++)
            <span style="display: block; width: 25px; height: 40px; background-color: {{ $colors[$i] ?? '#000000' }};opacity: 30%;"></span>
        @endfor
        <div style="flex-grow: 1; @if ($level != 0) padding-left: 10px; @endif  display: flex; align-items: center;">
            <a class="hover:underline cursor-pointer" href="{{ route('accounts.show', $account->id) }}">
                <b>{{ $account->name }}</b>
            </a>
        </div>
    </td>
    <td class="table-td">{{ $account->desc }}</td>
    <td class="table-td">{{ number_format($account->total_balance, 2) }}</td>
    <td class="table-td">{{ number_format($account->foreign_balance ?? 0, 2) }}</td>
    <td class="table-td">
        <span class="badge bg-secondary-500 text-white capitalize inline-flex items-center">
            @if ($account->nature === 'credit')
                <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:arrow-circle-up"></iconify-icon>
            @elseif ($account->nature === 'debit')
                <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:arrow-circle-down"></iconify-icon>
            @else
                <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:question-mark-circle"></iconify-icon>
            @endif
            {{ ucfirst($account->nature) }}
        </span>
    </td>
    <td class="table-td">
        <span class="badge bg-primary-500 text-white capitalize inline-flex items-center">
            @switch($account->main_account->type)
                @case('expense')
                    <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:cash"></iconify-icon>
                @break

                @case('revenue')
                    <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:currency-dollar"></iconify-icon>
                @break

                @case('asset')
                    <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:home"></iconify-icon>
                @break

                @case('liability')
                    <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:document-text"></iconify-icon>
                @break

                @default
                    <iconify-icon class="ltr:mr-1 rtl:ml-1" icon="heroicons-outline:badge-check"></iconify-icon>
            @endswitch
            {{ ucfirst($account->main_account->name) }}
        </span>
    </td>
    <td class="table-td">{{ $account->default_currency }}</td>

</tr>


<!-- Recursive call to display child accounts if any -->
@if (in_array($account->id, $showChildAccounts))
    @foreach ($account->children_accounts as $childAccount)
        @include('livewire.components.account-row', ['account' => $childAccount, 'level' => $level + 1])
    @endforeach
@endif
