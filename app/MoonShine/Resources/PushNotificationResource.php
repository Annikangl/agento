<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Notification\PushNotification;
use App\UseCases\Notifications\PushNotificationService;
use Berkayk\OneSignal\OneSignalFacade as OneSignal;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Date;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;

class PushNotificationResource extends ModelResource
{
    protected string $model = PushNotification::class;

    protected string $title = 'Рассылка Push';

    protected bool $createInModal = true;

    protected ?PageType $redirectAfterSave = PageType::INDEX;


    public function fields(): array
    {
        return [
            Block::make([
                ID::make('№', 'id'),
                Text::make('Заголовок', 'title')->required(),
                Textarea::make('Текст', 'content')->required(),
                Date::make('Создан', 'created_at')->format('d-m-Y H:i:s')->hideOnCreate()
            ]),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }


    public function getActiveActions(): array
    {
        return ['index', 'create'];
    }

    public function export(): ?ExportHandler
    {
        return null;
    }

    protected function afterCreated(Model|PushNotification $item): Model
    {
        app(PushNotificationService::class)->sendToAll($item->title, $item->content);
        return $item;
    }
}
