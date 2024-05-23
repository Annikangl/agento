<?php

namespace App\UseCases;

use App\DTOs\Offer\CommercialOfferCreateDto;
use App\Exceptions\Api\Services\PDFCreateException;
use App\Models\Offer\CommercialOffer;
use Illuminate\Support\Facades\Storage;

class CommercialOfferService
{
    /**
     * Create commercial offer record
     * @param int $userId
     * @param CommercialOfferCreateDto $dto
     * @return CommercialOffer
     * @throws PDFCreateException
     */
    public function create(int $userId, CommercialOfferCreateDto $dto): CommercialOffer
    {
        return CommercialOffer::query()->create(
            [
                'user_id' => $userId,
                'source_link' => $dto->source_link,
                'source_name' => CommercialOffer::getSourceName($dto->source_link),
                'lang' => $dto->lang,
                'status' => CommercialOffer::STATUS_PENDING,
            ]
        );
    }

    public function delete(CommercialOffer $offer): void
    {
        $path = str_replace('/storage/pdfs/', '', parse_url($offer->pdf_path, PHP_URL_PATH));

        if (Storage::disk('pdfs')->exists($path)) {
            Storage::disk('pdfs')->delete($path);
        }

        $offer->delete();
    }


}
