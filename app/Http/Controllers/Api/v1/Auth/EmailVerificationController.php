<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Events\Registered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerificationRequest;
use App\Models\User\VerificationCode;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class EmailVerificationController extends Controller
{
    /**
     * Check if email exists
     * @param EmailVerificationRequest $request
     * @return JsonResponse
     */
    public function checkEmailExists(EmailVerificationRequest $request): JsonResponse
    {
        return response()->json(['status' => true, 'message' => 'Email allowed'])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Send verify code to user via Mail
     * @param EmailVerificationRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sendVerifyCode(EmailVerificationRequest $request): JsonResponse
    {
        $email = $request->validated('email');

        $verificationCode = VerificationCode::createVerificationCode($email)->code;

        event(new Registered($verificationCode, $email));

        return response()->json(['status' => true, 'verification_code' => $verificationCode])
            ->setStatusCode(Response::HTTP_OK);
    }
}
