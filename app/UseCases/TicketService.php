<?php

namespace App\UseCases;

use App\Exceptions\Api\Auth\Ticket\CreateTicketException;
use App\Models\Ticket;
use App\Models\User\User;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class TicketService
{
    /**
     * @param User|null $user
     * @param string|null $title
     * @param string|null $content
     * @param Collection $agencyRegistrationData
     * @return Ticket
     * @throws CreateTicketException
     */
    public function create(?User $user, ?string $title, ?string $content, Collection $agencyRegistrationData): Ticket
    {
        if ($agencyRegistrationData->isNotEmpty()) {
            $title = 'Заявка на регистрацию агенства';
            $content = 'Оставлена заявка на регистрацию агентства ' . $agencyRegistrationData->get('name');
        }

        try {
            return Ticket::query()->create([
                'title' => $title,
                'content' => $content,
                'user_id' => $user->id ?? null,
                'agency_data' => $agencyRegistrationData->isEmpty() ? null : $agencyRegistrationData
            ]);
        } catch (\Throwable $throwable) {
            throw new CreateTicketException($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createTicketFromWeb(string $title, string $content, string $email, ?UploadedFile $attachment)
    {
        $ticket = Ticket::query()->create([
            'title' => $title,
            'content' => $content,
            'user_id' => $user->id ?? null,
            'email' => $email
        ]);

        if ($attachment) {
            $ticket->addMedia($attachment)->toMediaCollection();
        }

        return $ticket;
    }
}
