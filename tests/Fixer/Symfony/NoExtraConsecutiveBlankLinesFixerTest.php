<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Symfony;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @internal
 */
final class NoExtraConsecutiveBlankLinesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param int[]         $lineNumberRemoved Line numbers expected to be removed after fixing
     * @param string[]|null $config
     *
     * @dataProvider provideConfigTests
     */
    public function testWithConfig(array $lineNumberRemoved, array $config = null)
    {
        $this->getFixer()->configure($config);
        $template = <<<'EOF'
<?php
use \DateTime;

use \stdClass;

use \InvalidArgumentException;

class Test {

    public function testThrow($a)
    {
        if ($a) {
            throw new InvalidArgumentException('test'); // test

        }
        $date = new DateTime();
        $class = new stdClass();
        $class = (string) $class;
        $e = new InvalidArgumentException($class.$date->format('Y'));
        throw $e;

    }



    public function testBreak($a)
    {
        switch($a) {
            case 1:
                echo $a;
                break;

            case 2:
                echo 'test';
                break;
        }
    }

    public function testContinueAndReturn($a, $b)
    {
        while($a < 100) {
            if ($b < time()) {
                continue;

            }

            return $b;

        }

        return $a;

    }
}
EOF;
        $this->doTest($this->removeLinesFromString($template, $lineNumberRemoved), $template);
    }

    public function provideConfigTests()
    {
        $tests = array(
            array(
                array(3, 5),
                array('use'),
            ),
            array(
                array(23, 24),
                array('extra'),
            ),
            array(
                array(48, 52),
                array('return'),
            ),
            array(
                array(44),
                array('continue'),
            ),
            array(
                array(32),
                array('break'),
            ),
            array(
                array(14, 21),
                array('throw'),
            ),
        );

        $all = array(array(), array());
        foreach ($tests as $test) {
            $all[0] = array_merge($test[0], $all[0]);
            $all[1] = array_merge($test[1], $all[1]);
        }
        $tests[] = $all;

        // default configuration test
        $tests[] = array(
            array(23, 24),
            null,
        );

        return $tests;
    }

    private function removeLinesFromString($input, array $lineNumbers)
    {
        sort($lineNumbers);
        $lines = explode("\n", $input);
        $lineCount = count($lines);
        foreach ($lineNumbers as $lineNumber) {
            --$lineNumber;
            if ($lineNumber < 0 || $lineNumber > $lineCount) {
                throw new \InvalidArgumentException(sprintf('Line number "%d" out of range (0 - %d).', ++$lineNumber, $lineCount));
            }
            unset($lines[$lineNumber]);
        }

        return implode("\n", $lines);
    }

    public function testFix()
    {
        $expected = <<<'EOF'
<?php
$a = new Bar();

$a = new FooBaz();
EOF;

        $input = <<<'EOF'
<?php
$a = new Bar();


$a = new FooBaz();
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithManyEmptyLines()
    {
        $expected = <<<'EOF'
<?php
$a = new Bar();

$a = new FooBaz();
EOF;

        $input = <<<'EOF'
<?php
$a = new Bar();






$a = new FooBaz();
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithHeredoc()
    {
        $expected = '
<?php
$b = <<<TEXT
Foo TEXT
Bar


FooFoo
TEXT;
';

        $this->doTest($expected);
    }

    public function testFixWithNowdoc()
    {
        $expected = '
<?php
$b = <<<\'TEXT\'
Foo TEXT;
Bar1}


FooFoo
TEXT;
';

        $this->doTest($expected);
    }

    public function testFixWithEncapsulatedNowdoc()
    {
        $expected = '
<?php
$b = <<<\'TEXT\'
Foo TEXT
Bar

<<<\'TEMPLATE\'
BarFooBar TEMPLATE


TEMPLATE;


FooFoo
TEXT;
';

        $this->doTest($expected);
    }

    public function testFixWithMultilineString()
    {
        $expected = <<<'EOF'
<?php
$a = 'Foo


Bar';
EOF;

        $this->doTest($expected);
    }

    public function testFixWithTrickyMultilineStrings()
    {
        $expected = <<<'EOF'
<?php
$a = 'Foo';

$b = 'Bar


Here\'s an escaped quote '

.

'


FooFoo';
EOF;

        $input = <<<'EOF'
<?php
$a = 'Foo';


$b = 'Bar


Here\'s an escaped quote '


.


'


FooFoo';
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithCommentWithAQuote()
    {
        $expected = <<<'EOF'
<?php
$a = 'foo';

// my comment's gotta have a quote
$b = 'foobar';

$c = 'bar';
EOF;

        $input = <<<'EOF'
<?php
$a = 'foo';


// my comment's gotta have a quote
$b = 'foobar';


$c = 'bar';
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithTrailingInlineBlock()
    {
        $expected = "
<?php
    echo 'ellow';
?>

\$a = 0;



//a

<?php

\$a = 0;

\$b = 1;

//a
?>



";
        $this->doTest($expected);
    }

    public function testFixWithComments()
    {
        $expected = <<<'EOF'
<?php
//class Test
$a; //

$b;
/***/

$c;
//

$d;
EOF;

        $input = <<<'EOF'
<?php
//class Test
$a; //




$b;
/***/



$c;
//



$d;
EOF;
        $this->doTest($expected, $input);
    }

    public function testFixWithComments2()
    {
        $input = "<?php\n//a\n\n\n\n\$a =1;";
        $expected = "<?php\n//a\n\n\$a =1;";
        $this->doTest($expected, $input);
    }

    public function testFixWithWindowsLineBreaks()
    {
        $input = "<?php\r\n//a\r\n\r\n\r\n\r\n\$a =1;";
        $expected = "<?php\r\n//a\n\n\$a =1;";
        $this->doTest($expected, $input);
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessage [no_extra_consecutive_blank_lines] Unknown configuration item "__TEST__" passed.
     */
    public function testWrongConfig()
    {
        $this->getFixer()->configure(array('__TEST__'));
    }
}
