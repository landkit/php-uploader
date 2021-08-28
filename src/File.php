<?php

namespace LandKit\Uploader;

use Exception;

class File extends Uploader
{
    /**
     * @var string[]
     */
    protected static $allowedTypes = [
        'application/zip',
        'application/x-rar-compressed',
        'application/x-bzip',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/csv',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.text',
    ];

    /**
     * @var string[]
     */
    protected static $extensions = [
        'zip',
        'rar',
        'bz',
        'pdf',
        'doc',
        'docx',
        'csv',
        'xls',
        'xlsx',
        'ods',
        'odt'
    ];

    /**
     * @param array $file
     * @param string $name
     * @return string
     */
    public function upload(array $file, string $name): string
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