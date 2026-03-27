<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Send; // Use your own model
use Illuminate\Support\Str;

class SendMailController extends Controller
{
    // Show email form
    public function index()
    {
        return view('mailform');
    }

    // Send email and store in DB
    public function send(Request $request)
    {
        $to = $request->to ?? 'recipient@example.com';

        $data = [
            'title' => 'Welcome to Laravel Sends',
            'body'  => 'This is a test email using Laravel Sends!'
        ];

        // Send email
        Mail::send('emails.hello', $data, function ($message) use ($to) {
            $message->to($to)
                    ->subject('Test Email from Laravel Sends');
        });

        // Store email in DB
        Send::create([
            'uuid'       => Str::uuid(),
            'mail_class' => null,
            'subject'    => 'Test Email from Laravel Sends',
            'content'    => view('emails.hello', $data)->render(),
            'from'       => config('mail.from.address'), // safe now
            'to'         => $to,
            'sent_at'    => now(),
        ]);

        return back()->with('success', "Email sent and stored in DB to $to!");
    }

    // Show all sent emails
    public function allSends()
    {
        $emails = Send::latest()->get();
        return view('sends', compact('emails'));
    }
}