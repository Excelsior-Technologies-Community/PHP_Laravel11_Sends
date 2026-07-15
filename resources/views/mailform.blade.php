<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Sends — Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#1a1a1a; color:#e0e0e0; font-family:'Segoe UI',sans-serif; }
        .navbar { background:#111; border-bottom:1px solid #333; }
        .navbar-brand { color:#66b2ff!important; font-weight:700; font-size:1.3rem; }
        .nav-link { color:#ccc!important; }
        .nav-link:hover { color:#66b2ff!important; }
        .hero { background:linear-gradient(135deg,#1e3a5f,#0d1b2a); border-radius:16px; padding:50px 40px; text-align:center; margin-top:40px; }
        .hero h1 { font-size:2.2rem; color:#fff; }
        .hero p { color:#aaa; font-size:1rem; }
        .feature-card { background:#222; border:1px solid #333; border-radius:12px; padding:25px; height:100%; transition:0.3s; }
        .feature-card:hover { border-color:#66b2ff; transform:translateY(-3px); }
        .feature-card .icon { font-size:2rem; margin-bottom:12px; }
        .feature-card h5 { color:#fff; font-weight:600; }
        .feature-card p { color:#999; font-size:0.88rem; }
        .btn-feature { background:#007bff; color:#fff; border:none; border-radius:8px; padding:8px 18px; font-size:0.85rem; text-decoration:none; display:inline-block; margin-top:10px; }
        .btn-feature:hover { background:#0056b3; color:#fff; }
        .send-card { background:#222; border-radius:16px; padding:35px; max-width:480px; margin:0 auto; }
        input, select { background:#1e1e1e!important; color:#fff!important; border-color:#444!important; }
        input::placeholder { color:#777!important; }
        .alert-success { background:#1a3a2a; border-color:#28a745; color:#6fcf97; }
        .alert-danger  { background:#3a1a1a; border-color:#dc3545; color:#ff6b6b; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/">📧 Laravel Sends</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/all-sends') }}">📋 All Emails</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/analytics') }}">📊 Analytics</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/campaigns') }}">📣 Campaigns</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/bulk-import') }}">📥 Bulk Import</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/mailer-switch') }}">🔀 Mailer Switch</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">

    <div class="hero">
        <h1>🚀 Laravel 11 Mail System</h1>
        <p>Advanced email management with queue, analytics, campaigns, bulk import & failover</p>
    </div>

    <!-- Send Form -->
    <div class="send-card mt-5">
        <h4 class="text-center mb-4" style="color:#fff;">✉️ Send Test Email</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ url('/send-mail') }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="email" name="to" class="form-control" placeholder="Recipient Email" required>
            </div>
            <div class="mb-3">
                <select name="mailer" class="form-select">
                    @foreach(array_keys(config('mail.mailers')) as $m)
                        <option value="{{ $m }}">{{ strtoupper($m) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                📤 Queue & Send Email
            </button>
        </form>
    </div>

    <!-- Feature Cards -->
    <div class="row g-4 mt-4 mb-5">
        <div class="col-md-4">
            <div class="feature-card">
                <div class="icon">⚡</div>
                <h5>Queue + Rate Limiter</h5>
                <p>Max 50 emails/min throttle. Auto-retry 3x with 60s backoff. Burst protection.</p>
                <a href="{{ url('/all-sends') }}" class="btn-feature">View Logs</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="icon">📊</div>
                <h5>Live Analytics</h5>
                <p>Delivery rates, failure diagnostics, daily charts, top recipients tracking.</p>
                <a href="{{ url('/analytics') }}" class="btn-feature">Open Dashboard</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="icon">📣</div>
                <h5>Campaign Scheduler</h5>
                <p>Schedule campaigns to future dates. Select template, mailer, and dispatch.</p>
                <a href="{{ url('/campaigns') }}" class="btn-feature">Manage Campaigns</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="icon">📥</div>
                <h5>Bulk CSV Import</h5>
                <p>Upload CSV/Excel with email list. Buffered row parsing, batch DB insert.</p>
                <a href="{{ url('/bulk-import') }}" class="btn-feature">Import Contacts</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="icon">🔀</div>
                <h5>Mailer Failover</h5>
                <p>Test mailer connections. Auto-switch on failure. Full switch log tracking.</p>
                <a href="{{ url('/mailer-switch') }}" class="btn-feature">Manage Mailers</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="icon">📋</div>
                <h5>Email Logs</h5>
                <p>Search, filter by status, paginated log of all sent & failed emails.</p>
                <a href="{{ url('/all-sends') }}" class="btn-feature">View All Emails</a>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
