<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeHelper
{
    public static function generateSvg(string $data, int $size = 100): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd
        );
        $writer = new Writer($renderer);

        return $writer->writeString($data);
    }

    public static function generateBase64Svg(string $data, int $size = 100): string
    {
        $svg = self::generateSvg($data, $size);
        $svg = preg_replace('/<\?xml.*?\?>/', '', $svg);

        return 'data:image/svg+xml;base64,'.base64_encode(trim($svg));
    }
}
