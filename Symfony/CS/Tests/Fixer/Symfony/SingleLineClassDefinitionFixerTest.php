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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class SingleLineClassDefinitionFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        if (version_compare(PHP_VERSION, '5.4', '<') && false !== strpos($input, 'trait')) {
            $this->markTestSkipped('PHP higher than 5.3 is required.');
        }

        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                }

                class Another extends Thing
                {
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable {
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                }',
                '<?php
                class Foo
                extends Bar
                implements BarInterface,
                \Traversable
                {
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable {
                }',
                '<?php
                class Foo
                extends Bar
                implements BarInterface,
                \Traversable {
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                }
            
                class Another extends Thing
                {
                }',
                '<?php
                class Foo
                extends Bar
                implements BarInterface, \Traversable
                {
                }
            
                class Another
                extends Thing
                {
                }',
            ),
            array(
                '<?php
                final class Foo extends Bar implements BarInterface, \Traversable
                {
                }',
                '<?php
                final 
                class Foo
                extends Bar
                implements BarInterface,
                \Traversable
                {
                }',
            ),
            array(
                '<?php
                abstract class Foo extends Bar implements BarInterface, \Traversable
                {
                }',
                '<?php
                abstract 
                class Foo    
                extends Bar   
                
                implements BarInterface,    
                \Traversable
                {
                }',
            ),
            array(
                '<?php
                interface AmazingInterface extends AnotherAmazingInterface
                {
                }',
                '<?php
                interface
                AmazingInterface
                extends
                AnotherAmazingInterface
                {
                }',
            ),
            array(
                '<?php
                trait AmazingTrait
                {
                }',
                '<?php
                trait
                AmazingTrait
                {
                }',
            ),
        );
    }
}
