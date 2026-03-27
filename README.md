# PHP_Laravel11_Sends



## Project Description

PHP_Laravel11_Sends is a Laravel 11-based application that demonstrates how to send emails using Laravel’s mailing system and store outgoing emails in the database using the wnx/laravel-sends package.

This project is ideal for developers who want to track all sent emails, view them in a beautiful web interface, and test email delivery in a local development environment (Mailtrap recommended).

It includes a simple email form, a database log for sent emails, and a list page to display all sent emails.



## Features

- Send test emails to any recipient.
- Automatically store all outgoing emails in the database.
- View sent emails in a user-friendly table.
- Uses Laravel’s built-in Mail system.
- Fully responsive and styled with a dark theme.
- Supports future integration with Mailtrap, Gmail, or other SMTP servers.
- Clean MVC architecture for easy customization.




## Technology Stack

- Framework: Laravel 11
- Language: PHP 8+
- Database: MySQL
- Mailing: Laravel Mail + wnx/laravel-sends
- Frontend: Blade Templates, HTML, CSS (Dark Mode)
- Other Tools: Composer, Artisan CLI'





---



## Installation Steps


---


## STEP 1: Create Laravel 11 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel11_Sends "11.*"

```

### Go inside project:

```
cd PHP_Laravel11_Sends

```

#### Explanation:

Install Laravel 11 and create a new project folder.




## STEP 2: Database Setup

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel11_sends
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel11_sends

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

Configure .env and create MySQL database; run migrations for default tables.




## STEP 3: Install Laravel Sends

### Run:

```
composer require wnx/laravel-sends

```

#### Explanation:

Install the wnx/laravel-sends package for email logging.





## STEP 4: Publish Migrations & Config

### Run:

```
php artisan vendor:publish --tag="sends-migrations"
php artisan vendor:publish --tag="sends-config"
php artisan migrate

```

#### Explanation:

Publish package migrations & config, then migrate to create sends table.





## STEP 5: Configure Mail 

### in .env

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=YOUR_MAILTRAP_USERNAME
MAIL_PASSWORD=YOUR_MAILTRAP_PASSWORD
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="TrendyKart"

```

#### Explanation:

Update .env with SMTP credentials (Mailtrap recommended for testing).





## STEP 6: Add Event Listener for DB Storage

### Open app/Providers/EventServiceProvider.php and add:

```
protected $listen = [
        // This listens for every email sent
        \Illuminate\Mail\Events\MessageSent::class => [
            \Wnx\Sends\Listeners\StoreOutgoingMailListener::class,
        ],
    ];

```

#### Explanation:

Listen for every email sent and automatically store it in the database.






## STEP 7: Create Controller

### Run:

```
php artisan make:controller SendMailController

```

### app/Http/Controllers/SendMailController.php:

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Wnx\Sends\Models\Send; // Model to view DB entries

class SendMailController extends Controller
{
    // Show send email form
    public function index()
    {
        return view('mailform');
    }

    // Handle sending email
    public function send(Request $request)
    {
        $to = $request->to ?? 'recipient@example.com';

        $data = [
            'title' => 'Welcome to Laravel Sends',
            'body' => 'This is a test email using laravel-sends!'
        ];

        Mail::send('emails.hello', $data, function ($message) use ($to) {
            $message->to($to)
                    ->subject('Test Email from Laravel Sends');
        });

        return back()->with('success', "Email sent to $to!");
    }

    // Show all sent emails from DB
    public function allSends()
    {
        $emails = Send::latest()->get(); // fetch all emails
        return view('sends', compact('emails'));
    }
}

```

#### Explanation:

Controller to handle sending emails, storing in DB, and listing all emails.







## STEP 8: Create Views


### resources/views/mailform.blade.php

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Email</title>
    <style>
        /* Dark mode body */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a;
            color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Centered card */
        .card {
            background-color: #2b2b2b;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.7);
            width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #f5f5f5;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 12px 0;
            border: 1px solid #444;
            border-radius: 6px;
            background-color: #1e1e1e;
            color: #fff;
        }

        button {
            padding: 12px 20px;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            color: #66b2ff;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        .success {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Send Test Email</h1>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <form action="{{ url('/send-mail') }}" method="POST">
            @csrf
            <input type="email" name="to" placeholder="Recipient Email" required>
            <button type="submit">Send Email</button>
        </form>

        <a href="{{ url('/all-sends') }}">View All Sent Emails</a>
    </div>
</body>
</html>

```



### resources/views/sends.blade.php

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Sent Emails</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a;
            color: #e0e0e0;
            display: flex;
            justify-content: center;
            padding: 40px 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
        }

        h1 {
            text-align: center;
            color: #f5f5f5;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #2b2b2b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.7);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }

        th {
            background-color: #333;
            color: #66b2ff;
        }

        tr:nth-child(even) {
            background-color: #262626;
        }

        tr:hover {
            background-color: #3a3a3a;
        }

        a {
            display: block;
            margin-bottom: 15px;
            color: #66b2ff;
            text-decoration: none;
            font-size: 16px;
        }

        a:hover {
            text-decoration: underline;
        }

        td p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ url('/') }}">Send New Email</a>
        <h1>All Sent Emails</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>To</th>
                    <th>Subject</th>
                    <th>Body</th>
                    <th>Sent At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($emails as $email)
                <tr>
                    <td>{{ $email->id }}</td>
                    <td>{{ $email->to }}</td>
                    <td>{{ $email->subject }}</td>
                    <td>{!! $email->content !!}</td>
                    <td>{{ $email->sent_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

```

#### Explanation:

Blade templates for sending emails (mailform.blade.php) and viewing all sent emails (sends.blade.php).




## STEP 9: Add Routes

### routes/web.php:

```
use App\Http\Controllers\SendMailController;

Route::get('/', [SendMailController::class, 'index']);
Route::post('/send-mail', [SendMailController::class, 'send']);
Route::get('/all-sends', [SendMailController::class, 'allSends']);

```

#### Explanation:

Define web routes for form display, email sending, and viewing all sent emails.






## STEP 10: Create your own Send model

### Run:

```
php artisan make:model Send

```

### Then edit app/Models/Send.php:

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Send extends Model
{
    protected $table = 'sends';
    protected $fillable = [
        'uuid',
        'mail_class',
        'subject',
        'content',
        'from',
        'to',
        'sent_at',
    ];
    public $timestamps = true;
}

```

#### Explanation:

Model representing the sends table for interacting with email records.



## STEP 11: Create a Mailable:

### Run:

```
php artisan make:mail TestMail

```


### App/Mail/TestMail:

```
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

```

#### Explanation:

Optional Mailable class (TestMail) to structure email sending using Laravel's Mailable system.





## STEP 12:  Run the App  

### Start dev server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000

```

#### Explanation:

Start the Laravel server (php artisan serve) and open in browser to test sending and viewing emails.





## Expected Output:


### Main Dashboard Page:


<img src="screenshots/Screenshot 2026-03-27 171844.png" width="900">


### Email Form Page:


<img src="screenshots/Screenshot 2026-03-27 171958.png" width="900">


### Confirmation / Success Message:


<img src="screenshots/Screenshot 2026-03-27 172110.png" width="900">


### Sent Emails List / Email Log:


<img src="screenshots/Screenshot 2026-03-27 172124.png" width="900">


### Sample Received Email:


<img src="screenshots/Screenshot 2026-03-27 172153.png" width="900">



---



## Project Folder Structure:

```
PHP_Laravel11_Sends/
│
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── SendMailController.php    # Handles sending emails and viewing sent emails
│   │   └── Middleware/
│   ├── Mail/
│   │   └── TestMail.php                  # Mailable class for sending emails
│   ├── Models/
│   │   └── Send.php                      # Model representing 'sends' DB table
│   ├── Providers/
│   └── ...
│
├── bootstrap/
│   └── ...
│
├── config/
│   └── ...
│
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── xxxx_create_sends_table.php   # Table to store sent emails
│   │   └── (default Laravel migrations)
│   └── seeders/
│
├── public/
│   └── index.php
│
├── resources/
│   ├── views/
│   │   ├── emails/
│   │   │   └── hello.blade.php          # Email template
│   │   ├── mailform.blade.php           # Form to send test email
│   │   └── sends.blade.php              # List of sent emails
│   └── css/ (optional)
│
├── routes/
│   └── web.php                           # All web routes
│
├── storage/
│   └── ...
│
├── tests/
│   └── ...
│
├── vendor/
│   └── ...
│
├── .env                                  # Environment configuration
├── artisan                               # Laravel CLI
├── composer.json
└── composer.lock

```
