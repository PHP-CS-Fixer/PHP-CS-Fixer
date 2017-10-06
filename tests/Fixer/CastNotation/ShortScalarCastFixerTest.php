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

namespace PhpCsFixer\Tests\Fixer\CastNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer
 */
final class ShortScalarCastFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $cases = [];
        foreach (['boolean' => 'bool', 'integer' => 'int', 'double' => 'float', 'real' => 'float'] as $from => $to) {
            $cases[] =
                [
                    sprintf('<?php echo ( %s  )$a;', $to),
                    sprintf('<?php echo ( %s  )$a;', $from),
                ];
            $cases[] =
                [
                    sprintf('<?php $b=(%s) $d;', $to),
                    sprintf('<?php $b=(%s) $d;', $from),
                ];
            $cases[] =
                [
                    sprintf('<?php $b= (%s)$d;', $to),
                    sprintf('<?php $b= (%s)$d;', strtoupper($from)),
                ];
            $cases[] =
                [
                    sprintf('<?php $b=( %s) $d;', $to),
                    sprintf('<?php $b=( %s) $d;', ucfirst($from)),
                ];
            $cases[] =
                [
                    sprintf('<?php $b=(%s ) $d;', $to),
                    sprintf('<?php $b=(%s ) $d;', ucfirst($from)),
                ];
        }

        return $cases;
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideNoFixCases
     */
    public function testNoFix($expected)
    {
        $this->doTest($expected);
    }

    public function provideNoFixCases()
    {
        $cases = [];
        foreach (['string', 'array', 'object', 'unset', 'binary'] as $cast) {
            $cases[] = [sprintf('<?php $b=(%s) $d;', $cast)];
            $cases[] = [sprintf('<?php $b=( %s ) $d;', $cast)];
            $cases[] = [sprintf('<?php $b=(%s ) $d;', ucfirst($cast))];
            $cases[] = [sprintf('<?php $b=(%s ) $d;', strtoupper($cast))];
        }

        return $cases;
    }
}
