<?php

namespace LandKit\Uploader;

use Exception;

use function LandKit\Functions\strSlug;

abstract class Uploader
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var resource
     */
    protected $file;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var Exception|null
     */
    protected $fail;

    /**
     * @var array
     */
    protected static $allowedTypes = [];

    /**
     * @var array
     */
    protected static $extensions = [];

    /**
     * Create new Uploader instance.
     *
     * @param string $uploadDir
     * @param string $fileTypeDir
     * @param bool $yearMonthPath
     */
    public function __construct(string $uploadDir, string $fileTypeDir, bool $yearMonthPath = true)
    {
        $this->path = "{$uploadDir}/{$fileTypeDir}";
        $this->file = null;
        $this->name = '';
        $this->extension = '';
        $this->fail = null;

        $this->dir($uploadDir);
        $this->dir("{$uploadDir}/{$fileTypeDir}");

        if ($yearMonthPath) {
            $this->path("{$uploadDir}/{$fileTypeDir}");
        }
    }

    /**
     * @return array
     */
    public static function isAllowed(): array
    {
        return static::$allowedTypes;
    }

    /**
     * @return array
     */
    public static function isExtension(): array
    {
        return static::$extensions;
    }

    /**
     * @return Exception|null
     */
    public function fail()
    {
        return $this->fail;
    }

    /**
     * @param string $inputName
     * @param array $files
     * @return array
     */
    public function multiple(string $inputName, array $files): array
    {
        $gbFiles = [];
        $gbCount = count($files[$inputName]['name']);
        $gbKeys = array_keys($files[$inputName]);

        for ($i = 0; $i < $gbCount; $i++) {
            foreach ($gbKeys as $key) {
                $gbFiles[$i][$key] = $files[$inputName][$key][$i];
            }
        }

        return $gbFiles;
    }

    /**
     * @param string $filePath
     * @return void
     */
    public function remove(string $filePath)
    {
        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * @param string $value
     * @return string
     */
    protected function name(string $value): string
    {
        $value = strSlug($value);
        $this->name = "{$value}.{$this->extension}";

        if (file_exists("{$this->path}/{$this->name}") && is_file("{$this->path}/{$this->name}")) {
            $this->name = "{$value}-" . time() . ".{$this->extension}";
        }

        return $this->name;
    }

    /**
     * @param string $value
     * @param int $mode
     * @return void
     */
    protected function dir(string $value, int $mode = 0755)
    {
        if (!file_exists($value) || !is_dir($value)) {
            mkdir($value, $mode, true);
        }
    }

    /**
     * @param string $value
     * @return void
     */
    protected function path(string $value)
    {
        list($yearPath, $monthPath) = explode('/', date('Y/m'));

        $this->dir("{$value}/{$yearPath}");
        $this->dir("{$value}/{$yearPath}/{$monthPath}");
        $this->path = "{$value}/{$yearPath}/{$monthPath}";
    }
}