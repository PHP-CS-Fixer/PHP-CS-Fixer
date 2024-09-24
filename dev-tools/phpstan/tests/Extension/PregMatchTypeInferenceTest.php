<?php

namespace PhpCsFixer\PHPStan\Tests\Extension;

use PHPStan\Testing\TypeInferenceTestCase;

/**
 * @covers PhpCsFixer\PHPStan\Extension\PregMatchParameterOutExtension
 * @covers PhpCsFixer\PHPStan\Extension\PregMatchTypeSpecifyingExtension
 */
final class PregMatchTypeInferenceTest extends TypeInferenceTestCase
{

    /**
     * @return iterable<string[]>
     */
    public static function dataFile(): iterable
    {
        yield [__DIR__ . '/data/preg-match.php'];
        yield [__DIR__ . '/data/preg-match-all.php'];
    }

    /**
     * @dataProvider dataFile
     */
    public function testFile(string $file): void
    {
        $asserts = $this->gatherAssertTypes($file);
        self::assertNotCount(0, $asserts, sprintf('File %s has no asserts.', $file));
        $failures = [];

        foreach ($asserts as $args) {
            $assertType = array_shift($args);
            $file = array_shift($args);

            if ($assertType === 'type') {
                $expected = array_shift($args);
                $actual = array_shift($args);
                $line = array_shift($args);

                if ($expected !== $actual) {
                    $failures[] = sprintf("Line %d:\nExpected: %s\nActual:   %s\n", $line, $expected, $actual);
                }
            } elseif ($assertType === 'variableCertainty') {
                $expectedCertainty = array_shift($args);
                $actualCertainty = array_shift($args);
                $variableName = array_shift($args);
                $line = array_shift($args);

                if ($expectedCertainty->equals($actualCertainty) !== true) {
                    $failures[] = sprintf("Certainty of variable \$%s on line %d:\nExpected: %s\nActual:   %s\n", $variableName, $line, $expectedCertainty->describe(), $actualCertainty->describe());
                }
            }
        }

        if ($failures === []) {
            return;
        }

        self::fail(sprintf("Failed assertions in %s:\n\n%s", $file, implode("\n", $failures)));
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../phpstan.dist.neon',
        ];
    }

}
