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
    public function testExecuteOutput()
    {
        $expected = <<<'EOT'
Description of Foo/bar rule.
Fixes stuff.
Replaces bad stuff with good stuff.

Fixer is configurable using following options:
* functions (array): list of `function` names to fix; defaults to ['foo', 'test']

Fixing examples:
 * Example #1.
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ @@
   -<?php echo 'bad stuff and bad thing';
   +<?php echo 'good stuff and bad thing';
   ----------- end diff -----------

 * Example #2. Fixing with configuration: ['functions' => ['foo', 'bar']].
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ @@
   -<?php echo 'bad stuff and bad thing';
   +<?php echo 'good stuff and good thing';
   ----------- end diff -----------


EOT;

        $this->assertSame($expected, $this->execute(false)->getDisplay(true));
    }

    public function testExecuteOutputWithDecoration()
    {
        $expected = <<<EOT
\033[32mDescription of\033[39m Foo/bar \033[32mrule\033[39m.
Fixes stuff.
Replaces bad stuff with good stuff.

Fixer is configurable using following options:
* \033[32mfunctions\033[39m (\033[33marray\033[39m): list of \033[32m`function`\033[39m names to fix; defaults to \033[33m['foo', 'test']\033[39m

Fixing examples:
 * Example #1.
\033[33m   ---------- begin diff ----------\033[39m
   \033[31m--- Original\033[39m
   \033[32m+++ New\033[39m
   \033[36m@@ @@\033[39m
   \033[31m-<?php echo 'bad stuff and bad thing';\033[39m
   \033[32m+<?php echo 'good stuff and bad thing';\033[39m
\033[33m   ----------- end diff -----------\033[39m

 * Example #2. Fixing with configuration: \033[33m['functions' => ['foo', 'bar']]\033[39m.
\033[33m   ---------- begin diff ----------\033[39m
   \033[31m--- Original\033[39m
   \033[32m+++ New\033[39m
   \033[36m@@ @@\033[39m
   \033[31m-<?php echo 'bad stuff and bad thing';\033[39m
   \033[32m+<?php echo 'good stuff and good thing';\033[39m
\033[33m   ----------- end diff -----------\033[39m


EOT;

        $actual = $this->execute(true)->getDisplay(true);

        if (false !== strpos($actual, "\033[0m")) {
            $expected = str_replace("\033[39m", "\033[0m", $expected);
        }

        $this->assertSame($expected, $actual);
    }

    public function testExecuteStatusCode()
    {
        $this->assertSame(0, $this->execute(false)->getStatusCode());
    }

    public function testExecuteWithUnknownName()
    {
        $application = new Application();
        $application->add(new DescribeCommand(new FixerFactory()));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);

        $this->setExpectedException('InvalidArgumentException', 'Rule Foo/bar not found.');
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => 'Foo/bar',
        ));
    }

    public function testExecuteWithoutName()
    {
        $application = new Application();
        $application->add(new DescribeCommand(new FixerFactory()));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);

        $this->setExpectedExceptionRegExp('RuntimeException', '/^Not enough arguments( \(missing: "name"\))?\.$/');
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));
    }

    /**
     * @param bool $decorated
     *
     * @return CommandTester
     */
    private function execute($decorated)
    {
        $fixer = $this->prophesize();
        $fixer->willImplement('PhpCsFixer\Fixer\DefinedFixerInterface');
        $fixer->willImplement('PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface');

        $fixer->getName()->willReturn('Foo/bar');
        $fixer->getPriority()->willReturn(0);
        $fixer->isRisky()->willReturn(false);

        $generator = new FixerOptionValidatorGenerator();
        $functionNames = array('foo', 'test');
        $functions = new FixerOptionBuilder('functions', 'List of `function` names to fix.');
        $functions = $functions
            ->setAllowedTypes(array('array'))
            ->setAllowedValues(array(
                $generator->allowedValueIsSubsetOf($functionNames),
            ))
            ->setDefault($functionNames)
            ->getOption()
        ;

        $fixer->getConfigurationDefinition()->willReturn(new FixerConfigurationResolver(array($functions)));
        $fixer->getDefinition()->willReturn(new FixerDefinition(
            'Fixes stuff.',
            array(
                new CodeSample(
                    '<?php echo \'bad stuff and bad thing\';'
                ),
                new CodeSample(
                    '<?php echo \'bad stuff and bad thing\';',
                    array('functions' => array('foo', 'bar'))
                ),
            ),
            'Replaces bad stuff with good stuff.',
            'Can break stuff.'
        ));

        $things = false;
        $fixer->configure(array())->will(function () use (&$things) {
            $things = false;
        });

        $fixer->configure(array('functions' => array('foo', 'bar')))->will(function () use (&$things) {
            $things = true;
        });

        $fixer->fix(
            Argument::type('SplFileInfo'),
            Argument::type('PhpCsFixer\Tokenizer\Tokens')
        )->will(function (array $arguments) use (&$things) {
            $arguments[1][3] = new Token(array(
                $arguments[1][3]->getId(),
                ($things ? '\'good stuff and good thing\'' : '\'good stuff and bad thing\''),
            ));
        });

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerFixer($fixer->reveal(), true);

        $application = new Application();
        $application->add(new DescribeCommand($fixerFactory));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'name' => 'Foo/bar',
            ),
            array(
                'decorated' => $decorated,
            )
        );

        return $commandTester;
    }
}
