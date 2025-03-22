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
use PhpCsFixer\Fixer\Internal\ConfigurableFixerTemplateFixer;
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
    public function testDescribeCommand(string $fixerName, ?array $successorsNames): void
    {
        if (null !== $successorsNames) {
            $message = "Rule \"{$fixerName}\" is deprecated. "
                .([] === $successorsNames
                    ? 'It will be removed in version 4.0.'
                    : \sprintf('Use %s instead.', Utils::naturalLanguageJoin($successorsNames)));
            $this->expectDeprecation($message);
        }

        // @TODO 4.0 Remove this expectations
        $this->expectDeprecation('Rule set "@PER" is deprecated. Use "@PER-CS" instead.');
        $this->expectDeprecation('Rule set "@PER:risky" is deprecated. Use "@PER-CS:risky" instead.');
        if ('ordered_imports' === $fixerName) {
            $this->expectDeprecation('[ordered_imports] Option "sort_algorithm:length" is deprecated and will be removed in version 4.0.');
        }
        if ('nullable_type_declaration_for_default_null_value' === $fixerName) {
            $this->expectDeprecation('Option "use_nullable_type_declaration" for rule "nullable_type_declaration_for_default_null_value" is deprecated and will be removed in version 4.0. Behaviour will follow default one.');
        }

        $command = new DescribeCommand();

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
        yield [
            (new ConfigurableFixerTemplateFixer())->getName(),
            null,
        ];

        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        foreach ($factory->getFixers() as $fixer) {
            yield [
                $fixer->getName(),
                $fixer instanceof DeprecatedFixerInterface ? $fixer->getSuccessorsNames() : null,
            ];
        }
    }
}
