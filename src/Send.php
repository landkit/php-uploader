<?php

namespace LandKit\Uploader;

use Exception;

class Send extends Uploader
{
    /**
     * Send constructor.
     *
     * @param string $uploadDir
     * @param string $fileTypeDir
     * @param array $allowedTypes
     * @param array $extensions
     * @param bool $yearMonthPath
     */
    public function __construct(
        string $uploadDir,
        string $fileTypeDir,
        array $allowedTypes,
        array $extensions,
        bool $yearMonthPath = true
    ) {
        parent::__construct($uploadDir, $fileTypeDir, $yearMonthPath);
        self::$allowedTypes = $allowedTypes;
        self::$extensions = $extensions;
    }

    /**
     * @param array $file
     * @param string $name
     * @return string
     */
    public function send(array $file, string $name): string
    {
        try {
            $this->extension = mb_strtolower(pathinfo($file['name'])['extension']);

            if (!in_array($file['type'], static::$allowedTypes) || !in_array($this->extension, static::$extensions)) {
                throw new Exception("'{$file['name']}' not a valid file type or extension.");
            }

            move_uploaded_file($file['tmp_name'], "{$this->path}/{$this->name($name)}");

            return "{$this->path}/{$this->name}";
        } catch (Exception $e) {
            $this->fail = $e;
            return '';
        }
    }
}