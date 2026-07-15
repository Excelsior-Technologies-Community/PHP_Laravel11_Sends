<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Campaign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#1a1a1a; color:#e0e0e0; font-family:'Segoe UI',sans-serif; }
        .navbar { background:#111; border-bottom:1px solid #333; }
        .navbar-brand { color:#66b2ff!important; font-weight:700; }
        .nav-link { color:#ccc!important; }
        .nav-link:hover { color:#66b2ff!important; }
        .form-card { background:#222; border:1px solid #333; border-radius:14px; padding:35px; max-width:700px; margin:0 auto; }
        label { color:#aaa; font-size:0.9rem; }
        input, select, textarea { background:#1e1e1e!important; color:#fff!important; border-color:#444!important; }
        input::placeholder, textarea::placeholder { color:#666!important; }
        .form-text { color:#666; }
        .alert-danger { background:#3a1a1a; border-color:#dc3545; color:#ff6b6b; }
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
                <li class="nav-item"><a class="nav-link" href="{{ url('/bulk-import') }}">📥 Bulk Import</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="form-card">
        <h4 class="mb-4">📣 Create New Campaign</h4>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('campaigns.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Campaign Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Summer Sale 2026" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label>Email Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="Email subject line" value="{{ old('subject') }}" required>
            </div>

            <div class="mb-3">
                <label>Email Body</label>
                <textarea name="body" class="form-control" rows="5" placeholder="Write your email content here..." required>{{ old('body') }}</textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label>Email Template</label>
                    <select name="template" class="form-select">
                        @foreach($templates as $t)
                            <option value="{{ $t }}" {{ old('template') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Mailer Provider</label>
                    <select name="mailer" class="form-select">
                        @foreach($mailers as $m)
                            <option value="{{ $m }}" {{ old('mailer') == $m ? 'selected' : '' }}>{{ strtoupper($m) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label>Schedule Date & Time</label>
                <input type="datetime-local" name="scheduled_at" class="form-control"
                    value="{{ old('scheduled_at') }}" required>
                <div class="form-text">Must be a future date/time.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">📅 Schedule Campaign</button>
                <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
