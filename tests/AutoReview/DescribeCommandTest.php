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
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\Internal\ConfigurableFixerTemplateFixer;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSets;
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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DescribeCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset the global state of RuleSets::$customRuleSetDefinitions that was modified
        // when using `.php-cs-fixer.dist.php`, which registers custom rules/sets.
        //
        // @TODO: ideally, we don't have the global state but inject the state instead
        \Closure::bind(
            static fn () => RuleSets::$customRuleSetDefinitions = [],
            null,
            RuleSets::class
        )();
    }

    /**
     * @dataProvider provideDescribeCommandCases
     *
     * @param list<string> $successorsNames
     */
    public function testDescribeCommand(string $fixerName, ?array $successorsNames, ?string $configFile = null): void
    {
        if (null !== $successorsNames) {
            $message = "Rule \"{$fixerName}\" is deprecated. "
                .([] === $successorsNames
                    ? 'It will be removed in version 4.0.'
                    : \sprintf('Use %s instead.', Utils::naturalLanguageJoin($successorsNames)));
            $this->expectDeprecation($message);
        }

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
            '--config' => $configFile ?? ConfigurationResolver::IGNORE_CONFIG_FILE,
        ]);

        self::assertSame(0, $commandTester->getStatusCode());
    }

    /**
     * @return iterable<int, array{string, null|list<string>}>
     */
    public static function provideDescribeCommandCases(): iterable
    {
        // internal rules
        yield [
            (new ConfigurableFixerTemplateFixer())->getName(),
            null,
            __DIR__.'/../Fixtures/.php-cs-fixer.one-time-proxy.php',
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
