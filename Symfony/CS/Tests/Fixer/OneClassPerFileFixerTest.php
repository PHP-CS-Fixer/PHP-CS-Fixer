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

use Symfony\CS\Fixer\OneClassPerFileFixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class OneClassPerFileFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input, $file)
    {
        $this->expectOutputString($expected);

        $fixer = new OneClassPerFileFixer();

        $fixer->fix($file, $input);
    }

    public function provideExamples()
    {
        $file = new \SplFileInfo(__FILE__);
        $expectedPrefix = '! Found multiple classes/interfaces/traits in ' . strtr($file->getRealPath(), '\\', '/') . ': ';

        $examples = array(
            array('', '<?php namespace Foo; class Bar {}', $file),
            array($expectedPrefix . 'Bar, Baz' . PHP_EOL, '<?php namespace Foo; class Bar {} class Baz {}', $file),
            array('', '<?php namespace Foo; interface Bar {}', $file),
            array($expectedPrefix . 'Bar, Baz' . PHP_EOL, '<?php namespace Foo; interface Bar {} interface Baz {}', $file),
            array($expectedPrefix . 'Bar, Baz' . PHP_EOL, '<?php namespace Foo; class Bar {} interface Baz {}', $file),
        );

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $examples[] = array('', '<?php namespace Foo; trait Bar {}', $file);
            $examples[] = array($expectedPrefix . 'Bar, Baz' . PHP_EOL, '<?php namespace Foo; trait Bar {} trait Baz {}', $file);
            $examples[] = array($expectedPrefix . 'Bar, Baz' . PHP_EOL, '<?php namespace Foo; trait Bar {} class Baz {}', $file);
            $examples[] = array($expectedPrefix . 'Bar, Baz' . PHP_EOL, '<?php namespace Foo; trait Bar {} interface Baz {}', $file);
        }

        return $examples;
    }
}
