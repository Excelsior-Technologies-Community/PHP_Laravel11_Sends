<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Send;

class SendMailController extends Controller
{
    // Show form
    public function index()
    {
        return view('mailform');
    }

    // Send email + save DB
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|email'
        ]);

        $to = $request->to;

        $subject = 'Test Email from Laravel Sends';

        $data = [
            'title' => 'Welcome to Laravel Sends',
            'body' => 'This is a test email using Laravel Sends!'
        ];

        try {

            Mail::send(
                'emails.hello',
                $data,
                function ($message) use ($to, $subject) {

                    $message->from(
                        env('MAIL_FROM_ADDRESS'),
                        env('MAIL_FROM_NAME')
                    );

                    $message->to($to)
                            ->subject($subject);
                }
            );

            Send::create([
                'uuid' => Str::uuid(),
                'mail_class' => null,
                'subject' => $subject,
                'content' => $data['body'],
                'from' => env('MAIL_FROM_ADDRESS'),
                'to' => $to,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return back()->with(
                'success',
                'Email sent successfully to '.$to
            );

        } catch (\Exception $e) {

            \Log::error($e->getMessage());

            Send::create([
                'uuid' => Str::uuid(),
                'mail_class' => null,
                'subject' => $subject,
                'content' => $e->getMessage(),
                'from' => env('MAIL_FROM_ADDRESS'),
                'to' => $to,
                'status' => 'failed',
                'sent_at' => now(),
            ]);

            return back()->with(
                'error',
                'Email failed: '.$e->getMessage()
            );
        }
    }

    // LIST + SEARCH + FILTER
    public function allSends(Request $request)
    {
        $query = Send::query();

        if ($request->search) {

            $query->where('to', 'like', '%' . $request->search . '%')
                  ->orWhere(
                      'subject',
                      'like',
                      '%' . $request->search . '%'
                  );
        }

        if ($request->status) {

            $query->where(
                'status',
                $request->status
            );
        }

      $emails = $query->orderBy('id', 'asc')->paginate(2);
      
        return view(
            'sends',
            compact('emails')
        );
    }
}