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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Test\AccessibleObject;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class AbstractFunctionReferenceFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testCountArguments($code, $openIndex, $closeIndex, $argumentsCount)
    {
        $mock = new AccessibleObject($this->getMockForAbstractClass('\\PhpCsFixer\\AbstractFunctionReferenceFixer'));

        $this->assertSame(
            $argumentsCount,
            $mock->countArguments(Tokens::fromCode($code), $openIndex, $closeIndex)
        );
    }

    public function provideCases()
    {
        return array(
            array('<?php fnc();', 2, 3, 0),
            array('<?php fnc($a);', 2, 4, 1),
            array('<?php fnc($a, $b);', 2, 7, 2),
            array('<?php fnc($a, $b = array(1,2), $c = 3);', 2, 23, 3),
        );
    }
}
