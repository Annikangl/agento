<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Promocodes;

use App\Models\User\Promocode;
use App\MoonShine\Pages\Supervisor\SupervisorIndexPage;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Date;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\ID;
use MoonShine\Fields\Number;
use MoonShine\Fields\Text;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Models\MoonshineUser;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Resources\ModelResource;

class SupervisorPromocodeResource extends ModelResource
{
    protected string $model = Promocode::class;

    protected string $title = 'My promo';

    protected bool $createInModal = true;

    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Text::make('Code', 'code'),
                Number::make('Discount, %', 'discount')
                    ->max(100)
                    ->min(0)
                    ->required()
                    ->placeholder('Enter the discount as a percentage, from 0 to 100'),
                Date::make('Expired at date', 'expired_at'),
                Number::make('Usage limit', 'usage_limit'),
                Number::make('Used count', 'used_count')->hideOnCreate(),
                Hidden::make('supervisor_id', 'supervisor_id')
                    ->hideOnIndex()
                    ->hideOnDetail()
                    ->setValue($this->getUser()->id),
            ]),
        ];
    }
    public function rules(Model $item): array
    {
        return [];
    }

    public function query(): Builder
    {
        if ($this->getUser()->isSuperUser()) {
            return parent::query();
        }

        return parent::query()
            ->where('supervisor_id', $this->getUser()->id);
    }

    private function getUser(): MoonshineUser|Authenticatable
    {
        return auth('moonshine')->user();
    }

    public function export(): ?ExportHandler
    {
        return null;
    }

    public function getActiveActions(): array
    {
        return ['index', 'view'];
    }

    protected function pages(): array
    {
        return [
            SupervisorIndexPage::make($this->title),
            DetailPage::make(__('moonshine::ui.show')),
        ];
    }
}
