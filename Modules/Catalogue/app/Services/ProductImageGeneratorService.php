<?php

namespace Modules\Catalogue\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Catalogue\Models\CatalogueItem;
use Modules\Catalogue\Models\CatalogueItemImage;

class ProductImageGeneratorService
{
    public function __construct()
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            throw new \Exception('Gemini API Key is not configured.');
        }
    }

    /**
     * Generate an image for a catalogue item
     *
     * @param CatalogueItem $item
     * @return CatalogueItemImage|null
     */
    public function generateFor(CatalogueItem $item): ?CatalogueItemImage
    {
        if ($item->images()->count() > 0) {
            return $item->primaryImage; // Already has an image
        }

        $productName = $item->product ? $item->product->name : $item->name;
        $categoryName = ($item->product && $item->product->category) ? $item->product->category->name : 'General';
        $modelOrTags = $item->tags ?? $item->sku;

        // 1. Generate descriptive image prompt using Gemini
        $prompt = $this->generateImagePrompt((string)$productName, (string)$categoryName, (string)$modelOrTags);

        // 2. Fetch image from pollinations.ai
        $imageContent = $this->fetchImageFromPollinations($prompt);

        if (!$imageContent) {
            return null;
        }

        // 3. Save Image locally
        $filename = 'generated_' . $item->sku . '_' . Str::random(5) . '.jpg';
        $path = 'products/images/' . $filename;
        
        Storage::disk('public')->put($path, $imageContent);

        // 4. Create CatalogueItemImage record
        return CatalogueItemImage::create([
            'catalogue_item_id' => $item->id,
            'image_path' => $path,
            'is_primary' => true,
            'order' => 1,
        ]);
    }

    /**
     * Use Gemini to create a highly detailed image generation prompt
     */
    private function generateImagePrompt(string $name, string $category, string $model): string
    {
        $instruction = <<<PROMPT
You are an expert product photographer and AI image prompt engineer.

Your task is to write a SHORT, ACCURATE image generation prompt for a product photo.

STRICT RULES:
1. Think carefully about what the product PHYSICALLY LOOKS LIKE in real life. A laptop is a laptop. A bag of cement is a bag. A printer is a printer. Do NOT default to a bottle or generic shape.
2. Describe the EXACT real-world physical form: its shape, size, color, packaging, material, and any visual details that are true to this type of product.
3. The image must have a clean white or light studio background.
4. Write ONLY the final prompt. No explanations, no bullet points, no extra text.
5. Keep the prompt under 80 words.
6. The prompt must start with a description of the product itself (e.g. "A bag of Holcim cement...", "A laptop computer...", "A laserjet printer...").

Product Name: {$name}
Category: {$category}
SKU/Detail: {$model}

Write the image prompt now:
PROMPT;

        $cacheKey = 'gemini_prompt_v2_' . md5($name . $category . $model);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addDays(30), function () use ($instruction, $name, $category) {
            $apiKey = config('services.gemini.api_key');

            $response = \Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $instruction]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 120,
                    'temperature' => 0.4, // Lower = more accurate/factual
                ]
            ]);

            if ($response->successful()) {
                $text = trim($response->json('candidates.0.content.parts.0.text') ?? '');
                if (!empty($text)) {
                    return $text;
                }
            }

            // Fallback: construct a basic but accurate prompt directly
            return "A professional product photo of {$name} ({$category}), showing its real-world physical form, clean white studio background, sharp focus, photorealistic, high quality";
        });
    }

    /**
     * Call Pollinations API to get the image content
     */
    private function fetchImageFromPollinations(string $prompt): ?string
    {
        // URL encode the prompt
        $encodedPrompt = urlencode($prompt);
        
        // Use a 800x800 resolution with no logo
        $url = "https://image.pollinations.ai/prompt/{$encodedPrompt}?width=800&height=800&nologo=true";

        try {
            $response = Http::timeout(60)->get($url);
            
            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }
}
