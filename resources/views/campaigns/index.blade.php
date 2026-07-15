<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campaigns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#1a1a1a; color:#e0e0e0; font-family:'Segoe UI',sans-serif; }
        .navbar { background:#111; border-bottom:1px solid #333; }
        .navbar-brand { color:#66b2ff!important; font-weight:700; }
        .nav-link { color:#ccc!important; }
        .nav-link:hover { color:#66b2ff!important; }
        .card-dark { background:#222; border:1px solid #333; border-radius:14px; padding:25px; }
        .table { color:#e0e0e0; }
        .table thead { background:#333; }
        .table tbody tr:hover { background:#2a2a2a; }
        .badge-draft     { background:#2a2a2a; color:#aaa;    padding:4px 10px; border-radius:20px; font-size:0.8rem; }
        .badge-scheduled { background:#1a2a3a; color:#66b2ff; padding:4px 10px; border-radius:20px; font-size:0.8rem; }
        .badge-running   { background:#2a1a3a; color:#c084fc; padding:4px 10px; border-radius:20px; font-size:0.8rem; }
        .badge-completed { background:#1a3a2a; color:#6fcf97; padding:4px 10px; border-radius:20px; font-size:0.8rem; }
        .badge-failed    { background:#3a1a1a; color:#ff6b6b; padding:4px 10px; border-radius:20px; font-size:0.8rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/">📧 Laravel Sends</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">🏠 Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/analytics') }}">📊 Analytics</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ url('/campaigns') }}">📣 Campaigns</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/bulk-import') }}">📥 Bulk Import</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/mailer-switch') }}">🔀 Mailer Switch</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>📣 Email Campaigns</h3>
        <a href="{{ route('campaigns.create') }}" class="btn btn-primary">+ New Campaign</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background:#1a3a2a;border-color:#28a745;color:#6fcf97;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="background:#3a1a1a;border-color:#dc3545;color:#ff6b6b;">{{ session('error') }}</div>
    @endif

    <div class="card-dark">
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Subject</th><th>Mailer</th>
                        <th>Status</th><th>Scheduled At</th><th>Contacts</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                    <tr>
                        <td>{{ $campaign->id }}</td>
                        <td><strong>{{ $campaign->name }}</strong></td>
                        <td>{{ Str::limit($campaign->subject, 30) }}</td>
                        <td><code style="color:#66b2ff;">{{ $campaign->mailer }}</code></td>
                        <td><span class="badge-{{ $campaign->status }}">{{ strtoupper($campaign->status) }}</span></td>
                        <td style="font-size:0.85rem;color:#999;">{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d Y, H:i') : '—' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $campaign->contacts_count }}</span>
                        </td>
                        <td>
                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-outline-info me-1">View</a>
                            @if(in_array($campaign->status, ['draft','scheduled']))
                            <form action="{{ route('campaigns.dispatch', $campaign) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success"
                                    onclick="return confirm('Dispatch this campaign now?')">
                                    ▶ Dispatch
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No campaigns yet. <a href="{{ route('campaigns.create') }}">Create one</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $campaigns->links() }}</div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
