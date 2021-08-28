<?php

namespace LandKit\Uploader;

use Exception;

class Media extends Uploader
{
    /**
     * @var string[]
     */
    protected static $allowedTypes = [
        'audio/mp3',
        'audio/mpeg',
        'video/mp4',
    ];

    /**
     * @var string[]
     */
    protected static $extensions = [
        'mp3',
        'mp4'
    ];

    /**
     * @param array $media
     * @param string $name
     * @return string
     */
    public function upload(array $media, string $name): string
    {
        try {
            $this->extension = mb_strtolower(pathinfo($media['name'])['extension']);

            if (!in_array($media['type'], static::$allowedTypes) || !in_array($this->extension, static::$extensions)) {
                throw new Exception("'{$media['name']}' it is not a valid media type or extension.");
            }

            move_uploaded_file($media['tmp_name'], "{$this->path}/{$this->name($name)}");

            return "{$this->path}/{$this->name}";
        } catch (Exception $e) {
            $this->fail = $e;
            return '';
        }
    }
}