<?php

declare(strict_types=1);

namespace PHPStanGlpi\Services;

use FilesystemIterator;
use SplFileInfo;

class GlpiVersionResolver
{
    private GlpiPathResolver $glpiPathResolver;

    private ?string $version;

    public function __construct(GlpiPathResolver $glpiPathResolver, ?string $version)
    {
        $this->glpiPathResolver = $glpiPathResolver;
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

        $versionDir = \implode(DIRECTORY_SEPARATOR, [$this->glpiPathResolver->getGlpiPath(), 'version']);

        if (\is_dir($versionDir)) {
            $fileIterator = new FilesystemIterator($versionDir);
            $files = \iterator_to_array($fileIterator);
            $versionFile = \end($files);

            if ($versionFile instanceof SplFileInfo) {
                $this->version = $versionFile->getBaseName();
                return $this->version;
            }
        }

        throw new \LogicException('phpstan-glpi rules are not expected to be executed outside the GLPI context.');
    }
}
