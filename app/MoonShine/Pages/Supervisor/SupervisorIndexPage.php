<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Supervisor;

use App\MoonShine\Components\PromocodeList;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Grid;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Pages\Page;

class SupervisorIndexPage extends Page
{
    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title()
        ];
    }

    public function title(): string
    {
        return $this->title ?: 'SupervisorIndexPage';
    }

    public function components(): array
	{
		return [
            Grid::make([
                ValueMetric::make('Your balance')
                    ->value('100 AED')
                    ->columnSpan(4),
                ValueMetric::make('Promo code used')
                    ->value(1)
                    ->columnSpan(4),
            ]),

            PromocodeList::make(auth()->user()),
        ];
	}
}
