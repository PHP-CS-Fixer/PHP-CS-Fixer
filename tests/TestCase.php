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
                    if (\E_USER_DEPRECATED === $code || \E_DEPRECATED === $code) {
                        $this->actualDeprecations[] = $message;
                    }

                    return true;
                }
            );
        }
    }

    /**
     * @TODO v4 remove the content and make ready for v5
     */
    protected function expectDeprecationOfDeprecatedRuleSets(): void
    {
        $this->expectDeprecation('Rule set "@PER" is deprecated. Use "@PER-CS" instead.');
        $this->expectDeprecation('Rule set "@PER:risky" is deprecated. Use "@PER-CS:risky" instead.');
        $this->expectDeprecation('Rule set "@PHP54Migration" is deprecated. Use "@PHP5.4Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP56Migration:risky" is deprecated. Use "@PHP5.6Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHP70Migration" is deprecated. Use "@PHP7.0Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP70Migration:risky" is deprecated. Use "@PHP7.0Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHP71Migration" is deprecated. Use "@PHP7.1Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP71Migration:risky" is deprecated. Use "@PHP7.1Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHP73Migration" is deprecated. Use "@PHP7.3Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP74Migration" is deprecated. Use "@PHP7.4Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP74Migration:risky" is deprecated. Use "@PHP7.4Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHP80Migration" is deprecated. Use "@PHP8.0Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP80Migration:risky" is deprecated. Use "@PHP8.0Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHP81Migration" is deprecated. Use "@PHP8.1Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP82Migration" is deprecated. Use "@PHP8.2Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP82Migration:risky" is deprecated. Use "@PHP8.2Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHP83Migration" is deprecated. Use "@PHP8.3Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP84Migration" is deprecated. Use "@PHP8.4Migration" instead.');
        $this->expectDeprecation('Rule set "@PHP85Migration" is deprecated. Use "@PHP8.5Migration" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit100Migration:risky" is deprecated. Use "@PHPUnit10.0Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit30Migration:risky" is deprecated. Use "@PHPUnit3.0Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit32Migration:risky" is deprecated. Use "@PHPUnit3.2Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit35Migration:risky" is deprecated. Use "@PHPUnit3.5Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit43Migration:risky" is deprecated. Use "@PHPUnit4.3Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit48Migration:risky" is deprecated. Use "@PHPUnit4.8Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit50Migration:risky" is deprecated. Use "@PHPUnit5.0Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit52Migration:risky" is deprecated. Use "@PHPUnit5.2Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit54Migration:risky" is deprecated. Use "@PHPUnit5.4Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit55Migration:risky" is deprecated. Use "@PHPUnit5.5Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit56Migration:risky" is deprecated. Use "@PHPUnit5.6Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit57Migration:risky" is deprecated. Use "@PHPUnit5.7Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit60Migration:risky" is deprecated. Use "@PHPUnit6.0Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit75Migration:risky" is deprecated. Use "@PHPUnit7.5Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit84Migration:risky" is deprecated. Use "@PHPUnit8.4Migration:risky" instead.');
        $this->expectDeprecation('Rule set "@PHPUnit91Migration:risky" is deprecated. Use "@PHPUnit9.1Migration:risky" instead.');
    }
}
