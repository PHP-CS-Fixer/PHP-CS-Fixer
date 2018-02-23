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
        $expected =
"Description of Foo/bar rule.
Fixes stuff.
Replaces bad stuff with good stuff.

Fixer applying this rule is risky.
Can break stuff.

Fixer is configurable using following option:
* functions (a subset of ['foo', 'test']): list of `function` names to fix; defaults to ['foo', 'test']; DEPRECATED alias: funcs

Fixing examples:
 * Example #1. Fixing with the default configuration.
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ @@
   -<?php echo 'bad stuff and bad thing';
   +<?php echo 'good stuff and bad thing';
   "."
   ----------- end diff -----------

 * Example #2. Fixing with configuration: ['functions' => ['foo', 'bar']].
   ---------- begin diff ----------
   --- Original
   +++ New
   @@ @@
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
Fixes stuff.
Replaces bad stuff with good stuff.

\033[37;41mFixer applying this rule is risky.\033[39;49m
Can break stuff.

Fixer is configurable using following option:
* \033[32mfunctions\033[39m (a subset of \033[33m['foo', 'test']\033[39m): list of \033[32m`function`\033[39m names to fix; defaults to \033[33m['foo', 'test']\033[39m; DEPRECATED alias: \033[33mfuncs\033[39m

Fixing examples:
 * Example #1. Fixing with the \033[33mdefault\033[39m configuration.
\033[33m   ---------- begin diff ----------\033[39m
   \033[31m--- Original\033[39m
   \033[32m+++ New\033[39m
   \033[36m@@ @@\033[39m
   \033[31m-<?php echo 'bad stuff and bad thing';\033[39m
   \033[32m+<?php echo 'good stuff and bad thing';\033[39m
   "."
\033[33m   ----------- end diff -----------\033[39m

 * Example #2. Fixing with configuration: \033[33m['functions' => ['foo', 'bar']]\033[39m.
\033[33m   ---------- begin diff ----------\033[39m
   \033[31m--- Original\033[39m
   \033[32m+++ New\033[39m
   \033[36m@@ @@\033[39m
   \033[31m-<?php echo 'bad stuff and bad thing';\033[39m
   \033[32m+<?php echo 'good stuff and good thing';\033[39m
   "."
\033[33m   ----------- end diff -----------\033[39m

";
        $actual = $this->execute('Foo/bar', true)->getDisplay(true);

        if (false !== strpos($actual, "\033[0m")) {
            $expected = str_replace("\033[39;49m", "\033[0m", $expected);
            $expected = str_replace("\033[39m", "\033[0m", $expected);
        }

        $this->assertSame($expected, $actual);
    }

    public function testExecuteStatusCode()
    {
        $this->assertSame(0, $this->execute('Foo/bar', false)->getStatusCode());
    }

    public function testExecuteWithUnknownRuleName()
    {
        $application = new Application();
        $application->add(new DescribeCommand(new FixerFactory()));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);

        $this->setExpectedExceptionRegExp('InvalidArgumentException', '#^Rule "Foo/bar" not found\.$#');
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => 'Foo/bar',
        ));
    }

    public function testExecuteWithUnknownSetName()
    {
        $application = new Application();
        $application->add(new DescribeCommand(new FixerFactory()));

        $command = $application->find('describe');

        $commandTester = new CommandTester($command);

        $this->setExpectedExceptionRegExp('InvalidArgumentException', '#^Set "@NoSuchSet" not found\.$#');
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => '@NoSuchSet',
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

    public function testGetAlternativeSuggestion()
    {
        $this->setExpectedExceptionRegExp('InvalidArgumentException', '#^Rule "Foo2/bar" not found\. Did you mean "Foo/bar"\?$#');
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
        $fixer->willImplement('PhpCsFixer\Fixer\DefinedFixerInterface');
        $fixer->willImplement('PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface');

        $fixer->getName()->willReturn('Foo/bar');
        $fixer->getPriority()->willReturn(0);
        $fixer->isRisky()->willReturn(true);

        $functionNames = array('foo', 'test');
        $functions = new AliasedFixerOptionBuilder(new FixerOptionBuilder('functions', 'List of `function` names to fix.'), 'funcs');
        $functions = $functions
            ->setAllowedTypes(array('array'))
            ->setAllowedValues(array(new AllowedValueSubset($functionNames)))
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
                'name' => $name,
            ),
            array(
                'decorated' => $decorated,
            )
        );

        return $commandTester;
    }
}
