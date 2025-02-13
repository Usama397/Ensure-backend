<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path(config('services.google.sheets_credentials')));
        $this->client->addScope(Google_Service_Sheets::SPREADSHEETS);
        $this->service = new Google_Service_Sheets($this->client);
        $this->spreadsheetId = config('services.google.sheet_id');
    }

    public function appendData($data)
    {
        $range = 'Sheet1!A1'; // Adjust based on your sheet
        $body = new \Google_Service_Sheets_ValueRange([
            'values' => [$data]
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);
    }
}
