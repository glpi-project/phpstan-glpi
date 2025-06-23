<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\IgnoredGlpiVersion;

use FilesystemIterator;
use ReflectionClass;

trait TestIgnoredRuleTrait
{
    public function test(): void
    {
        $reflected_class = new ReflectionClass($this->getRule());

        $file_iterator = new FilesystemIterator(__DIR__ . '/../data/' . $reflected_class->getShortName());

        /** @var \SplFileInfo $file */
        foreach ($file_iterator as $file) {
            // No error should be detected
            $this->analyse([$file->getRealPath()], []);
        }
    }
}
