<?php

namespace App\UseCases;

use App\Exceptions\Api\Services\PDFCreateException;
use App\Models\Offer\CommercialOffer;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PDFCreator
{
    protected string $url;

    public function __construct()
    {
        $this->url = 'http://pdf-scrappers/';
    }

    /**
     * @param CommercialOffer $offer
     * @return array
     */
    public function create(CommercialOffer $offer, Collection $options): array
    {
        $url = $this->url . 'api_go_' . $offer->source_name . '.php';

        $data = [
            'link' => $offer->source_link,
            'name' => $offer->user->name,
            'phone' => $offer->user->phone,
            'lang' => $options->get('lang') ?? 'en'
        ];

        Log::channel('commercial_offer')->info('Sending request for create new PDF...', [
            'request_data' => $data,
        ]);

        try {
            $postFields = http_build_query($data);

            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

            $response = curl_exec($ch);

            if ($response === false) {
                throw new PDFCreateException(curl_error($ch), curl_errno($ch));
            }

            $result = json_decode($response, true);

            if (!is_array($result)) {
                throw new PDFCreateException(
                    'Invalid response format. Result: ' . $result . 'Response: ' . $response,
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            if (isset($result['code']) && $result['code'] !== 1) {
                throw new PDFCreateException($result['error']);
            }

            Log::channel('commercial_offer')->info('Response successfully', [
                'response' => $result
            ]);

            return $result;
        } catch (\Exception $exception) {
            Log::channel('commercial_offer')->error('Error creating PDF', ['error' => $exception->getMessage()]);
            return ['error' => $exception->getMessage()];
        } finally {
            if (isset($ch)) {
                curl_close($ch);
            }
        }

    }
}
