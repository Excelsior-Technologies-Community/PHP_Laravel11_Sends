<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mailer Switch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#1a1a1a; color:#e0e0e0; font-family:'Segoe UI',sans-serif; }
        .navbar { background:#111; border-bottom:1px solid #333; }
        .navbar-brand { color:#66b2ff!important; font-weight:700; }
        .nav-link { color:#ccc!important; }
        .nav-link:hover { color:#66b2ff!important; }
        .card-dark { background:#222; border:1px solid #333; border-radius:14px; padding:25px; }
        label { color:#aaa; font-size:0.9rem; }
        input, select { background:#1e1e1e!important; color:#fff!important; border-color:#444!important; }
        .mailer-card { background:#1e1e1e; border:1px solid #333; border-radius:10px; padding:18px; transition:0.3s; }
        .mailer-card.active-mailer { border-color:#66b2ff; background:#1a2a3a; }
        .mailer-card h6 { color:#fff; font-weight:600; margin-bottom:4px; }
        .status-online  { color:#6fcf97; font-size:0.82rem; }
        .status-offline { color:#ff6b6b; font-size:0.82rem; }
        .status-unknown { color:#999;    font-size:0.82rem; }
        .log-entry { background:#1e1e1e; border-radius:8px; padding:10px 14px; margin-bottom:8px; font-size:0.83rem; border-left:3px solid #444; }
        .log-entry .from { color:#ff9999; }
        .log-entry .to   { color:#6fcf97; }
        .log-entry .time { color:#666; font-size:0.78rem; }
        .log-entry .reason { color:#f0c040; }
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
                <li class="nav-item"><a class="nav-link" href="{{ url('/campaigns') }}">📣 Campaigns</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ url('/mailer-switch') }}">🔀 Mailer Switch</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">

    <h3 class="mb-1">🔀 Multi-Mailer Provider Switcher</h3>
    <p style="color:#999;font-size:0.9rem;">Test connections, manually switch providers, and view auto-failover logs.</p>

    @if(session('success'))
        <div class="alert" style="background:#1a3a2a;border-color:#28a745;color:#6fcf97;">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert" style="background:#3a1a1a;border-color:#dc3545;color:#ff6b6b;">❌ {{ session('error') }}</div>
    @endif

    <!-- Active Mailer Banner -->
    <div class="card-dark mb-4" style="border-color:#66b2ff;">
        <div class="d-flex align-items-center gap-3">
            <div style="font-size:2rem;">⚡</div>
            <div>
                <div style="color:#999;font-size:0.85rem;">Currently Active Mailer</div>
                <div style="font-size:1.6rem;font-weight:700;color:#66b2ff;">{{ strtoupper($activeMailer) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Mailer Cards -->
        <div class="col-md-8">
            <div class="card-dark">
                <h5 class="mb-3">📡 Available Mailers</h5>
                <div class="row g-3">
                    @foreach($mailers as $mailer)
                    @php
                        $status = $mailerStatus[$mailer] ?? null;
                        $isActive = $mailer === $activeMailer;
                    @endphp
                    <div class="col-md-4">
                        <div class="mailer-card {{ $isActive ? 'active-mailer' : '' }}">
                            <h6>
                                {{ strtoupper($mailer) }}
                                @if($isActive)
                                    <span style="font-size:0.7rem;background:#1a3a5a;color:#66b2ff;padding:2px 7px;border-radius:10px;margin-left:4px;">ACTIVE</span>
                                @endif
                            </h6>
                            @if($status)
                                @if($status['state'] === 'online')
                                    <div class="status-online">● Online</div>
                                @else
                                    <div class="status-offline">● Offline</div>
                                    <div style="font-size:0.75rem;color:#888;margin-top:2px;">{{ Str::limit($status['error'] ?? '', 35) }}</div>
                                @endif
                                <div class="time" style="color:#555;font-size:0.75rem;margin-top:3px;">Checked: {{ $status['checked_at'] }}</div>
                            @else
                                <div class="status-unknown">● Not tested</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="col-md-4">

            <!-- Manual Switch -->
            <div class="card-dark mb-3">
                <h6 class="mb-3">🔄 Manual Switch</h6>
                <form action="{{ route('mailer-switch.switch') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <select name="mailer" class="form-select form-select-sm">
                            @foreach($mailers as $m)
                                <option value="{{ $m }}" {{ $m === $activeMailer ? 'selected' : '' }}>{{ strtoupper($m) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Switch Mailer</button>
                </form>
            </div>

            <!-- Test Connection -->
            <div class="card-dark">
                <h6 class="mb-3">🧪 Test Connection</h6>
                <form action="{{ route('mailer-switch.test') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <select name="mailer" class="form-select form-select-sm">
                            @foreach($mailers as $m)
                                <option value="{{ $m }}">{{ strtoupper($m) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <input type="email" name="test_to" class="form-control form-control-sm"
                            placeholder="Send test to email">
                    </div>
                    <button type="submit" class="btn btn-outline-info btn-sm w-100">Test & Send</button>
                </form>
            </div>

        </div>
    </div>

    <!-- Switch Log -->
    <div class="card-dark mt-4">
        <h5 class="mb-3">📜 Failover / Switch Log</h5>
        @if(empty($switchLog))
            <p style="color:#666;font-size:0.9rem;">No switches recorded yet.</p>
        @else
            @foreach(array_reverse($switchLog) as $log)
            <div class="log-entry">
                <span class="from">{{ strtoupper($log['from']) }}</span>
                <span style="color:#555;"> → </span>
                <span class="to">{{ strtoupper($log['to']) }}</span>
                &nbsp;&nbsp;
                <span class="reason">{{ $log['reason'] }}</span>
                <div class="time">{{ $log['switched_at'] }}</div>
            </div>
            @endforeach
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
