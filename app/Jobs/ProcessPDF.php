<?php

namespace App\Jobs;

use App\Exceptions\Api\Services\PDFCreateException;
use App\Models\Offer\CommercialOffer;
use App\UseCases\PDFCreator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProcessPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(protected CommercialOffer $offer, protected Collection $options)
    {
    }

    /**
     * Scrapping krisha.kz and save PDF by URL
     * @param PDFCreator $creator
     * @throws PDFCreateException
     */
    public function handle(PDFCreator $creator): void
    {
        try {
            $response = $creator->create($this->offer, $this->options);

            if (isset($response['fname']) && !is_string($response['fname'])) {
                $this->offer->setStatusError($response['error']);
                throw new PDFCreateException('Invalid fname', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $this->offer->update([
                'title' => $response['title'],
                'pdf_path' => $response['fname'],
                'status' => CommercialOffer::STATUS_COMPLETED
            ]);
        } catch (\Throwable $exception) {
            $this->offer->setStatusError($exception->getMessage());
            throw new PDFCreateException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
