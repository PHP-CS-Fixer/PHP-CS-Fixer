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

use PhpCsFixer\Future;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Future
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FutureTest extends TestCase
{
    /**
     * @var null|false|string
     */
    private $originalValueOfFutureMode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalValueOfFutureMode = getenv('PHP_CS_FIXER_FUTURE_MODE');
    }

    protected function tearDown(): void
    {
        putenv("PHP_CS_FIXER_FUTURE_MODE={$this->originalValueOfFutureMode}");

        parent::tearDown();
    }

    /**
     * @group legacy
     */
    public function testTriggerDeprecationWhenFutureModeIsOff(): void
    {
        putenv('PHP_CS_FIXER_FUTURE_MODE=0');

        $message = __METHOD__.'::The message';
        $this->expectDeprecation($message);

        Future::triggerDeprecation(new \DomainException($message));

        $triggered = Future::getTriggeredDeprecations();
        self::assertContains($message, $triggered);
    }

    public function testTriggerDeprecationWhenFutureModeIsOn(): void
    {
        putenv('PHP_CS_FIXER_FUTURE_MODE=1');

        $message = __METHOD__.'::The message';
        $exception = new \DomainException($message);
        $futureModeException = null;

        try {
            Future::triggerDeprecation($exception);
        } catch (\Exception $futureModeException) {
        }

        self::assertInstanceOf(\RuntimeException::class, $futureModeException);
        self::assertSame($exception, $futureModeException->getPrevious());

        $triggered = Future::getTriggeredDeprecations();
        self::assertNotContains($message, $triggered);
    }

    public function testGetV4OrV3ForOldBehaviour(): void
    {
        putenv('PHP_CS_FIXER_FUTURE_MODE=0');

        self::assertSame(
            'old',
            Future::getV4OrV3('new', 'old')
        );
    }

    public function testGetV4OrV3ForNewBehaviour(): void
    {
        putenv('PHP_CS_FIXER_FUTURE_MODE=1');

        self::assertSame(
            'new',
            Future::getV4OrV3('new', 'old')
        );
    }
}
