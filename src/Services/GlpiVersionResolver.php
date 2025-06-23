<?php

declare(strict_types=1);

namespace PHPStanGlpi\Services;

use FilesystemIterator;
use SplFileInfo;

class GlpiVersionResolver
{
    private ?string $version;

    public function __construct(?string $version)
    {
        $this->version = $version;
    }

    /**
     * Get the GLPI version.
     *
     * @throws \LogicException
     */
    public function getGlpiVersion(): string
    {
        if ($this->version !== null) {
            return $this->version;
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
            $version_dir = $directory . DIRECTORY_SEPARATOR . 'version';

            if (\is_dir($version_dir)) {
                $file_iterator = new FilesystemIterator($version_dir);
                $files = \iterator_to_array($file_iterator);
                $version_file = \end($files);

                if ($version_file instanceof SplFileInfo) {
                    $this->version = $version_file->getBaseName();
                    return $this->version;
                }
            }
        }

        throw new \LogicException('phpstan-glpi rules are not expected to be executed outside the GLPI context.');
    }
}
