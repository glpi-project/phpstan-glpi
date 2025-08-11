<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Analyser;

use PHPStan\Testing\TypeInferenceTestCase;

class GlobalTypeResolverForGlpi11Test extends TypeInferenceTestCase
{
    /**
     * @phpstan-ignore missingType.iterableValue
     */
    public static function dataFileAsserts(): iterable
    {
        yield from self::gatherAssertTypes(__DIR__ . '/../data/GlobalTypeResolver/install/migrations/update_tpl.php');
        yield from self::gatherAssertTypes(__DIR__ . '/../data/GlobalTypeResolver/function-parameter.php');
        yield from self::gatherAssertTypes(__DIR__ . '/../data/GlobalTypeResolver/global-statement-with-phpdoc.php');
        yield from self::gatherAssertTypes(__DIR__ . '/../data/GlobalTypeResolver/global-statement-in-glpi-11.0.php');
    }

    /**
     * @param mixed ...$args
     * @dataProvider dataFileAsserts
     */
    public function testFileAsserts(
        string $assertType,
        string $file,
        ...$args
    ): void {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../extension.neon',
            __DIR__ . '/../data/glpi-11.0.x.neon',
            __DIR__ . '/../data/GlobalTypeResolver/glpi-path.neon',
        ];
    }
}
