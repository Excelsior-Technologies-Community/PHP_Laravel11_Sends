<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Sent Emails</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #1a1a1a;
            color: white;
            font-family: Arial, sans-serif;
        }

        .container {
            padding-top: 30px;
        }

        .card-custom {
            background: #222;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 0 20px rgba(255,255,255,0.05);
        }

        .table {
            color: white;
        }

        .table thead {
            background: #333;
        }

        .table tbody tr:nth-child(even) {
            background: #2a2a2a;
        }

        .table tbody tr:hover {
            background: #3a3a3a;
        }

        .sent {
            color: lightgreen;
            font-weight: bold;
        }

        .failed {
            color: #ff6b6b;
            font-weight: bold;
        }

        .pagination {
            justify-content: center;
        }

        .page-link {
            background: #2a2a2a;
            color: white;
            border: 1px solid #444;
        }

        .page-link:hover {
            background: #444;
            color: white;
        }

        .page-item.active .page-link {
            background: #0d6efd;
            border-color: #0d6efd;
        }

        a {
            text-decoration: none;
        }

        h2 {
            margin-bottom: 25px;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="{{ url('/') }}" class="btn btn-outline-light mb-3">
        ← Send New Email
    </a>

    <div class="card-custom">

        <h2>📧 All Sent Emails</h2>

        <!-- SEARCH + FILTER -->
        <form method="GET" class="row g-3">

            <div class="col-md-5">
                <input
                    type="text"
                    class="form-control"
                    name="search"
                    placeholder="Search email or subject"
                    value="{{ request('search') }}">
            </div>

            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="">All Status</option>

                    <option value="sent"
                        {{ request('status')=='sent' ? 'selected' : '' }}>
                        Sent
                    </option>

                    <option value="failed"
                        {{ request('status')=='failed' ? 'selected' : '' }}>
                        Failed
                    </option>
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary w-100">
                    Filter
                </button>
            </div>

        </form>


        <div class="table-responsive mt-4">

            <table class="table table-bordered table-dark align-middle">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>To</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Message</th>
                        <th>Sent At</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($emails as $email)

                    <tr>

                        <td>{{ $email->id }}</td>

                        <td>{{ $email->to }}</td>

                        <td>{{ $email->subject }}</td>

                        <td>
                            <span class="{{ $email->status }}">
                                {{ strtoupper($email->status) }}
                            </span>
                        </td>

                        <td>
                            {{ $email->content }}
                        </td>

                        <td>
                            {{ $email->sent_at }}
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            No emails found
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- Bootstrap Pagination -->
        <div class="mt-4 d-flex justify-content-center">

            {{ $emails->appends(request()->query())->links() }}

        </div>

    </div>

</div>

</body>
</html>