<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocAnnotationWithoutDotFixer>
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocAnnotationWithoutDotFixer
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocAnnotationWithoutDotFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
    /**
     * Summary.
     *
     * Description.
     *
     * @param string|null $str   some string
     * @param string $ip         IPv4 is not lowercased
     * @param string $a          A
     * @param string $a_string   a string
     * @param string $ab         ab
     * @param string $t34        T34
     * @param string $s          S§
     * @param string $genrb      Optional. The path to the "genrb" executable
     * @param string $ellipsis1  Ellipsis is this: ...
     * @param string $ellipsis2  Ellipsis is this: 。。。
     * @param string $ellipsis3  Ellipsis is this: …
     * @param bool   $isStr      Is it a string?
     * @param int    $int        Some single-line description. With many dots.
     * @param int    $int        Some multiline
     *                           description. With many dots.
     *
     * @return array result array
     *
     * @SomeCustomAnnotation This is important sentence that must not be modified.
     */',
            '<?php
    /**
     * Summary.
     *
     * Description.
     *
     * @param string|null $str   Some string.
     * @param string $ip         IPv4 is not lowercased.
     * @param string $a          A.
     * @param string $a_string   A string.
     * @param string $ab         Ab.
     * @param string $t34        T34.
     * @param string $s          S§.
     * @param string $genrb      Optional. The path to the "genrb" executable
     * @param string $ellipsis1  Ellipsis is this: ...
     * @param string $ellipsis2  Ellipsis is this: 。。。
     * @param string $ellipsis3  Ellipsis is this: …
     * @param bool   $isStr      Is it a string?
     * @param int    $int        Some single-line description. With many dots.
     * @param int    $int        Some multiline
     *                           description. With many dots.
     *
     * @return array Result array。
     *
     * @SomeCustomAnnotation This is important sentence that must not be modified.
     */',
        ];

        yield [
            // invalid char inside line won't crash the fixer
            '<?php
    /**
     * @var string this: '.\chr(174).' is an odd character
     * @var string This: '.\chr(174).' is an odd character 2nd time。
     */',
            '<?php
    /**
     * @var string This: '.\chr(174).' is an odd character.
     * @var string This: '.\chr(174).' is an odd character 2nd time。
     */',
        ];

        yield [
            '<?php
    /**
     * @deprecated since version 2. Use emergency() which is PSR-3 compatible.
     */',
        ];

        yield [
            '<?php
    /**
     * @internal This method is public to be usable as callback. It should not
     *           be used in user code.
     */',
        ];

        yield [
            '<?php
    /**
     * @deprecated this is
     *             deprecated
     */',
            '<?php
    /**
     * @deprecated This is
     *             deprecated.
     */',
        ];

        yield [
            '<?php
    /**
     * @return bool|null returns `true` if the class has a single-column ID
     *                   and Returns `false` otherwise
     */',
            '<?php
    /**
     * @return bool|null Returns `true` if the class has a single-column ID
     *                   and Returns `false` otherwise.
     */',
        ];

        yield [
            '<?php
    /**
     * @throws \Exception having whitespaces after dot, yet I am fixed
     */',
            '<?php
    /**
     * @throws \Exception having whitespaces after dot, yet I am fixed.   '.'
     */',
        ];

        yield [
            '<?php
    /**
     * @throws \Exception having tabs after dot, yet I am fixed
     */',
            '<?php
    /**
     * @throws \Exception having tabs after dot, yet I am fixed.		'.'
     */',
        ];

        yield [
            '<?php
    /**
     * Dispatches an event to all registered listeners.
     *
     * @param string    $eventName The name of the event to dispatch. The name of the event is
     *                             the name of the method that is invoked on listeners.
     * @param EventArgs $eventArgs The event arguments to pass to the event handlers/listeners.
     *                             If not supplied, the single empty EventArgs instance is used.
     *
     * @return bool
     */
    function dispatchEvent($eventName, EventArgs $eventArgs = null) {}

    /**
     * Extract the `object_to_populate` field from the context if it exists
     * and is an instance of the provided $class.
     *
     * @param string $class   The class the object should be
     * @param array  $context The denormalization context
     * @param string $key     Key in which to look for the object to populate.
     *                        Keeps backwards compatibility with `AbstractNormalizer`.
     *
     * @return null|object an object if things check out, null otherwise
     */
    function extractObjectToPopulate($class, array $context, $key = null) {}
                ',
        ];

        yield [
            '<?php
    /**
     * This is a broken phpdoc - missing asterisk
     * @param string $str As it is broken, let us not apply the rule to description of parameter.

     */
    function foo($str) {}',
        ];

        yield [
            '<?php
    /**
     * @return bool|null Returns `true` if the class has a single-column ID.
                         Returns `false` otherwise.
                         That was multilined comment. With plenty of sentenced.
     */
    function nothingToDo() {}',
        ];

        yield [
            '<?php
/**
 * @param string $bar τάχιστη
 */
function foo ($bar) {}
',
            '<?php
/**
 * @param string $bar Τάχιστη.
 */
function foo ($bar) {}
',
        ];
    }
}
