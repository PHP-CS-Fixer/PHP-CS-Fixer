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

namespace PhpCsFixer\Tests\Console\Command;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\FixerConfiguration\AliasedFixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\CodeSampleInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\Fixtures\DescribeCommand\DescribeFixtureFixer;
use PhpCsFixer\Tests\Fixtures\ExternalRuleSet\ExampleRuleSet;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @group legacy
 *
 * @covers \PhpCsFixer\Console\Command\DescribeCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DescribeCommandTest extends TestCase
{
    /**
     * @dataProvider provideExecuteOutputCases
     */
    public function testExecuteOutput(string $expected, bool $expectedIsRegEx, bool $decorated, FixerInterface $fixer): void
    {
        if ($fixer instanceof DeprecatedFixerInterface) {
            $this->expectDeprecation(\sprintf('Rule "%s" is DEPRECATED and will be removed in the next major version 4.0. You should use "%s" instead.', $fixer->getName(), implode('", "', $fixer->getSuccessorsNames())));
        }

        $actual = $this->execute($fixer->getName(), $decorated, $fixer)->getDisplay(true);

        if (true === $expectedIsRegEx) {
            self::assertMatchesRegularExpression($expected, $actual);
        } else {
            self::assertSame($expected, $actual);
        }
    }

    /**
     * @return iterable<string, array{string, bool, bool, FixerInterface}>
     */
    public static function provideExecuteOutputCases(): iterable
    {
        yield 'rule is configurable, risky and deprecated' => [
            "Description of the `Foo/bar` rule.

Fixes stuff.
Replaces bad stuff with good stuff.

This rule is DEPRECATED and will be removed in the next major version 4.0
You should use `Foo/baz` instead.

This rule is RISKY
Can break stuff.

Fixer is configurable using following options:
* deprecated_option (bool): a deprecated option; defaults to false. DEPRECATED: use option `functions` instead.
* functions (a subset of ['foo', 'test']): list of `function` names to fix; defaults to ['foo', 'test']; DEPRECATED alias: funcs

Fixing examples:
 * Example #1. Fixing with the default configuration.
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ -1,1 +1,1 @@
   -<?php echo 'bad stuff and bad thing';
   +<?php echo 'good stuff and bad thing';
   "."
   ----------- end diff -----------

 * Example #2. Fixing with configuration: ['functions' => ['foo', 'bar']].
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ -1,1 +1,1 @@
   -<?php echo 'bad stuff and bad thing';
   +<?php echo 'good stuff and good thing';
   ".'
   ----------- end diff -----------

',
            false,
            false,
            self::createConfigurableDeprecatedFixerDouble(),
        ];

        yield 'rule is configurable, risky and deprecated [with decoration]' => [
            "\033[34mDescription of the \033[39m\033[32m`Foo/bar`\033[39m\033[34m rule.\033[39m

Fixes stuff.
Replaces bad stuff with good stuff.

\033[37;41mThis rule is DEPRECATED and will be removed in the next major version 4.0\033[39;49m
You should use \033[32m`Foo/baz`\033[39m instead.

\033[37;41mThis rule is RISKY\033[39;49m
Can break stuff.

Fixer is configurable using following options:
* \033[32mdeprecated_option\033[39m (\033[33mbool\033[39m): a deprecated option; defaults to \e[33mfalse\e[39m. \033[37;41mDEPRECATED\033[39;49m: use option \e[32m`functions`\e[39m instead.
* \033[32mfunctions\033[39m (a subset of \e[33m['foo', 'test']\e[39m): list of \033[32m`function`\033[39m names to fix; defaults to \033[33m['foo', 'test']\033[39m; \e[37;41mDEPRECATED\e[39;49m alias: \033[33mfuncs\033[39m

Fixing examples:
 * Example #1. Fixing with the \033[33mdefault\033[39m configuration.
\033[33m   ---------- begin diff ----------\033[39m
   \033[31m--- Original\033[39m
   \033[32m+++ New\033[39m
   \033[36m@@ -1,1 +1,1 @@\033[39m
   \033[31m-<?php echo 'bad stuff and bad thing';\033[39m
   \033[32m+<?php echo 'good stuff and bad thing';\033[39m
   "."
\033[33m   ----------- end diff -----------\033[39m

 * Example #2. Fixing with configuration: \033[33m['functions' => ['foo', 'bar']]\033[39m.
\033[33m   ---------- begin diff ----------\033[39m
   \033[31m--- Original\033[39m
   \033[32m+++ New\033[39m
   \033[36m@@ -1,1 +1,1 @@\033[39m
   \033[31m-<?php echo 'bad stuff and bad thing';\033[39m
   \033[32m+<?php echo 'good stuff and good thing';\033[39m
   "."
\033[33m   ----------- end diff -----------\033[39m

",
            false,
            true,
            self::createConfigurableDeprecatedFixerDouble(),
        ];

        yield 'rule without code samples' => [
            'Description of the `Foo/samples` rule.

Summary of the rule.
Description of the rule.

Fixing examples are not available for this rule.

',
            false,
            false,
            self::createFixerWithSamplesDouble([]),
        ];

        yield 'rule with code samples' => [
            "Description of the `Foo/samples` rule.

Summary of the rule.
Description of the rule.

Fixing examples:
 * Example #1.
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ -1,1 +1,1 @@
   -<?php echo 'BEFORE';
   +<?php echo 'AFTER';
   "."
   ----------- end diff -----------

 * Example #2.
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ -1,1 +1,1 @@
   -<?php echo 'BEFORE'.'-B';
   +<?php echo 'AFTER'.'-B';
   ".'
   ----------- end diff -----------

',
            false,
            false,
            self::createFixerWithSamplesDouble([
                new CodeSample(
                    "<?php echo 'BEFORE';".\PHP_EOL,
                ),
                new CodeSample(
                    "<?php echo 'BEFORE'.'-B';".\PHP_EOL,
                ),
            ]),
        ];

        yield 'rule with code samples (one with matching PHP version, one NOT)' => [
            "Description of the `Foo/samples` rule.

Summary of the rule.
Description of the rule.

Fixing examples:
 * Example #1.
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ -1,1 +1,1 @@
   -<?php echo 'BEFORE';
   +<?php echo 'AFTER';
   ".'
   ----------- end diff -----------

',
            false,
            false,
            self::createFixerWithSamplesDouble([
                new CodeSample(
                    "<?php echo 'BEFORE';".\PHP_EOL,
                ),
                new VersionSpecificCodeSample(
                    "<?php echo 'BEFORE'.'-B';".\PHP_EOL,
                    new VersionSpecification(20_00_00),
                ),
            ]),
        ];

        yield 'rule with code samples (all with NOT matching PHP version)' => [
            'Description of the `Foo/samples` rule.

Summary of the rule.
Description of the rule.

Fixing examples cannot be demonstrated on the current PHP version.

',
            false,
            false,
            self::createFixerWithSamplesDouble([
                new VersionSpecificCodeSample(
                    "<?php echo 'BEFORE';".\PHP_EOL,
                    new VersionSpecification(20_00_00),
                ),
                new VersionSpecificCodeSample(
                    "<?php echo 'BEFORE'.'-B';".\PHP_EOL,
                    new VersionSpecification(20_00_00),
                ),
            ]),
        ];

        yield 'rule that is part of ruleset' => [
            '/^Description of the `binary_operator_spaces` rule.
.*
   ----------- end diff -----------

'.preg_quote("The fixer is part of the following rule sets:
* @PER *(deprecated)* with config: ['default' => 'at_least_single_space']
* @PER-CS with config: ['default' => 'at_least_single_space']
* @PER-CS1.0 *(deprecated)* with config: ['default' => 'at_least_single_space']
* @PER-CS1x0 with config: ['default' => 'at_least_single_space']
* @PER-CS2.0 *(deprecated)* with config: ['default' => 'at_least_single_space']
* @PER-CS2x0 with config: ['default' => 'at_least_single_space']
* @PER-CS3.0 *(deprecated)* with config: ['default' => 'at_least_single_space']
* @PER-CS3x0 with config: ['default' => 'at_least_single_space']
* @PSR12 with config: ['default' => 'at_least_single_space']
* @PhpCsFixer with default config
* @Symfony with default config").'
$/s',
            true,
            false,
            new BinaryOperatorSpacesFixer(),
        ];
    }

    public function testExecuteStatusCode(): void
    {
        $this->expectDeprecation('Rule "Foo/bar" is DEPRECATED and will be removed in the next major version 4.0. You should use "Foo/baz" instead.');

        self::assertSame(0, $this->execute('Foo/bar', false)->getStatusCode());
    }

    public function testExecuteWithUnknownRuleName(): void
    {
        $application = new Application();
        $application->add(new DescribeCommand(new FixerFactory()));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^Rule "Foo/bar" not found\.$#');
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'Foo/bar',

            '--config' => __DIR__.'/../../Fixtures/.php-cs-fixer.vanilla.php',
        ]);
    }

    public function testExecuteWithUnknownSetName(): void
    {
        $application = new Application();
        $application->add(new DescribeCommand(new FixerFactory()));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^Set "@NoSuchSet" not found\.$#');
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => '@NoSuchSet',
            '--config' => __DIR__.'/../../Fixtures/.php-cs-fixer.vanilla.php',
        ]);
    }

    public function testExecuteWithoutName(): void
    {
        $application = new Application();
        $application->add(new DescribeCommand(new FixerFactory()));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "name") when not running interactively.');
        $commandTester->execute([
            'command' => $command->getName(),
            '--config' => __DIR__.'/../../Fixtures/.php-cs-fixer.vanilla.php',
        ], ['interactive' => false]);
    }

    public function testGetAlternativeSuggestion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^Rule "Foo2/bar" not found\. Did you mean "Foo/bar"\?$#');
        $this->execute('Foo2/bar', false);
    }

    public function testFixerClassNameIsExposedWhenVerbose(): void
    {
        $fixer = new class implements FixerInterface {
            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRisky(): bool
            {
                return true;
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \LogicException('Not implemented.');
            }

            public function getDefinition(): FixerDefinition
            {
                return new FixerDefinition('Fixes stuff.', []);
            }

            public function getName(): string
            {
                return 'Foo/bar_baz';
            }

            public function getPriority(): int
            {
                return 0;
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerFixer($fixer, true);

        $application = new Application();
        $application->add(new DescribeCommand($fixerFactory));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'name' => 'Foo/bar_baz',
                '--config' => __DIR__.'/../../Fixtures/.php-cs-fixer.vanilla.php',
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        self::assertStringContainsString(str_replace("\0", '\\', \get_class($fixer)), $commandTester->getDisplay(true));
    }

    public function testCommandDescribesCustomFixer(): void
    {
        $application = new Application();
        $application->add(new DescribeCommand());

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => (new DescribeFixtureFixer())->getName(),
            '--config' => __DIR__.'/../../Fixtures/DescribeCommand/.php-cs-fixer.custom-rule.php',
        ]);

        $expected = "Description of the `Vendor/describe_fixture` rule.

Fixture for describe command.

Fixing examples:
 * Example #1.
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -echo 'describe fixture';
   +echo 'fixture for describe';
   ".'
   ----------- end diff -----------

';
        self::assertSame($expected, $commandTester->getDisplay(true));
        self::assertSame(0, $commandTester->getStatusCode());
    }

    public function testCommandDescribesCustomSet(): void
    {
        $application = new Application();
        $application->add(new DescribeCommand());

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => (new ExampleRuleSet())->getName(),
            '--config' => __DIR__.'/../../Fixtures/DescribeCommand/.php-cs-fixer.custom-set.php',
        ]);

        $expected = "You may the '--expand' option to see nested sets expanded into nested rules.
Description of the `@Vendor/RuleSet` set.

Purpose of example rule set description.

 * align_multiline_comment configurable
   | Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.
   | Configuration: false

";
        self::assertSame($expected, $commandTester->getDisplay(true));
        self::assertSame(0, $commandTester->getStatusCode());
    }

    /**
     * @param list<CodeSampleInterface> $samples
     */
    private static function createFixerWithSamplesDouble(array $samples): FixerInterface
    {
        return new class($samples) extends AbstractFixer {
            /**
             * @var list<CodeSampleInterface>
             */
            private array $samples;

            /**
             * @param list<CodeSampleInterface> $samples
             */
            public function __construct(
                array $samples
            ) {
                parent::__construct();
                $this->samples = $samples;
            }

            public function getName(): string
            {
                return 'Foo/samples';
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                return new FixerDefinition(
                    'Summary of the rule.',
                    $this->samples,
                    'Description of the rule.',
                    null,
                );
            }

            public function isCandidate(Tokens $tokens): bool
            {
                return true;
            }

            public function applyFix(\SplFileInfo $file, Tokens $tokens): void
            {
                $tokens[3] = new Token([
                    $tokens[3]->getId(),
                    "'AFTER'",
                ]);
            }
        };
    }

    private static function createConfigurableDeprecatedFixerDouble(): FixerInterface
    {
        return new class implements ConfigurableFixerInterface, DeprecatedFixerInterface {
            /** @var array<string, mixed> */
            private array $configuration;

            public function configure(array $configuration): void
            {
                $this->configuration = $configuration;
            }

            public function getConfigurationDefinition(): FixerConfigurationResolver
            {
                $functionNames = ['foo', 'test'];

                return new FixerConfigurationResolver([
                    (new AliasedFixerOptionBuilder(new FixerOptionBuilder('functions', 'List of `function` names to fix.'), 'funcs'))
                        ->setAllowedTypes(['string[]'])
                        ->setAllowedValues([new AllowedValueSubset($functionNames)])
                        ->setDefault($functionNames)
                        ->getOption(),
                    (new FixerOptionBuilder('deprecated_option', 'A deprecated option.'))
                        ->setAllowedTypes(['bool'])
                        ->setDefault(false)
                        ->setDeprecationMessage('Use option `functions` instead.')
                        ->getOption(),
                ]);
            }

            public function getSuccessorsNames(): array
            {
                return ['Foo/baz'];
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRisky(): bool
            {
                return true;
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                $tokens[3] = new Token([
                    $tokens[3]->getId(),
                    [] !== $this->configuration ? '\'good stuff and good thing\'' : '\'good stuff and bad thing\'',
                ]);
            }

            public function getDefinition(): FixerDefinition
            {
                return new FixerDefinition(
                    'Fixes stuff.',
                    [
                        new CodeSample(
                            "<?php echo 'bad stuff and bad thing';\n",
                        ),
                        new CodeSample(
                            "<?php echo 'bad stuff and bad thing';\n",
                            ['functions' => ['foo', 'bar']],
                        ),
                    ],
                    'Replaces bad stuff with good stuff.',
                    'Can break stuff.',
                );
            }

            public function getName(): string
            {
                return 'Foo/bar';
            }

            public function getPriority(): int
            {
                return 0;
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }

    private function execute(string $name, bool $decorated, ?FixerInterface $fixer = null): CommandTester
    {
        $fixer ??= self::createConfigurableDeprecatedFixerDouble();

        $fixerClassName = \get_class($fixer);
        $isBuiltIn = str_starts_with($fixerClassName, 'PhpCsFixer') && !str_contains($fixerClassName, '@anon');

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerFixer($fixer, !$isBuiltIn);

        $application = new Application();
        $application->add(new DescribeCommand($fixerFactory));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'name' => $name,
                '--config' => __DIR__.'/../../Fixtures/.php-cs-fixer.vanilla.php',
            ],
            [
                'decorated' => $decorated,
            ],
        );

        return $commandTester;
    }
}
