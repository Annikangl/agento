<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Banner;

use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Enums\PageType;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Date;
use MoonShine\Fields\Preview;
use MoonShine\Fields\Switcher;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use VI\MoonShineSpatieMediaLibrary\Fields\MediaLibrary;

/**
 * @extends ModelResource<Banner>
 */
class BannerResource extends ModelResource
{
    protected string $model = Banner::class;

    protected string $title = 'Баннеры на главной странице';

    protected bool $createInModal = true;

    protected ?PageType $redirectAfterSave = PageType::INDEX;

    /**
     * @throws FieldException
     */
    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            Preview::make(
                'Баннер',
                'banner',
                formatted: fn(Banner $banner) => $banner->getFirstMediaUrl('banner', 'thumb_1280')
            )
                ->image(),
            Switcher::make('Активирован', 'is_active')->updateOnPreview(),
            Date::make('Дата загрузки', 'created_at')
        ];
    }

    public function formFields(): array
    {
        return [
            ID::make()->sortable(),
            Preview::make(
                "Загрузите изображение баннера, которое будет отображаться на главной странице в приложении.
             Если требуется, то удалите старое изображение и загрузите новое.
             Рекомендуемый размер изображения 1280x720.",
                'banner'
            )
                ->image()
                ->required(),
            MediaLibrary::make('Загрузите изображение', 'banner')
                ->required(),

        ];
    }

    public function detailFields(): array
    {
        return [
            ID::make()->sortable(),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }

    public function getActiveActions(): array
    {
        return ['index', 'create', 'delete', 'update'];
    }

    public function export(): ?ExportHandler
    {
        return null;
    }
}
