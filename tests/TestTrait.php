<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests;

use PHPStan\File\FileHelper;
use PHPStanGlpi\Services\EarlierRulesAdoptionResolver;
use PHPStanGlpi\Services\GlpiPathResolver;
use PHPStanGlpi\Services\GlpiVersionResolver;
use PHPStanGlpi\Analyser\GlobalTypeResolver;

trait TestTrait
{
    protected function getGlobalTypeResolver(?string $glpiVersion = null, ?string $glpipath = null): GlobalTypeResolver
    {
        return new GlobalTypeResolver(
            self::getContainer()->getByType(FileHelper::class),
            $this->getGlpiPathResolver($glpipath),
            $this->getGlpiVersionResolver($glpiVersion),
        );
    }

    protected function getEarlierRulesAdoptionResolver(
        ?string $glpiVersion = null,
        bool $enabled = false
    ): EarlierRulesAdoptionResolver {
        return new EarlierRulesAdoptionResolver(
            $this->getGlpiVersionResolver($glpiVersion),
            $enabled
        );
    }

    protected function getGlpiPathResolver(?string $glpipath = null): GlpiPathResolver
    {
        return new GlpiPathResolver(
            self::getContainer()->getByType(FileHelper::class),
            $glpipath
        );
    }

    protected function getGlpiVersionResolver(?string $glpiVersion = null, ?string $glpipath = null): GlpiVersionResolver
    {
        return new GlpiVersionResolver(
            $this->getGlpiPathResolver(),
            $glpiVersion
        );
    }
}
