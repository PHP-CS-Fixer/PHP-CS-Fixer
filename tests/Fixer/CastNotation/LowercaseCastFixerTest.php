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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class LowercaseCastFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        $cases = array();
        foreach (array('boolean', 'bool', 'integer', 'int', 'double', 'float', 'real', 'float', 'string', 'array', 'object', 'unset', 'binary') as $from) {
            $cases[] =
                array(
                    sprintf('<?php $b= (%s)$d;', $from),
                    sprintf('<?php $b= (%s)$d;', strtoupper($from)),
                );
            $cases[] =
                array(
                    sprintf('<?php $b=( %s) $d;', $from),
                    sprintf('<?php $b=( %s) $d;', ucfirst($from)),
                );
            $cases[] =
                array(
                    sprintf('<?php $b=(%s ) $d;', $from),
                    sprintf('<?php $b=(%s ) $d;', strtoupper($from)),
                );
            $cases[] =
                array(
                    sprintf('<?php $b=(  %s  ) $d;', $from),
                    sprintf('<?php $b=(  %s  ) $d;', ucfirst($from)),
                );
        }

        return $cases;
    }
}
