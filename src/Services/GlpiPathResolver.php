<?php

declare(strict_types=1);

namespace PHPStanGlpi\Services;

use PHPStan\File\FileHelper;

class GlpiPathResolver
{
    private FileHelper $fileHelper;

    private ?string $path;

    public function __construct(FileHelper $fileHelper, ?string $path)
    {
        $this->fileHelper = $fileHelper;
        $this->path = $path !== null ? $this->fileHelper->normalizePath($path) : null;
    }

    /**
     * Get the GLPI path.
     *
     * @throws \LogicException
     */
    public function getGlpiPath(): string
    {
        if ($this->path !== null) {
            return $this->path;
        }

        $expected_directories = [
            // Expected directory when `phpstan-glpi` in required by GLPI itself:
            // `glpi/` <- `vendor/` <- `glpi-project/` <- `phpstan-glpi/` <- `src/` <- `Services/`
            \dirname(__DIR__, 5),

            // Expected directory when `phpstan-glpi` in required a GLPI plugin:
            // `glpi/` <- `plugins/` <- `{$plugin_key}/` <- `vendor/` <- `glpi-project/` <- `phpstan-glpi/` <- `src/` <- `Services/`
            \dirname(__DIR__, 7),
        ];

        foreach ($expected_directories as $directory) {
            $commonGlpiFile = $this->fileHelper->normalizePath($directory . '/src/CommonGLPI.php');

            if (\is_file($commonGlpiFile)) {
                $this->path = $directory;
                return $this->path;
            }
        }

        throw new \LogicException('phpstan-glpi rules are not expected to be executed outside the GLPI context.');
    }
}
