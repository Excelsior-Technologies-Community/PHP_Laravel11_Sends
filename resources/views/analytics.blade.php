<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body { background:#1a1a1a; color:#e0e0e0; font-family:'Segoe UI',sans-serif; }
        .navbar { background:#111; border-bottom:1px solid #333; }
        .navbar-brand { color:#66b2ff!important; font-weight:700; }
        .nav-link { color:#ccc!important; }
        .nav-link:hover { color:#66b2ff!important; }
        .stat-card { background:#222; border-radius:14px; padding:25px; text-align:center; border:1px solid #333; }
        .stat-card .number { font-size:2.5rem; font-weight:700; }
        .stat-card .label  { color:#999; font-size:0.9rem; margin-top:4px; }
        .card-dark { background:#222; border:1px solid #333; border-radius:14px; padding:25px; }
        .table { color:#e0e0e0; }
        .table thead { background:#333; }
        .table tbody tr:hover { background:#2a2a2a; }
        .badge-sent   { background:#1a3a2a; color:#6fcf97; padding:4px 10px; border-radius:20px; font-size:0.8rem; }
        .badge-failed { background:#3a1a1a; color:#ff6b6b; padding:4px 10px; border-radius:20px; font-size:0.8rem; }
        .badge-pending{ background:#2a2a1a; color:#f0c040; padding:4px 10px; border-radius:20px; font-size:0.8rem; }
        .progress { background:#333; height:10px; border-radius:10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/">📧 Laravel Sends</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">🏠 Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/all-sends') }}">📋 All Emails</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ url('/analytics') }}">📊 Analytics</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/campaigns') }}">📣 Campaigns</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/bulk-import') }}">📥 Bulk Import</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/mailer-switch') }}">🔀 Mailer Switch</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">

    <h3 class="mb-4">📊 Live Email Analytics Dashboard</h3>

    <!-- Stat Counters -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="number text-white">{{ $total }}</div>
                <div class="label">Total Emails</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="number" style="color:#6fcf97;">{{ $sent }}</div>
                <div class="label">Delivered</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="number" style="color:#ff6b6b;">{{ $failed }}</div>
                <div class="label">Failed</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="number" style="color:#f0c040;">{{ $pending }}</div>
                <div class="label">Pending</div>
            </div>
        </div>
    </div>

    <!-- Delivery Rate Bars -->
    <div class="card-dark mb-4">
        <h5 class="mb-3">📈 Delivery Rate Diagnostics</h5>
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span>Delivery Rate</span><span style="color:#6fcf97;">{{ $deliveryRate }}%</span>
            </div>
            <div class="progress">
                <div class="progress-bar bg-success" style="width:{{ $deliveryRate }}%"></div>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-between mb-1">
                <span>Failure Rate</span><span style="color:#ff6b6b;">{{ $failureRate }}%</span>
            </div>
            <div class="progress">
                <div class="progress-bar bg-danger" style="width:{{ $failureRate }}%"></div>
            </div>
        </div>
    </div>

    <!-- Daily Chart -->
    <div class="card-dark mb-4">
        <h5 class="mb-3">📅 Last 7 Days — Daily Email Activity</h5>
        <canvas id="dailyChart" height="80"></canvas>
    </div>

    <div class="row g-4">
        <!-- Top Recipients -->
        <div class="col-md-5">
            <div class="card-dark h-100">
                <h5 class="mb-3">🏆 Top Recipients</h5>
                <table class="table table-borderless">
                    <thead><tr><th>Email</th><th>Count</th></tr></thead>
                    <tbody>
                        @forelse($topRecipients as $r)
                        <tr>
                            <td>{{ $r->to }}</td>
                            <td><span class="badge bg-primary">{{ $r->count }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Emails -->
        <div class="col-md-7">
            <div class="card-dark h-100">
                <h5 class="mb-3">🕐 Recent 10 Emails</h5>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead><tr><th>To</th><th>Subject</th><th>Status</th><th>Sent At</th></tr></thead>
                        <tbody>
                            @forelse($recentEmails as $email)
                            <tr>
                                <td style="font-size:0.85rem;">{{ $email->to }}</td>
                                <td style="font-size:0.85rem;">{{ Str::limit($email->subject, 25) }}</td>
                                <td>
                                    @if($email->status === 'sent')
                                        <span class="badge-sent">SENT</span>
                                    @elseif($email->status === 'failed')
                                        <span class="badge-failed">FAILED</span>
                                    @else
                                        <span class="badge-pending">{{ strtoupper($email->status) }}</span>
                                    @endif
                                </td>
                                <td style="font-size:0.8rem;color:#999;">{{ $email->sent_at ? \Carbon\Carbon::parse($email->sent_at)->format('M d, H:i') : '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted">No emails yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
const dailyStats = @json($dailyStats);
const labels = dailyStats.map(d => d.date);
const sentData   = dailyStats.map(d => d.sent);
const failedData = dailyStats.map(d => d.failed);

new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Sent',
                data: sentData,
                backgroundColor: 'rgba(111,207,151,0.7)',
                borderRadius: 6,
            },
            {
                label: 'Failed',
                data: failedData,
                backgroundColor: 'rgba(255,107,107,0.7)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { color: '#ccc' } } },
        scales: {
            x: { ticks: { color: '#999' }, grid: { color: '#333' } },
            y: { ticks: { color: '#999' }, grid: { color: '#333' }, beginAtZero: true }
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
