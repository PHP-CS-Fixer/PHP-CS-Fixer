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

use Symfony\CS\Tokenizer\TransformerInterface;
use Symfony\CS\Tokenizer\Transformers;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TransformersTest extends \PHPUnit_Framework_TestCase
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
        $transformers = Transformers::create();

        $transformersReflection = new \ReflectionClass($transformers);
        $propertyReflection = $transformersReflection->getProperty('items');
        $propertyReflection->setAccessible(true);

        $items = $propertyReflection->getValue($transformers);

        $cases = array();

        foreach ($items as $item) {
            foreach ($item->getCustomTokenNames() as $name) {
                $cases[] = array($name);
            }
        }

        return $cases;
    }

    /**
     * @dataProvider getPriorityCases
     */
    public function testPriority(TransformerInterface $first, TransformerInterface $second)
    {
        $this->assertLessThan($first->getPriority(), $second->getPriority());
    }

    public function getPriorityCases()
    {
        $transformersObject = Transformers::create();
        $transformers = array();

        foreach ($transformersObject->getTransformers() as $transformer) {
            $transformers[$transformer->getName()] = $transformer;
        }

        return array(
            array($transformers['curly_close'], $transformers['dynamic_prop_brace']),
            array($transformers['curly_close'], $transformers['dollar_close_curly_braces']),
        );
    }
}
