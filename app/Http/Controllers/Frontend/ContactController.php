<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\UseCases\TicketService;

class ContactController extends Controller
{
    public function __construct(private readonly TicketService $ticketService)
    {
    }

    public function contactForm()
    {
        return view('frontend.contact.create');
    }

    public function createTicket(TicketRequest $request)
    {
        try {
            $this->ticketService->createTicketFromWeb(
                title: 'Обращение с сайта',
                content: $request->validated('content'),
                email: $request->validated('email'),
                attachment: $request->validated('attachment')
            );

            return redirect()->route('contact.form')
                ->with('success', 'Thank you for contacting us. We will definitely consider your request!');
        } catch (\Exception $exception) {
            return redirect()->route('contact.form')->with('error', 'Something went wrong. Please, try later');
        }
    }
}
