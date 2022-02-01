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

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\FixerConfiguration\AliasedFixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\DescribeCommand
 */
final class DescribeCommandTest extends TestCase
{
    public function testExecuteOutput(): void
    {
        $expected =
"Description of Foo/bar rule.
Fixes stuff. DEPRECATED: use `Foo/baz` instead.
Replaces bad stuff with good stuff.

Fixer applying this rule is risky.
Can break stuff.

Fixer is configurable using following options:
* functions (a subset of ['foo', 'test']): list of `function` names to fix; defaults to ['foo', 'test']; DEPRECATED alias: funcs
* deprecated_option (bool): a deprecated option; defaults to false. DEPRECATED: use option `functions` instead.

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

';
        static::assertSame($expected, $this->execute('Foo/bar', false)->getDisplay(true));
    }

    public function testExecuteOutputWithDecoration(): void
    {
        $expected =
"\033[32mDescription of\033[39m Foo/bar \033[32mrule\033[39m.
Fixes stuff. \033[37;41mDEPRECATED\033[39;49m: use \033[32m`Foo/baz`\033[39m instead.
Replaces bad stuff with good stuff.

\033[37;41mFixer applying this rule is risky.\033[39;49m
Can break stuff.

Fixer is configurable using following options:
* \033[32mfunctions\033[39m (a subset of \e[33m['foo', 'test']\e[39m): list of \033[32m`function`\033[39m names to fix; defaults to \033[33m['foo', 'test']\033[39m; \e[37;41mDEPRECATED\e[39;49m alias: \033[33mfuncs\033[39m
* \033[32mdeprecated_option\033[39m (\033[33mbool\033[39m): a deprecated option; defaults to \e[33mfalse\e[39m. \033[37;41mDEPRECATED\033[39;49m: use option \e[32m`functions`\e[39m instead.

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

";
        $actual = $this->execute('Foo/bar', true)->getDisplay(true);

        static::assertSame($expected, $actual);
    }

    public function testExecuteStatusCode(): void
    {
        static::assertSame(0, $this->execute('Foo/bar', false)->getStatusCode());
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
        ]);
    }

    public function testExecuteWithoutName(): void
    {
        $application = new Application();
        $application->add(new DescribeCommand(new FixerFactory()));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Not enough arguments( \(missing: "name"\))?\.$/');
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
    }

    public function testGetAlternativeSuggestion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#^Rule "Foo2/bar" not found\. Did you mean "Foo/bar"\?$#');
        $this->execute('Foo2/bar', false);
    }

    public function testFixerClassNameIsExposedWhenVerbose(): void
    {
        $fixerName = uniqid('Foo/bar_');

        $fixer = $this->prophesize(\PhpCsFixer\Fixer\FixerInterface::class);
        $fixer->getName()->willReturn($fixerName);
        $fixer->getPriority()->willReturn(0);
        $fixer->isRisky()->willReturn(true);
        $fixer->getDefinition()->willReturn(new FixerDefinition('Fixes stuff.', []));
        $mock = $fixer->reveal();

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerFixer($mock, true);

        $application = new Application();
        $application->add(new DescribeCommand($fixerFactory));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'name' => $fixerName,
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ]
        );

        static::assertStringContainsString(\get_class($mock), $commandTester->getDisplay(true));
    }

    private function execute(string $name, bool $decorated): CommandTester
    {
        $fixer = $this->prophesize();
        $fixer->willImplement(\PhpCsFixer\Fixer\ConfigurableFixerInterface::class);
        $fixer->willImplement(\PhpCsFixer\Fixer\DeprecatedFixerInterface::class);

        $fixer->getName()->willReturn('Foo/bar');
        $fixer->getPriority()->willReturn(0);
        $fixer->isRisky()->willReturn(true);
        $fixer->getSuccessorsNames()->willReturn(['Foo/baz']);

        $functionNames = ['foo', 'test'];

        $fixer->getConfigurationDefinition()->willReturn(new FixerConfigurationResolver([
            (new AliasedFixerOptionBuilder(new FixerOptionBuilder('functions', 'List of `function` names to fix.'), 'funcs'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset($functionNames)])
                ->setDefault($functionNames)
                ->getOption(),
            (new FixerOptionBuilder('deprecated_option', 'A deprecated option.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->setDeprecationMessage('Use option `functions` instead.')
                ->getOption(),
        ]));

        $fixer->getDefinition()->willReturn(new FixerDefinition(
            'Fixes stuff.',
            [
                new CodeSample(
                    "<?php echo 'bad stuff and bad thing';\n"
                ),
                new CodeSample(
                    "<?php echo 'bad stuff and bad thing';\n",
                    ['functions' => ['foo', 'bar']]
                ),
            ],
            'Replaces bad stuff with good stuff.',
            'Can break stuff.'
        ));

        $things = false;
        $fixer->configure([])->will(function () use (&$things): void {
            $things = false;
        });
        $fixer->configure(['functions' => ['foo', 'bar']])->will(function () use (&$things): void {
            $things = true;
        });

        $fixer->fix(
            Argument::type(\SplFileInfo::class),
            Argument::type(\PhpCsFixer\Tokenizer\Tokens::class)
        )->will(function (array $arguments) use (&$things): void {
            $arguments[1][3] = new Token([
                $arguments[1][3]->getId(),
                ($things ? '\'good stuff and good thing\'' : '\'good stuff and bad thing\''),
            ]);
        });

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerFixer($fixer->reveal(), true);

        $application = new Application();
        $application->add(new DescribeCommand($fixerFactory));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'name' => $name,
            ],
            [
                'decorated' => $decorated,
            ]
        );

        return $commandTester;
    }
}
