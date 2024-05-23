<?php

namespace App\Exceptions\Api\Services;

use App\Traits\ApiException;
use Illuminate\Support\Facades\Log;

class PDFCreateException extends \Exception
{
    use ApiException;

    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report(): void
    {
        Log::channel('commercial_offer')->error('PDF creation failed: ' . $this->getMessage(), [
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ]);
    }
}
