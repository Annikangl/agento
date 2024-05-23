<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;

class TicketResource extends ModelResource
{
    public string $model = Ticket::class;

    public string $title = 'Обращения пользователей';

    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Тема обращения', 'title'),
            Text::make(
                label: 'Краткое содержание',
                formatted: fn(Ticket $ticket) => $ticket->getShortedContentAttribute()
            ),
            BelongsTo::make('Пользователь', 'user')
        ];
    }

    public array $with = ['user'];

    public function rules(Model $item): array
    {
        return [];
    }

    public function search(): array
    {
        return ['id'];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(): array
    {
        return [

        ];
    }

    public function getActiveActions(): array
    {
        return ['view', 'delete'];
    }
}
