<?php

namespace App\UseCases\Selection;

use App\DTOs\Selection\SelectionCreateDto;
use App\Exceptions\Api\Selection\CreateSelectionException;
use App\Exceptions\Api\Selection\CreateSelectionUniquidException;
use App\Exceptions\Api\Selection\DeleteSelectionException;
use App\Models\Selection\Selection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class SelectionService
{
    /**
     * @param int $userId
     * @param string|null $search
     * @return LengthAwarePaginator
     */
    public function getByUserId(int $userId, string $search = null): LengthAwarePaginator
    {
        return Selection::query()->with('adverts')
            ->byUser($userId)
            ->bySearch($search)
            ->latest()
            ->paginate(15);
    }

    /**
     * Create a new selection
     * @param SelectionCreateDto $dto
     * @param int $userId
     * @return Selection
     * @throws CreateSelectionException
     */
    public function create(SelectionCreateDto $dto, int $userId): Selection
    {
        try {
            $selection = new Selection();
            $selection->user_id = $userId;
            $selection->title = $dto->title;
            $selection->deal_type = $dto->deal_type;
            $selection->property_type = $dto->property_type;
            $selection->completion = $dto->completion;
            $selection->beds = $dto->beds;
            $selection->size_from = $dto->size_from;
            $selection->size_to = $dto->size_to;
            $selection->size_units = $dto->size_units;
            $selection->location = $dto->location;
            $selection->budget_from = $dto->budget_from;
            $selection->budget_to = $dto->budget_to;
            $selection->is_liked = false;
            $selection->location_type = $dto->location_type;
            $selection->expired_at = now()->addDays(Selection::EXPIRED_DAYS);
            $selection->save();

            return $selection;
        } catch (\Throwable $throwable) {
            throw new CreateSelectionException($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create unique id for selection. This unique id use as a link for sharing selection
     * @throws CreateSelectionUniquidException
     */
    public function createUniqueId(Selection $selection): void
    {
        if ($selection->adverts->isEmpty()) {
            throw new CreateSelectionUniquidException(
                'Selection doesnt have adverts',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($selection->uniqueid) {
            return;
        }

        try {
            $selection->uniqueid = $this->generateUniqueId();
            $selection->web_link = route('selection.show', $selection->uniqueid);
            $selection->save();
        } catch (\Throwable $throwable) {
            throw new CreateSelectionUniquidException(
                'Failed to save uniquid',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete selection from DB
     * @param Selection $selection
     * @return void
     * @throws DeleteSelectionException
     */
    public function delete(Selection $selection): void
    {
        try {
            $selection->delete();
        } catch (\Throwable $throwable) {
            throw new DeleteSelectionException($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate unique id
     * @return string
     */
    public function generateUniqueId(): string
    {
        return uniqid();
    }
}
