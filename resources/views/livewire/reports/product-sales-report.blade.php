<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Product Sales Report
            </h4>
        </div>
    </div>

    <div class="card p-6">
        <div class="card-body flex flex-col pb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" wire:model.live="startDate" id="start_date">
                </div>
                <div class="form-group">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" wire:model.live="endDate" id="end_date">
                </div>
                <div class="form-group">
                    <label for="search" class="form-label">Search Products</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by product name...">
                </div>
            </div>
        </div>

        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Product</th>
                                    <th scope="col" class="table-th">Unit KG</th>
                                    <th scope="col" class="table-th">Quantity ({{ $productTotals->sum('total_quantity') }})</th>
                                    <th scope="col" class="table-th">Total KG</th>
                                    <th scope="col" class="table-th">Orders ({{ $productTotals->sum('order_count') }})</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @foreach($productTotals as $product)
                                <tr class="even:bg-slate-50 dark:even:bg-slate-700">
                                    <td class="table-td">{{ $product->name }}</td>
                                    <td class="table-td">{{ number_format($product->weight / 1000, 2) }}</td>
                                    <td class="table-td">{{ number_format($product->total_quantity) }}</td>
                                    <td class="table-td">{{ number_format($product->total_quantity * $product->weight / 1000, 2) }}</td>
                                    <td class="table-td">{{ number_format($product->order_count) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                {{ $productTotals->links() }}
            </div>
        </div>
    </div>
</div> 