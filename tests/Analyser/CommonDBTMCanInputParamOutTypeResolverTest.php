<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Analyser;

use PHPStan\Testing\TypeInferenceTestCase;

class CommonDBTMCanInputParamOutTypeResolverTest extends TypeInferenceTestCase
{
    /**
     * @phpstan-ignore missingType.iterableValue
     */
    public static function dataFileAsserts(): iterable
    {
        yield from self::gatherAssertTypes(__DIR__ . '/../data/CommonDBTMCanInputParamOutTypeResolver/param-out.php');
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
            __DIR__ . '/../data/CommonDBTMCanInputParamOutTypeResolver/extension.neon',
        ];
    }
}
