<?php

namespace App\Helpers;

class ImageHash
{
    /**
     * Generate a difference hash (dHash) for an image.
     *
     * @param string $path Path to the image file
     * @return string|null 64-bit hex hash
     */
    public static function dhash($path)
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $info = getimagesize($path);
        if (!$info) return null;

        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $img = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $img = imagecreatefrompng($path);
                break;
            case IMAGETYPE_WEBP:
                $img = imagecreatefromwebp($path);
                break;
            default:
                return null;
        }

        if (!$img) return null;

        // Resize to 9x8
        $small = imagecreatetruecolor(9, 8);
        imagecopyresampled($small, $img, 0, 0, 0, 0, 9, 8, $info[0], $info[1]);

        // Grayscale and compare
        $pixels = [];
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 9; $x++) {
                $rgb = imagecolorat($small, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $gray = (int)($r * 0.299 + $g * 0.587 + $b * 0.114);
                $pixels[$y][$x] = $gray;
            }
        }

        imagedestroy($small);
        imagedestroy($img);

        $hash = '';
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $hash .= ($pixels[$y][$x] < $pixels[$y][$x + 1]) ? '1' : '0';
            }
        }

        // Convert binary string to hex
        return base_convert($hash, 2, 16);
    }

    /**
     * Calculate Hamming distance between two hashes.
     */
    public static function distance($hash1, $hash2)
    {
        $bin1 = str_pad(base_convert($hash1, 16, 2), 64, '0', STR_PAD_LEFT);
        $bin2 = str_pad(base_convert($hash2, 16, 2), 64, '0', STR_PAD_LEFT);

        $distance = 0;
        for ($i = 0; $i < 64; $i++) {
            if ($bin1[$i] !== $bin2[$i]) {
                $distance++;
            }
        }

        return $distance;
    }
}
