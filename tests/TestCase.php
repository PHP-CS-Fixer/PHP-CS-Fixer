<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
abstract class TestCase extends BaseTestCase
{
    /** @var null|callable */
    private $previouslyDefinedErrorHandler;

    /** @var array<int, string> */
    private array $expectedDeprecations = [];

    /** @var array<int, string> */
    private array $actualDeprecations = [];

    protected function assertPostConditions(): void
    {
        if (null !== $this->previouslyDefinedErrorHandler) {
            $this->actualDeprecations = array_unique($this->actualDeprecations);
            sort($this->actualDeprecations);
            $this->expectedDeprecations = array_unique($this->expectedDeprecations);
            sort($this->expectedDeprecations);

            self::assertSame($this->expectedDeprecations, $this->actualDeprecations);
        }

        parent::assertPostConditions();
    }

    protected function tearDown(): void
    {
        if (null !== $this->previouslyDefinedErrorHandler) {
            restore_error_handler();
        }

        parent::tearDown();
    }

    final public function testNotDefiningConstructor(): void
    {
        $reflection = new \ReflectionObject($this);

        self::assertNotSame(
            $reflection->getConstructor()->getDeclaringClass()->getName(),
            $reflection->getName(),
        );
    }

    /**
     * Mark test to expect given deprecation. Order or repetition count of expected vs actual deprecation usage can vary, but result sets must be identical.
     *
     * @TODO change access to protected and pass the parameter when PHPUnit 9 support is dropped
     */
    public function expectDeprecation(/* string $message */): void
    {
        $this->expectedDeprecations[] = func_get_arg(0);

        if (null === $this->previouslyDefinedErrorHandler) {
            $this->previouslyDefinedErrorHandler = set_error_handler(
                function (
                    int $code,
                    string $message
                ) {
                    if (\E_USER_DEPRECATED === $code || \E_DEPRECATED === $code) {
                        $this->actualDeprecations[] = $message;
                    }

                    return true;
                }
            );
        }
    }

    /** @TODO find better place for me */
    final protected static function createSerializedStringOfClassName(string $className): string
    {
        return \sprintf('O:%d:"%s":0:{}', \strlen($className), $className);
    }
}
