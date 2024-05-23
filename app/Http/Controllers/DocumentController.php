<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    /**
     * Get Terms of Use document
     * @return BinaryFileResponse
     */
    public function getTermsOfUse(): BinaryFileResponse
    {
        return response()->file(public_path('assets/documents/terms_of_use.pdf'));
    }

    /**
     * Get privacy policy document
     * @return BinaryFileResponse
     */
    public function getPolicy(): BinaryFileResponse
    {
        return response()->file(public_path('assets/documents/privacy_policy.pdf'));
    }
}
