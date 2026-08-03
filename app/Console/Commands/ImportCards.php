<?php

namespace App\Console\Commands;

use App\Models\Card;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportCards extends Command
{
    protected $signature = 'app:import-cards';
    
    protected $description = 'Import Magic cards from Scryfall';

    public function handle()
{
    $this->info('Getting Scryfall card data...');

    // Get the current Scryfall bulk data URL
    $response = Http::timeout(30)
        ->withHeaders([
            'User-Agent' => 'MTG Deck Builder/1.0',
            'Accept' => 'application/json',
        ])
        ->get('https://api.scryfall.com/bulk-data/default-cards');

    if (! $response->successful()) {
        $this->error('Could not connect to Scryfall.');

        return self::FAILURE;
    }

    $downloadUrl = $response->json('jsonl_download_uri');

    if (! $downloadUrl) {
        $this->error('Could not find Scryfall download URL.');

        return self::FAILURE;
    }

    // Download the compressed file
    $this->info('Downloading card database...');

    $download = Http::timeout(600)
        ->withHeaders([
            'User-Agent' => 'MTG Deck Builder/1.0',
        ])
        ->get($downloadUrl);

    if (! $download->successful()) {
        $this->error('Failed to download Scryfall cards.');

        return self::FAILURE;
    }

    // Save the downloaded file
    $filePath = storage_path('app/scryfall_cards.jsonl.gz');

    file_put_contents($filePath, $download->body());

    $this->info('Download complete.');
    $this->info('Importing cards into database...');

    // Open the compressed JSONL file
    $file = gzopen($filePath, 'r');

    if ($file === false) {
        $this->error('Could not open Scryfall file.');

        return self::FAILURE;
    }

    $count = 0;

    while (($line = gzgets($file)) !== false) {

        $card = json_decode($line, true);

        if (! $card) {
            continue;
        }

        Card::updateOrCreate(
            [
                'name' => $card['name'],
            ],
            [
                'type_line' => $card['type_line'] ?? '',
                'color_identity' => $card['color_identity'] ?? [],
                'commander_legal' =>
                    ($card['legalities']['commander'] ?? 'not_legal')
                    === 'legal',
            ]
        );

        $count++;

        if ($count % 1000 === 0) {
            $this->info("Imported {$count} cards...");
        }
    }

    gzclose($file);

    $this->info("Successfully imported {$count} cards!");

    return self::SUCCESS;
    }
}