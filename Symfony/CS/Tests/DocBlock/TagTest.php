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

namespace Symfony\CS\Tests\DocBlock;

use Symfony\CS\DocBlock\Tag;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideNameCases
     */
    public function testName($expected, $input)
    {
        $tag = new Tag($input);

        $this->assertSame($expected, $tag->getName());
    }

    public function provideNameCases()
    {
        return array(
            array('param', '     * @param Foo $foo'),
            array('return', '*   @return            false'),
            array('thRoWs', '*@thRoWs \Exception'),
            array('THROWSSS', "\t@THROWSSS\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n"),
            array('other', ' *   @\Foo\Bar(baz = 123)'),
            array('expectedException', '     * @expectedException Exception'),
            array('property-read', ' * @property-read integer $daysInMonth number of days in the given month'),
            array('method', ' * @method'),
            array('method', ' * @method string getString()'),
            array('other', ' * @method("GET")'),
        );
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValid($expected, $input)
    {
        $tag = new Tag($input);

        $this->assertSame($expected, $tag->valid());
    }

    public function provideValidCases()
    {
        return array(
            array(true, '     * @param Foo $foo'),
            array(true, '*   @return            false'),
            array(true, '*@throws \Exception'),
            array(true, ' * @method'),
            array(true, ' * @method string getString()'),
            array(true, ' * @property-read integer $daysInMonth number of days in the given month'),
            array(false, ' * @method("GET")'),
            array(false, '*@thRoWs \InvalidArgumentException'),
            array(false, "\t@THROWSSS\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n"),
            array(false, ' *   @\Foo\Bar(baz = 123)'),
            array(false, '     * @expectedException Exception'),
        );
    }
}
