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