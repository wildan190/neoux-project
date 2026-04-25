<?php

namespace Modules\Catalogue\Console;

use Illuminate\Console\Command;
use Modules\Catalogue\Models\CatalogueItem;
use Modules\Catalogue\Jobs\GenerateMissingItemImageJob;

class GenerateMissingImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'catalogue:generate-missing-images {--limit=10 : The maximum number of images to generate}';

    /**
     * The console command description.
     */
    protected $description = 'Dispatches jobs to generate images using AI for products that are missing them.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for products with missing images...');

        $limit = $this->option('limit');

        $itemsWithoutImages = CatalogueItem::whereDoesntHave('images')
            ->where('is_active', true)
            ->take($limit)
            ->get();

        if ($itemsWithoutImages->isEmpty()) {
            $this->info('No active products found without images.');
            return 0;
        }

        $this->info("Found {$itemsWithoutImages->count()} products without images. Dispatching generation jobs...");

        $bar = $this->output->createProgressBar(count($itemsWithoutImages));

        $bar->start();

        foreach ($itemsWithoutImages as $item) {
            // Dispatch the generation job for each item
            GenerateMissingItemImageJob::dispatch($item->id);
            $bar->advance();
        }

        $bar->finish();
        
        $this->newLine();
        $this->info('Jobs dispatched successfully!');

        return 0;
    }
}
