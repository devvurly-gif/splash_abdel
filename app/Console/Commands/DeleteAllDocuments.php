<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentHeader;
use App\Models\DocumentLine;
use App\Models\JournalStock;
use Illuminate\Support\Facades\DB;

class DeleteAllDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:delete-all {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all document headers and their associated lines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete ALL documents? This action cannot be undone!')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting deletion of all documents...');

        try {
            DB::transaction(function () {
                // Count documents before deletion
                $headersCount = DocumentHeader::count();
                $linesCount = DocumentLine::count();
                $journalCount = JournalStock::whereNotNull('document_header_id')->count();

                $this->info("Found {$headersCount} document headers, {$linesCount} document lines, and {$journalCount} journal entries.");

                // Delete journal entries related to documents
                $this->info('Deleting journal entries related to documents...');
                $deletedJournal = JournalStock::whereNotNull('document_header_id')->delete();
                $this->info("Deleted {$deletedJournal} journal entries.");

                // Delete document lines (cascade should handle this, but we do it explicitly)
                $this->info('Deleting document lines...');
                $deletedLines = DocumentLine::query()->delete();
                $this->info("Deleted {$deletedLines} document lines.");

                // Delete document headers
                $this->info('Deleting document headers...');
                $deletedHeaders = DocumentHeader::query()->delete();
                $this->info("Deleted {$deletedHeaders} document headers.");

                $this->info('All documents have been deleted successfully!');
            });

            $this->info('Operation completed successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
