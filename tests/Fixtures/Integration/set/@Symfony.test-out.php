<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme;

declare(ticks=1);

/**
 * Coding standards demonstration.
 */
class FooBar
{
    public const SOME_CONST = 42;

    private $fooBar;

    /**
     * @param string $dummy Some argument description
     */
    public function __construct($dummy)
    {
        $this->fooBar = $this->transformText($dummy);
    }

    /**
     * Foo.
     *
     * @param string      $dummy Some argument description
     * @param string|null $data  Foo
     *
     * @return string|null Transformed input
     *
     * @throws \RuntimeException
     */
    private function transformText($dummy, array $options = [], $data = null)
    {
        $fnc = function () { return true; };

        $mergedOptions = array_merge(
            [
                'some_default' => 'values',
                'another_default' => 'more values',
            ],
            $options
        );

        if (true === $dummy) {
            return;
        }

        if ('string' === $dummy) {
            if ('values' === $mergedOptions['some_default']) {
                return substr($dummy, 0, 5);
            }

            return ucwords($dummy);
        }

        throw new \RuntimeException(sprintf('Unrecognized dummy option "%s".', $dummy));
    }

    private function reverseBoolean($value = null, $theSwitch = false)
    {
        if (!$theSwitch) {
            return;
        }

        return !$value;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    private function printText($text)
    {
        echo $text;
    }
}

interface Test1Interface
{
}

interface Test2Interface
{
}

class FooBarTest extends \PHPUnit_Framework_TestCase implements Test1Interface, Test2Interface
{
    /**
     * @expectedException \Exception
     */
    public function testFooBar($a)
    {
        $b = 1 === $a ? 'a' : 'b';
        echo $b;
    }
}

final class FinalClass
{
    final public function finalMethod()
    {
    }
}

function callback($a, ...$b)
{
    return (--$a) * ($b++);
}

/** @var int $a */
$a = &$b;
$c = &$d;

echo 1;
