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
        $this->expectException(
            'PhpCsFixer\\PregException'
        );
        $this->expectExceptionMessage(
            'Error occurred when calling preg_match.'
        );

        Preg::match('', 'foo', $matches);
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
        $this->expectException(
            'PhpCsFixer\\PregException'
        );
        $this->expectExceptionMessage(
            'Error occurred when calling preg_match_all.'
        );

        Preg::matchAll('', 'foo', $matches);
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
        $this->expectException(
            'PhpCsFixer\\PregException'
        );
        $this->expectExceptionMessage(
            'Error occurred when calling preg_replace.'
        );

        Preg::replace('', 'foo', 'bar');
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
        $this->expectException(
            'PhpCsFixer\\PregException'
        );
        $this->expectExceptionMessage(
            'Error occurred when calling preg_replace_callback.'
        );

        Preg::replaceCallback('', 'sort', 'foo');
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
        return [
            ['/u/u', 'u'],
            ['/u/u', 'u/u'],
            ['/./', chr(224).'bc'],
            ['/à/', 'àbc'],
            ['/'.chr(224).'|í/', 'àbc'],
        ];
    }

    public function provideArrayOfPatternsCases()
    {
        return [
            [['/à/', '/í/'], 'Tàíl'],
            [['/'.chr(174).'/', '/'.chr(224).'/'], 'foo'],
        ];
    }

    public function testSplitFailing()
    {
        $this->expectException(
            'PhpCsFixer\\PregException'
        );
        $this->expectExceptionMessage(
            'Error occurred when calling preg_split.'
        );

        Preg::split('', 'foo');
    }

    /**
     * @param string $pattern
     *
     * @dataProvider provideCommonCases
     */
    public function testSplit($pattern)
    {
        $expectedResult = preg_split($pattern, 'foo');
        $actualResult = Preg::split($pattern, 'foo');

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testCorrectnessForUtf8String()
    {
        $pattern = '/./';
        $subject = 'àbc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        $this->assertSame(['à'], $methodMatches);
        $this->assertNotSame(['à'], $functionMatches);
    }

    public function testCorrectnessForNonUtf8String()
    {
        $pattern = '/./u';
        $subject = chr(224).'bc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        $this->assertSame([chr(224)], $methodMatches);
        $this->assertNotSame([chr(224)], $functionMatches);
    }
}
