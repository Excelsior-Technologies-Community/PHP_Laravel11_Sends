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