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
 */
abstract class TestCase extends BaseTestCase
{
    /** @var null|callable */
    private $previouslyDefinedErrorHandler;

    /** @var list<string> */
    private array $expectedDeprecations = [];

    /** @var list<string> */
    private array $actualDeprecations = [];

    protected function tearDown(): void
    {
        if (null !== $this->previouslyDefinedErrorHandler) {
            $this->actualDeprecations = array_unique($this->actualDeprecations);
            sort($this->actualDeprecations);
            $this->expectedDeprecations = array_unique($this->expectedDeprecations);
            sort($this->expectedDeprecations);
            self::assertSame($this->expectedDeprecations, $this->actualDeprecations);

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
                    if (E_USER_DEPRECATED === $code || E_DEPRECATED === $code) {
                        $this->actualDeprecations[] = $message;
                    }

                    return true;
                }
            );
        }
    }
}
