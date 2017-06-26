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
use PhpCsFixer\RuleSetInterface;
use PhpCsFixer\Tokenizer\Token;
use PHPUnit\Framework\TestCase;
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
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->application = new Application();
    }

    public function testExecuteOutput()
    {
        $expected = <<<'EOT'
Description of Foo/bar rule.
Fixes stuff.
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

        $this->assertSame($expected, $this->execute('Foo/bar', false)->getDisplay(true));
    }

    public function testExecuteOutputWithDecoration()
    {
        $expected = <<<EOT
\033[32mDescription of\033[39m Foo/bar \033[32mrule\033[39m.
Fixes stuff.
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

        $actual = $this->execute('Foo/bar', true)->getDisplay(true);

        if (false !== strpos($actual, "\033[0m")) {
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
        $this->application->add(new DescribeCommand(new FixerFactory()));

        $command = $this->application->find('describe');

        $commandTester = new CommandTester($command);

        $this->setExpectedExceptionRegExp('InvalidArgumentException', '#^Rule "Foo/bar" not found\.$#');
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => 'Foo/bar',
        ));
    }

    public function testExecuteWithUnknownSetName()
    {
        $this->application->add(new DescribeCommand(new FixerFactory()));

        $command = $this->application->find('describe');

        $commandTester = new CommandTester($command);

        $this->setExpectedExceptionRegExp('InvalidArgumentException', '#^Set "@NoSuchSet" not found\.$#');
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => '@NoSuchSet',
        ));
    }

    public function testExecuteWithoutName()
    {
        $this->application->add(new DescribeCommand(new FixerFactory()));

        $command = $this->application->find('describe');

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
     * @param bool   $decorated
     * @param string $expected
     *
     * @dataProvider provideDescribeSetCases
     */
    public function testDescribeSet($decorated, $expected)
    {
        $this->assertSame(
            $expected,
            $this->execute('@testSet', $decorated)->getDisplay(true)
        );
    }

    public function provideDescribeSetCases()
    {
        return array(
            array(
                true,
<<<EOT
\033[32mDescription of\033[39m @testSet \033[32mset.\033[39m

 * \033[32mFoo/bar\033[39m \033[37;41mrisky\033[39;49m
   | Fixes stuff.
   \033[33m| Configuration: ['functions' => ['foo' => 'bar']]\033[39m


EOT
            ),
            array(
                false,
<<<'EOT'
Description of @testSet set.

 * Foo/bar risky
   | Fixes stuff.
   | Configuration: ['functions' => ['foo' => 'bar']]


EOT
            ),
        );
    }

    /**
     * @param string $name
     * @param bool   $decorated
     * @param int    $verbosityLevel
     *
     * @return CommandTester
     */
    private function execute($name, $decorated, $verbosityLevel = OutputInterface::VERBOSITY_NORMAL)
    {
        $fixer = $this->prophesize();
        $fixer->willImplement('PhpCsFixer\Fixer\DefinedFixerInterface');
        $fixer->willImplement('PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface');

        $fixer->configure(array('functions' => array('foo' => 'bar')))->willReturn(null);
        $fixer->getName()->willReturn('Foo/bar');
        $fixer->getPriority()->willReturn(0);

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

        $fixer->isRisky()->willReturn(true);

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

        $ruleSet = new TestRuleSet();
        $fixerFactory->useRuleSet($ruleSet);

        $this->application->add(new DescribeCommand($fixerFactory, $ruleSet));

        $command = $this->application->find('describe');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'name' => $name,
            ),
            array(
                'decorated' => $decorated,
                'verbosity' => $verbosityLevel,
            )
        );

        return $commandTester;
    }
}

/**
 * @internal
 * @coversNothing
 */
final class TestRuleSet implements RuleSetInterface
{
    private $rules;

    public function __construct(array $set = array())
    {
        $this->rules = array('Foo/bar' => array('functions' => array('foo' => 'bar')));
    }

    public static function create(array $set = array())
    {
        return new self();
    }

    public function getRuleConfiguration($rule)
    {
        return $this->rules[$rule];
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getSetDefinitionNames()
    {
        return array('@testSet');
    }

    public function hasRule($rule)
    {
        return isset($this->rules[$rule]);
    }
}
