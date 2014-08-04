<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\ExtraEmptyLinesFixer;

class ExtraEmptyLinesFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = $this->getTestFile();

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

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixWithManyEmptyLines()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = $this->getTestFile();

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

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixWithHeredoc()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
<?php
$b = <<<TEXT
Foo TEXT
Bar


FooFoo
TEXT;
EOF;

        $input = <<<'EOF'
<?php
$b = <<<TEXT
Foo TEXT
Bar


FooFoo
TEXT;
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixWithNowdoc()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
<?php
$b = <<<'TEXT'
Foo TEXT;
Bar1}


FooFoo
TEXT;
EOF;

        $input = <<<'EOF'
<?php
$b = <<<'TEXT'
Foo TEXT;
Bar1}


FooFoo
TEXT;
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixWithEncapsulatedNowdoc()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
<?php
$b = <<<'TEXT'
Foo TEXT
Bar

<<<'TEMPLATE'
BarFooBar TEMPLATE


TEMPLATE;


FooFoo
TEXT;
EOF;

        $input = <<<'EOF'
<?php
$b = <<<'TEXT'
Foo TEXT
Bar

<<<'TEMPLATE'
BarFooBar TEMPLATE


TEMPLATE;


FooFoo
TEXT;
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixWithMultilineString()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
<?php
$a = 'Foo


Bar';
EOF;

        $input = <<<'EOF'
<?php
$a = 'Foo


Bar';
EOF;

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixWithTrickyMultilineStrings()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = $this->getTestFile();

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

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function testFixWithCommentWithAQuote()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = new \SplFileInfo(__FILE__);

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

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
