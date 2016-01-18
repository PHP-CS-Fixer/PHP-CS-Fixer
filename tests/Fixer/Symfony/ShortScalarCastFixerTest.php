<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class ShortScalarCastFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $cases = array();
        foreach (array('boolean' => 'bool', 'integer' => 'int', 'double' => 'float', 'real' => 'float') as $from => $to) {
            $cases[] =
                array(
                    sprintf('<?php echo ( %s  )$a;', $to),
                    sprintf('<?php echo ( %s  )$a;', $from),
                );
            $cases[] =
                array(
                    sprintf('<?php $b=(%s) $d;', $to),
                    sprintf('<?php $b=(%s) $d;', $from),
                );
            $cases[] =
                array(
                    sprintf('<?php $b= (%s)$d;', $to),
                    sprintf('<?php $b= (%s)$d;', strtoupper($from)),
                );
            $cases[] =
                array(
                    sprintf('<?php $b=( %s) $d;', $to),
                    sprintf('<?php $b=( %s) $d;', ucfirst($from)),
                );
            $cases[] =
                array(
                    sprintf('<?php $b=(%s ) $d;', $to),
                    sprintf('<?php $b=(%s ) $d;', ucfirst($from)),
                );
        }

        return $cases;
    }

    /**
     * @dataProvider provideNoFixCases
     */
    public function testNoFix($expected)
    {
        $this->doTest($expected);
    }

    public function provideNoFixCases()
    {
        $cases = array();
        foreach (array('string', 'array', 'object', 'unset', 'binary') as $cast) {
            $cases[] = array(sprintf('<?php $b=(%s) $d;', $cast));
            $cases[] = array(sprintf('<?php $b=( %s ) $d;', $cast));
            $cases[] = array(sprintf('<?php $b=(%s ) $d;', ucfirst($cast)));
            $cases[] = array(sprintf('<?php $b=(%s ) $d;', strtoupper($cast)));
        }

        return $cases;
    }
}
