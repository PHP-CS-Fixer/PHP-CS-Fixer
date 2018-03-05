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

use PhpCsFixer\Preg;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @covers \PhpCsFixer\Preg
 *
 * @internal
 */
final class PregTest extends TestCase
{
    public function testMatchFailing()
    {
        $this->setExpectedException(
            'PhpCsFixer\\PregException',
            'Error occurred when calling preg_match.'
        );

        Preg::match('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar', $matches);
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     */
    public function testMatch($pattern, $subject)
    {
        $expectedResult = preg_match($pattern, $subject, $expectedMatches);
        $actualResult = Preg::match($pattern, $subject, $actualMatches);

        $this->assertSame($expectedResult, $actualResult);
        $this->assertSame($expectedMatches, $actualMatches);
    }

    public function testMatchAllFailing()
    {
        $this->setExpectedException(
            'PhpCsFixer\\PregException',
            'Error occurred when calling preg_match_all.'
        );

        Preg::matchAll('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar', $matches);
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     */
    public function testMatchAll($pattern, $subject)
    {
        $expectedResult = preg_match_all($pattern, $subject, $expectedMatches);
        $actualResult = Preg::matchAll($pattern, $subject, $actualMatches);

        $this->assertSame($expectedResult, $actualResult);
        $this->assertSame($expectedMatches, $actualMatches);
    }

    public function testReplaceFailing()
    {
        $this->setExpectedException(
            'PhpCsFixer\\PregException',
            'Error occurred when calling preg_replace.'
        );

        Preg::replace('/(?:\D+|<\d+>)*[!?]/', 'foo', 'foobar foobar foobar');
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     * @dataProvider provideArrayOfPatternsCases
     */
    public function testReplace($pattern, $subject)
    {
        $expectedResult = preg_replace($pattern, 'foo', $subject);
        $actualResult = Preg::replace($pattern, 'foo', $subject);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReplaceCallbackFailing()
    {
        $this->setExpectedException(
            'PhpCsFixer\\PregException',
            'Error occurred when calling preg_replace_callback.'
        );

        Preg::replaceCallback('/(?:\D+|<\d+>)*[!?]/', 'sort', 'foobar foobar foobar');
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     * @dataProvider provideArrayOfPatternsCases
     */
    public function testReplaceCallback($pattern, $subject)
    {
        $callback = function (array $x) { return implode('-', $x); };

        $expectedResult = preg_replace_callback($pattern, $callback, $subject);
        $actualResult = Preg::replaceCallback($pattern, $callback, $subject);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function provideCommonCases()
    {
        return array(
            array('/u/u', 'u'),
            array('/u/u', 'u/u'),
            array('/./', chr(224).'bc'),
            array('/à/', 'àbc'),
            array('/'.chr(224).'|í/', 'àbc'),
        );
    }

    public function provideArrayOfPatternsCases()
    {
        return array(
            array(array('/à/', '/í/'), 'Tàíl'),
            array(array('/'.chr(174).'/', '/'.chr(224).'/'), 'foo'),
        );
    }

    public function testSplitUtf8Pattern()
    {
        $expectedResult = preg_split('/à/u', 'àbc');
        $actual = Preg::split('/à/u', 'àbc');

        $this->assertSame($expectedResult, $actual);
    }

    public function testSplitNonUtf8Pattern()
    {
        $expectedResult = preg_split('/'.chr(224).'|í/', 'àbc');
        $actual = Preg::split('/'.chr(224).'|í/', 'àbc');

        $this->assertSame($expectedResult, $actual);
    }

    public function testCorrectnessForUtf8String()
    {
        $pattern = '/./';
        $subject = 'àbc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        $this->assertSame(array('à'), $methodMatches);
        $this->assertNotSame(array('à'), $functionMatches);
    }

    public function testCorrectnessForNonUtf8String()
    {
        $pattern = '/./u';
        $subject = chr(224).'bc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        $this->assertSame(array(chr(224)), $methodMatches);
        $this->assertNotSame(array(chr(224)), $functionMatches);
    }
}
