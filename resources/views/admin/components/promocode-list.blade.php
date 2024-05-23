<h3 class="mt-5">My promo</h3>
<x-moonshine::table :notfound="true">
    <x-slot:thead>
        <th>№</th>
        <th>Code</th>
        <th>Discount</th>
        <th>Created At</th>
        <th>Usage Limit</th>
        <th>Used Count</th>
        <th>Expired At</th>
    </x-slot:thead>
    <x-slot:tbody>
        @foreach($promocodes as $promocode)
            <tr class="@if($promocode->is_active) bgc-green @endif">
                <th>{{ $promocode->id }}</th>
                <th>{{ $promocode->code }}</th>
                <th>{{ $promocode->discount }} %</th>
                <th>{{ $promocode->created_at }}</th>
                <th>{{ $promocode->usage_limit }}</th>
                <th>{{ $promocode->usad_count }}</th>
                <th>{{ $promocode->expired_at }}</th>
            </tr>
        @endforeach
    </x-slot:tbody>
</x-moonshine::table>
