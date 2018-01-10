<?php

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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tokenizer\Token;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\DescribeCommand
 */
final class DescribeCommandTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        parent::setUp();

        $this->application = new Application();
    }

    public function testExecuteOutput()
    {
        $expected =
"Description of Foo/bar rule.
Fixes stuff. DEPRECATED: use `Foo/baz` instead.
Replaces bad stuff with good stuff.

Fixer applying this rule is risky.
Can break stuff.

Fixer is configurable using following option:
* functions (array): list of `function` names to fix; defaults to ['foo', 'test']

Fixing examples:
 * Example #1. Fixing with the default configuration.
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php echo 'bad stuff and bad thing';
   +<?php echo 'good stuff and bad thing';
   "."
   ----------- end diff -----------

 * Example #2. Fixing with configuration: ['functions' => ['foo', 'bar']].
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ -1 +1 @@
   -<?php echo 'bad stuff and bad thing';
   +<?php echo 'good stuff and good thing';
   ".'
   ----------- end diff -----------

';
        $this->assertSame($expected, $this->execute('Foo/bar', false)->getDisplay(true));
    }

    public function testExecuteOutputWithDecoration()
    {
        $expected =
"\033[32mDescription of\033[39m Foo/bar \033[32mrule\033[39m.
Fixes stuff. \033[37;41mDEPRECATED\033[39;49m: use \033[32m`Foo/baz`\033[39m instead.
Replaces bad stuff with good stuff.

\033[37;41mFixer applying this rule is risky.\033[39;49m
Can break stuff.

Fixer is configurable using following option:
* \033[32mfunctions\033[39m (\033[33marray\033[39m): list of \033[32m`function`\033[39m names to fix; defaults to \033[33m['foo', 'test']\033[39m

Fixing examples:
 * Example #1. Fixing with the \033[33mdefault\033[39m configuration.
\033[33m   ---------- begin diff ----------\033[39m
   \033[31m--- Original\033[39m
   \033[32m+++ New\033[39m
   \033[36m@@ -1 +1 @@\033[39m
   \033[31m-<?php echo 'bad stuff and bad thing';\033[39m
   \033[32m+<?php echo 'good stuff and bad thing';\033[39m
   "."
\033[33m   ----------- end diff -----------\033[39m

 * Example #2. Fixing with configuration: \033[33m['functions' => ['foo', 'bar']]\033[39m.
\033[33m   ---------- begin diff ----------\033[39m
   \033[31m--- Original\033[39m
   \033[32m+++ New\033[39m
   \033[36m@@ -1 +1 @@\033[39m
   \033[31m-<?php echo 'bad stuff and bad thing';\033[39m
   \033[32m+<?php echo 'good stuff and good thing';\033[39m
   "."
\033[33m   ----------- end diff -----------\033[39m

";
        $actual = $this->execute('Foo/bar', true)->getDisplay(true);

        $this->assertSame($expected, $actual);
    }

    public function testExecuteStatusCode()
    {
        $this->assertSame(0, $this->execute('Foo/bar', false)->getStatusCode());
    }

    public function testExecuteWithUnknownRuleName()
    {
        $this->application->add(new DescribeCommand(new FixerFactory()));

        $command = $this->application->find('describe');

        $commandTester = new CommandTester($command);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('#^Rule "Foo/bar" not found\.$#');
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'Foo/bar',
        ]);
    }

    public function testExecuteWithUnknownSetName()
    {
        $this->application->add(new DescribeCommand(new FixerFactory()));

        $command = $this->application->find('describe');

        $commandTester = new CommandTester($command);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('#^Set "@NoSuchSet" not found\.$#');
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => '@NoSuchSet',
        ]);
    }

    public function testExecuteWithoutName()
    {
        $this->application->add(new DescribeCommand(new FixerFactory()));

        $command = $this->application->find('describe');

        $commandTester = new CommandTester($command);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/^Not enough arguments( \(missing: "name"\))?\.$/');
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
    }

    public function testGetAlternativeSuggestion()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('#^Rule "Foo2/bar" not found\. Did you mean "Foo/bar"\?$#');
        $this->execute('Foo2/bar', false);
    }

    /**
     * @param string $name
     * @param bool   $decorated
     *
     * @return CommandTester
     */
    private function execute($name, $decorated)
    {
        $fixer = $this->prophesize();
        $fixer->willImplement(\PhpCsFixer\Fixer\DefinedFixerInterface::class);
        $fixer->willImplement(\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface::class);
        $fixer->willImplement(\PhpCsFixer\Fixer\DeprecatedFixerInterface::class);

        $fixer->getName()->willReturn('Foo/bar');
        $fixer->getPriority()->willReturn(0);
        $fixer->isRisky()->willReturn(true);
        $fixer->getSuccessorsNames()->willReturn(['Foo/baz']);

        $generator = new FixerOptionValidatorGenerator();
        $functionNames = ['foo', 'test'];
        $functions = new FixerOptionBuilder('functions', 'List of `function` names to fix.');
        $functions = $functions
            ->setAllowedTypes(['array'])
            ->setAllowedValues([
                $generator->allowedValueIsSubsetOf($functionNames),
            ])
            ->setDefault($functionNames)
            ->getOption()
        ;

        $fixer->getConfigurationDefinition()->willReturn(new FixerConfigurationResolver([$functions]));
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
        $fixer->configure([])->will(function () use (&$things) {
            $things = false;
        });
        $fixer->configure(['functions' => ['foo', 'bar']])->will(function () use (&$things) {
            $things = true;
        });

        $fixer->fix(
            Argument::type(\SplFileInfo::class),
            Argument::type(\PhpCsFixer\Tokenizer\Tokens::class)
        )->will(function (array $arguments) use (&$things) {
            $arguments[1][3] = new Token([
                $arguments[1][3]->getId(),
                ($things ? '\'good stuff and good thing\'' : '\'good stuff and bad thing\''),
            ]);
        });

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerFixer($fixer->reveal(), true);

        $this->application->add(new DescribeCommand($fixerFactory));

        $command = $this->application->find('describe');

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
