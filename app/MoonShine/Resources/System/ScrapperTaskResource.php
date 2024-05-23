<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\System;

use App\Enums\ScrapperTaskStatus;
use App\Models\ScrapperTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Date;
use MoonShine\Fields\Enum;
use MoonShine\Fields\ID;
use MoonShine\Fields\Preview;
use MoonShine\Handlers\ExportHandler;
use MoonShine\MoonShineRequest;
use MoonShine\Resources\ModelResource;

/**
 * @extends ModelResource<ScrapperTask>
 */
class ScrapperTaskResource extends ModelResource
{
    protected string $model = ScrapperTask::class;

    protected string $title = 'История работы парсинга';

    public function fields(): array
    {
        return [
            Block::make([
                ID::make('#', 'task_id')->sortable(),
                Date::make('Старт', 'task_start')->format('d.m.Y H:i'),
                Date::make('Последнее обновление', 'task_last_update')->format('d.m.Y H:i'),
                Enum::make('Статус', 'task_status')->attach(ScrapperTaskStatus::class),
                Preview::make('Ресурс парсинга', 'task_type'),
                Preview::make('Прогресс, %', 'task_progress')->hideOnIndex(),
                Preview::make('Лог файл', 'task_log_path'),
            ]),
        ];
    }

    /**
     * @throws \Throwable
     */
    public function detailButtons(): array
    {
        return [
            ActionButton::make(__('moonshine::ui.resource.actions.download_log'))
                ->method('viewLog')
                ->icon('heroicons.eye'),
            ActionButton::make('Останоить')
                ->canSee(fn (ScrapperTask $task) => $task->task_status === ScrapperTaskStatus::RUNNING)
                ->method('stopScrapper')
                ->icon('heroicons.stop'),
        ];
    }

    public function getActiveActions(): array
    {
        return ['view'];
    }

    public function export(): ?ExportHandler
    {
        return null;
    }

    public function rules(Model $item): array
    {
        return [];
    }

    /**
     * @throws \Exception
     */
    public function viewLog(MoonShineRequest $request): RedirectResponse
    {
        $task = ScrapperTask::query()->find($request->input('resourceItem'));

        return redirect()->route('moonshine.scrapper.log.download', ['scrapperTask' => $task]);
    }

    public function stopScrapper(MoonShineRequest $request): RedirectResponse
    {
        $task = ScrapperTask::query()->find($request->input('resourceItem'));

        $task->task_status = 0;
        $task->save();

        return back();
    }
}
