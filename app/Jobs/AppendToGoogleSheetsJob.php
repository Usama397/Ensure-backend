<?php

namespace App\Jobs;

use App\Services\GoogleSheetsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AppendToGoogleSheetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        try {
            Log::info('Google Sheets Job Started', ['data' => $this->data]);
    
            $sheetsService = new GoogleSheetsService();
            $sheetsService->appendData($this->data);
    
            Log::info('Google Sheets Job Completed Successfully');
        } catch (\Exception $e) {
            Log::error("Google Sheets API Error: " . $e->getMessage());
        }
    }
}


