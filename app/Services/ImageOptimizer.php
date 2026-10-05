<?php

namespace App\Services;

use GdImage;
use RuntimeException;

class ImageOptimizer
{
    public const WIDTH = 1600;

    public const HEIGHT = 900;

    public const QUALITY = 80;

    /**
     * Cover-crop an image to 16:9, scale it down to at most 1600x900 (never upscaling) and encode as WebP.
     *
     * @return array{contents: string, width: int, height: int}
     */
    public function optimize(string $contents): array
    {
        $source = @imagecreatefromstring($contents);

        if (! $source instanceof GdImage) {
            throw new RuntimeException('The image could not be decoded.');
        }

        $source = $this->applyExifOrientation($source, $contents);

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        // Largest 16:9 region that fits inside the source, centered.
        $cropWidth = min($sourceWidth, (int) floor($sourceHeight * self::WIDTH / self::HEIGHT));
        $cropHeight = (int) floor($cropWidth * self::HEIGHT / self::WIDTH);
        $cropX = intdiv($sourceWidth - $cropWidth, 2);
        $cropY = intdiv($sourceHeight - $cropHeight, 2);

        $width = min($cropWidth, self::WIDTH);
        $height = (int) round($width * self::HEIGHT / self::WIDTH);

        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        imagecopyresampled($canvas, $source, 0, 0, $cropX, $cropY, $width, $height, $cropWidth, $cropHeight);

        ob_start();
        $encoded = imagewebp($canvas, null, self::QUALITY);
        $webp = (string) ob_get_clean();

        if (! $encoded || $webp === '') {
            throw new RuntimeException('The image could not be encoded as WebP.');
        }

        return ['contents' => $webp, 'width' => $width, 'height' => $height];
    }

    private function applyExifOrientation(GdImage $image, string $contents): GdImage
    {
        if (! function_exists('exif_read_data') || ! str_starts_with($contents, "\xFF\xD8")) {
            return $image;
        }

        $exif = @exif_read_data('data://image/jpeg;base64,'.base64_encode($contents));
        $rotated = match ($exif['Orientation'] ?? 1) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated instanceof GdImage ? $rotated : $image;
    }
}
