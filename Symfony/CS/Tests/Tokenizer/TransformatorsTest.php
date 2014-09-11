<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer;

use Symfony\CS\Tokenizer\Transformators;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TransformatorsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCustomTokenPrefixCases
     */
    public function testCustomTokenPrefix($name)
    {
        $this->assertStringStartsWith('CT_', $name, 'Custom token should start with `CT_` prefix.');
    }

    public function provideCustomTokenPrefixCases()
    {
        $transformators = Transformators::create();

        $transformatorsReflection = new \ReflectionClass($transformators);
        $propertyReflection = $transformatorsReflection->getProperty('items');
        $propertyReflection->setAccessible(true);

        $items = $propertyReflection->getValue($transformators);

        $cases = array();

        foreach ($items as $item) {
            foreach ($item->getCustomTokenNames() as $name) {
                $cases[] = array($name);
            }
        }

        return $cases;
    }
}
