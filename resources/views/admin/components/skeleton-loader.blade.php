
<div class="w-100">
    @php
        $rowsCount = $rows ?? 5;
    @endphp
    
    @foreach(range(1, $rowsCount) as $i)
        <div class="skeleton-line"></div>
    @endforeach
</div>