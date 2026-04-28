<?php

namespace Modules\Catalogue\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Catalogue\Models\CatalogueItem;
use Modules\Catalogue\Services\ProductImageGeneratorService;
use Illuminate\Support\Facades\Log;

class GenerateMissingItemImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $itemId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * Execute the job.
     */
    public function handle(ProductImageGeneratorService $imageGenerator): void
    {
        $item = CatalogueItem::find($this->itemId);

        if (!$item) {
            return;
        }

        if ($item->images()->count() > 0) {
            Log::info("Item {$this->itemId} already has images. Skipping generation.");
            return;
        }

        \Illuminate\Support\Facades\Redis::throttle('gemini-image-gen')
            ->allow(20)
            ->every(60)
            ->then(function () use ($item, $imageGenerator) {
                try {
                    $imageGenerator->generateFor($item);
                    Log::info("Successfully generated image for item {$this->itemId}");
                } catch (\Exception $e) {
                    Log::error("Failed to generate image for item {$this->itemId}: " . $e->getMessage());
                    throw $e;
                }
            }, function () {
                $this->release(10);
            });
    }
}
