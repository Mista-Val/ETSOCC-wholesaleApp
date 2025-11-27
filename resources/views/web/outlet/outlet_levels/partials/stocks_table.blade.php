@forelse($stocks as $stock)
    <tr>
        {{-- Access SKU/Name via relationship if it's an Eloquent model (single outlet) --}}
        {{-- Or directly if it's a query builder result with selected columns (All Outlets) --}}
        
        <td class="px-6 py-3">
            {{ $stock->product->sku ?? $stock->sku ?? '-' }}
        </td>
        <td class="px-6 py-3">
            {{ $stock->product->name ?? $stock->name ?? '-' }}
        </td>
        <td class="px-6 py-3">{{ $stock->product_quantity ?? 0 }}</td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="px-6 py-3 text-center text-gray-500">
            <div class="flex flex-col items-center justify-center gap-2 py-8">
                <img src="{{ asset('web/images/stock.png') }}" alt="stock" class="w-16 h-16" />
                <h3><strong>No records found.</strong></h3>
            </div>
        </td>
    </tr>
@endforelse