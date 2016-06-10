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
final class SingleLineFunctionDeclarationFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    function bar(Bar $thing, $other)
                    {
                    }
                }',
            ),
            array(
                '<?php
                function bar(Bar $thing, $other)
                {
                }',
            ),
            array(
                '<?php
interface ConfigInterface
{
    /**
     * Returns the name of the configuration.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the configuration
     */
    public function getName();

    /**
     * Returns the description of the configuration.
     *
     * A short one-line description for the configuration.
     *
     * @return string The description of the configuration
     */
    public function getDescription();

    /**
     * Returns an iterator of files to scan.
     *
     * @return \Traversable A \Traversable instance that returns \SplFileInfo instances
     */
    public function getFinder();
}
                ',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    function bar(Bar $thing, $other)
                    {
                    }
                }',
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    function bar(
                    Bar $thing,
                    
                    $other
                    )
                    {
                    }
                }',
            ),
            array(
                '<?php
                abstract class Foo extends Bar implements BarInterface, \Traversable
                {
                    abstract function bar(Bar $thing, $other);
                }',
                '<?php
                abstract class Foo extends Bar implements BarInterface, \Traversable
                {
                    abstract
                    function bar(
                    Bar $thing,
                    
                    $other
                    )
                    ;
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    final function bar(Bar $thing, $other)
                    {
                    }
                }',
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    final
                    function bar(
                    Bar $thing,
                    
                    $other
                    )
                    {
                    }
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    private function bar(Bar $thing, $other)
                    {
                    }
                }',
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    private
                    function bar(
                    Bar $thing,
                    
                    $other
                    )
                    {
                    }
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    protected function bar(Bar $thing, $other)
                    {
                    }
                }',
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    protected
                    function bar(
                    Bar $thing,
                    
                    $other
                    )
                    {
                    }
                }',
            ),
            array(
                '<?php
                abstract class Foo extends Bar implements BarInterface, \Traversable
                {
                    abstract protected function bar(Bar $thing, $other);
                }',
                '<?php
                abstract class Foo extends Bar implements BarInterface, \Traversable
                {
                    abstract
                    protected
                    function bar(
                    Bar $thing,
                    
                    $other
                    );
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    public function bar(Bar $thing, $other)
                    {
                    }
                }',
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    public
                    function bar(
                    Bar $thing,
                    
                    $other
                    )
                    {
                    }
                }',
            ),
            array(
                '<?php
                abstract class Foo extends Bar implements BarInterface, \Traversable
                {
                    abstract public function bar(Bar $thing, $other);
                }',
                '<?php
                abstract class Foo extends Bar implements BarInterface, \Traversable
                {
                    abstract
                    public
                    function bar(
                    Bar $thing,
                    
                    $other
                    );
                }',
            ),
            array(
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    final public function bar(Bar $thing, $other)
                    {
                    }
                }',
                '<?php
                class Foo extends Bar implements BarInterface, \Traversable
                {
                    final
                    public function bar(
                    Bar $thing,
                    
                    $other
                    )
                    {
                    }
                }',
            ),
            array(
                '<?php
                function bar(Bar $thing, $other)
                {
                }',
                '<?php
                function bar(Bar $thing,
                $other)
                {
                }',
            ),
            array(
                '<?php
interface ConfigInterface
{
    /**
     * Returns the name of the configuration.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the configuration
     */
    public function getName();
}
                  ',
                '<?php
interface ConfigInterface
{
    /**
     * Returns the name of the configuration.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the configuration
     */
    public
    function
    getName()
    ;
}
                  ',
            ),
        );
    }
}
