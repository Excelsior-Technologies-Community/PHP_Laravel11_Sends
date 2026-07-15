<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campaign: {{ $campaign->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#1a1a1a; color:#e0e0e0; font-family:'Segoe UI',sans-serif; }
        .navbar { background:#111; border-bottom:1px solid #333; }
        .navbar-brand { color:#66b2ff!important; font-weight:700; }
        .nav-link { color:#ccc!important; }
        .nav-link:hover { color:#66b2ff!important; }
        .card-dark { background:#222; border:1px solid #333; border-radius:14px; padding:25px; }
        .stat-pill { background:#2a2a2a; border-radius:10px; padding:15px 20px; text-align:center; }
        .stat-pill .num { font-size:1.8rem; font-weight:700; }
        .table { color:#e0e0e0; }
        .table thead { background:#333; }
        .table tbody tr:hover { background:#2a2a2a; }
        .badge-pending  { background:#2a2a1a; color:#f0c040; padding:3px 9px; border-radius:20px; font-size:0.78rem; }
        .badge-sent     { background:#1a3a2a; color:#6fcf97; padding:3px 9px; border-radius:20px; font-size:0.78rem; }
        .badge-failed   { background:#3a1a1a; color:#ff6b6b; padding:3px 9px; border-radius:20px; font-size:0.78rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/">📧 Laravel Sends</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">🏠 Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ url('/campaigns') }}">📣 Campaigns</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>📣 {{ $campaign->name }}</h3>
            <small style="color:#999;">Subject: {{ $campaign->subject }} &nbsp;|&nbsp; Mailer: <code style="color:#66b2ff;">{{ $campaign->mailer }}</code></small>
        </div>
        <div class="d-flex gap-2">
            @if(in_array($campaign->status, ['draft','scheduled']))
            <form action="{{ route('campaigns.dispatch', $campaign) }}" method="POST">
                @csrf
                <button class="btn btn-success" onclick="return confirm('Dispatch now?')">▶ Dispatch Now</button>
            </form>
            @endif
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">← Back</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert" style="background:#1a3a2a;border-color:#28a745;color:#6fcf97;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert" style="background:#3a1a1a;border-color:#dc3545;color:#ff6b6b;">{{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-pill">
                <div class="num text-white">{{ $campaign->total }}</div>
                <div style="color:#999;font-size:0.85rem;">Total</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-pill">
                <div class="num" style="color:#6fcf97;">{{ $campaign->sent_count }}</div>
                <div style="color:#999;font-size:0.85rem;">Sent</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-pill">
                <div class="num" style="color:#ff6b6b;">{{ $campaign->failed_count }}</div>
                <div style="color:#999;font-size:0.85rem;">Failed</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-pill">
                <div class="num" style="color:#66b2ff;">{{ strtoupper($campaign->status) }}</div>
                <div style="color:#999;font-size:0.85rem;">Status</div>
            </div>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="card-dark">
        <h5 class="mb-3">👥 Campaign Contacts</h5>
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Error</th><th>Sent At</th></tr>
                </thead>
                <tbody>
                    @forelse($contacts as $c)
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td>{{ $c->name ?: '—' }}</td>
                        <td>{{ $c->email }}</td>
                        <td><span class="badge-{{ $c->status }}">{{ strtoupper($c->status) }}</span></td>
                        <td style="font-size:0.8rem;color:#ff6b6b;">{{ $c->error ? Str::limit($c->error, 40) : '—' }}</td>
                        <td style="font-size:0.8rem;color:#999;">{{ $c->sent_at ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No contacts. Import via <a href="{{ url('/bulk-import') }}">Bulk Import</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $contacts->links() }}</div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
