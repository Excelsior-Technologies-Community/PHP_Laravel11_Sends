<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BulkImportController extends Controller
{
    // ── Feature 4: CSV/Excel Bulk Contact Importer ──────────────────────────

    public function index()
    {
        $campaigns = Campaign::pluck('name', 'id');
        $imports   = CampaignContact::with('campaign')
            ->latest()
            ->paginate(15);

        return view('bulk-import', compact('campaigns', 'imports'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file'    => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        $file        = $request->file('csv_file');
        $campaignId  = $request->campaign_id;
        $inserted    = 0;
        $skipped     = 0;
        $errors      = [];

        // Open file handle for buffered streaming (memory efficient)
        $handle = fopen($file->getRealPath(), 'r');

        // Skip header row
        $header = fgetcsv($handle);

        // Detect column positions dynamically
        $header     = array_map('strtolower', array_map('trim', $header));
        $emailIndex = array_search('email', $header);
        $nameIndex  = array_search('name', $header);

        if ($emailIndex === false) {
            fclose($handle);
            return back()->with('error', 'CSV must have an "email" column header.');
        }

        $batchSize = 200;
        $batch     = [];
        $rowNum    = 1;

        // Buffered row-by-row parsing
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $email = trim($row[$emailIndex] ?? '');
            $name  = isset($nameIndex) && $nameIndex !== false ? trim($row[$nameIndex] ?? '') : null;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Invalid email '{$email}'";
                continue;
            }

            $batch[] = [
                'campaign_id' => $campaignId,
                'name'        => $name,
                'email'       => $email,
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            // Flush batch to DB every 200 rows
            if (count($batch) >= $batchSize) {
                CampaignContact::insert($batch);
                $inserted += count($batch);
                $batch = [];
            }
        }

        // Insert remaining rows
        if (!empty($batch)) {
            CampaignContact::insert($batch);
            $inserted += count($batch);
        }

        fclose($handle);

        // Update campaign total if linked
        if ($campaignId) {
            $total = CampaignContact::where('campaign_id', $campaignId)->count();
            Campaign::where('id', $campaignId)->update(['total' => $total]);
        }

        Log::info("[BulkImport] Inserted: {$inserted}, Skipped: {$skipped}");

        $msg = "Imported {$inserted} contacts.";
        if ($skipped > 0) {
            $msg .= " Skipped {$skipped} invalid rows.";
        }

        return back()->with('success', $msg)->with('import_errors', $errors);
    }
}
