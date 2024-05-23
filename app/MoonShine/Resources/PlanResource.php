<?php

namespace App\MoonShine\Resources;

use App\Models\User\Plan;
use Illuminate\Database\Eloquent\Model;

use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;

class PlanResource extends ModelResource
{
	public string $model = Plan::class;

	public string $title = 'Планы подписок';

    protected string $column = 'name';

	public function fields(): array
	{
		return [
		    ID::make()->sortable(),
            Text::make('План', 'name'),
            Text::make('Цена', 'price'),
            Text::make('Длительность, дней', 'duration'),
            Text::make('Описание', 'description'),
        ];
	}

	public function rules(Model $item): array
	{
	    return [];
    }

    public function search(): array
    {
        return ['id'];
    }

    public function getActiveActions(): array
    {
        return ['view', 'delete'];
    }
}
