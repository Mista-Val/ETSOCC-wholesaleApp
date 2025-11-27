<?php

use App\Models\ReceivedStock;

// Get all received stocks with relationships
function getAllReceivedStocks($perPage = 10)
{
    return ReceivedStock::with(['warehouse', 'items.product'])
           ->latest()
           ->paginate($perPage);
}