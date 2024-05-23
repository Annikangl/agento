<h3 class="mt-3">История транзакций</h3>
<x-moonshine::table :notfound="true">
    <x-slot:thead>
        <th>№</th>
        <th>Событие</th>
        <th>План подписки</th>
        <th>Цена в KZT</th>
        <th>Магазин</th>
        <th>Дата операции</th>
        <th>Активна</th>
    </x-slot:thead>
    <x-slot:tbody>
        @foreach($transactions as $transaction)
            <tr class="@if($transaction->is_active) bgc-green @endif">
                <th>{{ $transaction->id }}</th>
                <th>{{ $transaction->event_type }}</th>
                <th>{{ $transaction->plan->name }}</th>
                <th>{{ $transaction->price_in_purchased_currency }}</th>
                <th>{{ $transaction->store }}</th>
                <th>{{ $transaction->created_at }}</th>
                <th>{{ $transaction->is_active  }}</th>
            </tr>
        @endforeach
    </x-slot:tbody>
</x-moonshine::table>
