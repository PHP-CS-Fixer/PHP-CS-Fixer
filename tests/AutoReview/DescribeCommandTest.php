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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Utils;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 *
 * @group legacy
 * @group auto-review
 * @group covers-nothing
 */
final class DescribeCommandTest extends TestCase
{
    /**
     * @dataProvider provideDescribeCommandCases
     *
     * @param list<string> $successorsNames
     */
    public function testDescribeCommand(FixerFactory $factory, string $fixerName, ?array $successorsNames): void
    {
        if (null !== $successorsNames) {
            $message = "Rule \"{$fixerName}\" is deprecated. "
                .([] === $successorsNames
                    ? 'It will be removed in version 4.0.'
                    : sprintf('Use %s instead.', Utils::naturalLanguageJoin($successorsNames)));
            $this->expectDeprecation($message);
        }

        // @TODO 4.0 Remove this expectation
        $this->expectDeprecation('Rule set "@PER" is deprecated. Use "@PER-CS" instead.');
        $this->expectDeprecation('Rule set "@PER:risky" is deprecated. Use "@PER-CS:risky" instead.');

        $command = new DescribeCommand($factory);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => $fixerName,
        ]);

        self::assertSame(0, $commandTester->getStatusCode());
    }

    public static function provideDescribeCommandCases(): iterable
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        foreach ($factory->getFixers() as $fixer) {
            yield [
                $factory,
                $fixer->getName(),
                $fixer instanceof DeprecatedFixerInterface ? $fixer->getSuccessorsNames() : null,
            ];
        }
    }
}
