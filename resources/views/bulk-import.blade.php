<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Import</title>
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
        .table { color:#e0e0e0; }
        .table thead { background:#333; }
        .table tbody tr:hover { background:#2a2a2a; }
        .badge-pending  { background:#2a2a1a; color:#f0c040; padding:3px 9px; border-radius:20px; font-size:0.78rem; }
        .badge-sent     { background:#1a3a2a; color:#6fcf97; padding:3px 9px; border-radius:20px; font-size:0.78rem; }
        .badge-failed   { background:#3a1a1a; color:#ff6b6b; padding:3px 9px; border-radius:20px; font-size:0.78rem; }
        .drop-zone { border:2px dashed #444; border-radius:12px; padding:40px; text-align:center; color:#666; cursor:pointer; transition:0.3s; }
        .drop-zone:hover { border-color:#66b2ff; color:#66b2ff; }
        .error-list { background:#2a1a1a; border:1px solid #5a2a2a; border-radius:8px; padding:12px; max-height:150px; overflow-y:auto; }
        .error-list li { font-size:0.82rem; color:#ff9999; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/">📧 Laravel Sends</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">🏠 Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/campaigns') }}">📣 Campaigns</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ url('/bulk-import') }}">📥 Bulk Import</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">

    <h3 class="mb-4">📥 Bulk Contact Importer</h3>

    @if(session('success'))
        <div class="alert" style="background:#1a3a2a;border-color:#28a745;color:#6fcf97;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert" style="background:#3a1a1a;border-color:#dc3545;color:#ff6b6b;">
            ❌ {{ session('error') }}
        </div>
    @endif
    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="mb-3">
            <p style="color:#f0c040;font-size:0.9rem;">⚠️ Row Errors ({{ count(session('import_errors')) }}):</p>
            <ul class="error-list">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        <!-- Upload Form -->
        <div class="col-md-5">
            <div class="card-dark">
                <h5 class="mb-3">📂 Upload CSV / Excel File</h5>

                <form action="{{ route('bulk-import.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label>Select Campaign (Optional)</label>
                        <select name="campaign_id" class="form-select">
                            <option value="">— No Campaign —</option>
                            @foreach($campaigns as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>CSV / Excel File</label>
                        <div class="drop-zone" onclick="document.getElementById('csvFile').click()">
                            <div>📁 Click to select file</div>
                            <small>Supported: .csv, .txt, .xlsx, .xls — Max 5MB</small>
                            <div id="fileName" style="margin-top:8px;color:#66b2ff;font-size:0.85rem;"></div>
                        </div>
                        <input type="file" id="csvFile" name="csv_file" accept=".csv,.txt,.xlsx,.xls"
                            style="display:none;" onchange="document.getElementById('fileName').textContent = this.files[0]?.name || ''">
                        @error('csv_file')
                            <div style="color:#ff6b6b;font-size:0.85rem;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" style="background:#1a2a1a;border-radius:8px;padding:12px;font-size:0.82rem;color:#6fcf97;">
                        <strong>CSV Format Required:</strong><br>
                        Row 1 must be header: <code>name,email</code><br>
                        Example:<br>
                        <code>name,email</code><br>
                        <code>John Doe,john@example.com</code>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">📤 Import Contacts</button>
                </form>
            </div>
        </div>

        <!-- Import Log -->
        <div class="col-md-7">
            <div class="card-dark">
                <h5 class="mb-3">📋 Imported Contacts Log</h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Campaign</th><th>Status</th><th>Sent At</th></tr>
                        </thead>
                        <tbody>
                            @forelse($imports as $contact)
                            <tr>
                                <td>{{ $contact->id }}</td>
                                <td>{{ $contact->name ?: '—' }}</td>
                                <td style="font-size:0.85rem;">{{ $contact->email }}</td>
                                <td style="font-size:0.82rem;color:#66b2ff;">{{ $contact->campaign?->name ?? '—' }}</td>
                                <td><span class="badge-{{ $contact->status }}">{{ strtoupper($contact->status) }}</span></td>
                                <td style="font-size:0.8rem;color:#999;">{{ $contact->sent_at ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">No contacts imported yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $imports->links() }}</div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
