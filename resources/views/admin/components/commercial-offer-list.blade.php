<h3 class="mt-3">Последние 25 КП</h3>

<x-moonshine::table :notfound="true">
    <x-slot:thead>
        <th>№ КП</th>
        <th>Источник</th>
        <th>Заголовок</th>
        <th>PDF</th>
        <th>Статус</th>
        <th>Дата</th>
    </x-slot:thead>
    <x-slot:tbody>
        @foreach($items as $item)
            @php
                /** @var \App\Models\Offer\CommercialOffer $item */
            @endphp
            <tr class="@if($item->isCompleted()) bgc-green @elseif($item->isError()) bgc-red @endif">
                <th>{{ $item->id }}</th>
                <th><a href="{{  $item->source_link  }}" target="_blank">Источник</a></th>
                <th>{{ $item->title }}</th>
                <th><a href="{{ $item->pdf_path }}" target="_blank">PDF</a></th>
                <th>{{ \App\Models\Offer\CommercialOffer::getStatuses()[ $item->status ] }}</th>
                <th>{{ $item->created_at }}</th>
            </tr>
        @endforeach
    </x-slot:tbody>
</x-moonshine::table>
