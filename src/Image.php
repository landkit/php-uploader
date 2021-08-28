<?php

namespace LandKit\Uploader;

use Exception;

class Image extends Uploader
{
    /**
     * @var string[]
     */
    protected static $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    /**
     * @param array $image
     * @param string $name
     * @param int $width
     * @param array|null $quality
     * @return string
     */
    public function upload(array $image, string $name, int $width = 1920, array $quality = null): string
    {
        try {
            if (empty($image['type']) || !in_array($image['type'], self::$allowedTypes)) {
                throw new Exception("'{$image['name']}' is not a valid image file.");
            }

            if (!$this->imageCreate($image)) {
                throw new Exception("{$image['name']}' it is not a valid image type or extension.");
            }

            $this->name($name);

            if ($this->extension == 'gif') {
                move_uploaded_file($image['tmp_name'], "{$this->path}/{$this->name}");
                return "{$this->path}/{$this->name}";
            }

            $this->imageGenerate($width, ($quality ?? ['jpg' => 75, 'png' => 5]));

            return "{$this->path}/{$this->name}";
        } catch (Exception $e) {
            $this->fail = $e;
            return '';
        }
    }

    /**
     * @param array $image
     * @return bool
     */
    private function imageCreate(array $image): bool
    {
        if ($image['type'] == 'image/jpeg') {
            $this->file = imagecreatefromjpeg($image['tmp_name']);
            $this->extension = 'jpg';
            $this->checkAngle($image);

            return true;
        }

        if ($image['type'] == 'image/png') {
            $this->file = imagecreatefrompng($image['tmp_name']);
            $this->extension = 'png';

            return true;
        }

        if ($image['type'] == 'image/gif') {
            $this->extension = 'gif';
            return true;
        }

        return false;
    }

    /**
     * @param int $width
     * @param array $quality
     * @return void
     */
    private function imageGenerate(int $width, array $quality)
    {
        $fileX = imagesx($this->file);
        $fileY = imagesy($this->file);

        $imageWidth = $width < $fileX ? $width : $fileX;
        $imageHeight = ($imageWidth * $fileY) / $fileX;

        $imageCreate = imagecreatetruecolor($imageWidth, $imageHeight);

        if ($this->extension == 'jpg') {
            imagecopyresampled($imageCreate, $this->file, 0, 0, 0, 0, $imageWidth, $imageHeight, $fileX, $fileY);
            imagejpeg($imageCreate, "{$this->path}/{$this->name}", $quality['jpg']);
        }

        if ($this->extension == 'png') {
            imagealphablending($imageCreate, false);
            imagesavealpha($imageCreate, true);
            imagecopyresampled($imageCreate, $this->file, 0, 0, 0, 0, $imageWidth, $imageHeight, $fileX, $fileY);
            imagepng($imageCreate, "{$this->path}/{$this->name}", $quality['png']);
        }

        imagedestroy($this->file);
        imagedestroy($imageCreate);
    }

    /**
     * @param array $image
     * @return void
     */
    private function checkAngle(array $image)
    {
        $exif = @exif_read_data($image['tmp_name']);
        $orientation = empty($exif['Orientation']) ? null : $exif['Orientation'];

        switch ($orientation) {
            case 8:
                $this->file = imagerotate($this->file, 90, 0);
                break;
            case 3:
                $this->file = imagerotate($this->file, 180, 0);
                break;
            case 6:
                $this->file = imagerotate($this->file, -90, 0);
                break;
        }
    }
}