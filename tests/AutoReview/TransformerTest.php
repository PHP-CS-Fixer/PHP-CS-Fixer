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

namespace PhpCsFixer\tests\AutoReview;

use PhpCsFixer\Tokenizer\TransformerInterface;
use PhpCsFixer\Tokenizer\Transformers;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 */
final class TransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param TransformerInterface $transformer
     *
     * @dataProvider provideTransformers
     */
    public function testTransformersAreFinal(TransformerInterface $transformer)
    {
        $transformerRef = new \ReflectionClass($transformer);

        $this->assertTrue(
            $transformerRef->isFinal(),
            sprintf('Transformer "%s" must be declared "final."', $transformer->getName())
        );
    }

    /**
     * @return TransformerInterface[]
     */
    public function provideTransformers()
    {
        static $transformersArray = null;

        if (null === $transformersArray) {
            $transformers = Transformers::create();
            $reflection = new \ReflectionObject($transformers);
            $transformersItems = $reflection->getProperty('items');
            $transformersItems->setAccessible(true);
            $transformersItems = $transformersItems->getValue($transformers);
            $transformersArray = [];
            foreach ($transformersItems as $transformer) {
                $transformersArray[] = [$transformer];
            }
        }

        return $transformersArray;
    }
}
