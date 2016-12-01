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

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class CombineConsecutiveIssetsFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
                $exists = isset($foo);
                ',
            ),
            array(
                '<?php
                $exists = isset($foo, $bar, $thing);
                ',
                '<?php
                $exists = isset($foo) && isset($bar) && isset($thing);
                ',
            ),
            array(
                '<?php
                $exists = isset($foo, $data[isset($unrelated) ? "key" : "other_key"], $thing);
                ',
                '<?php
                $exists = isset($foo) && isset($data[isset($unrelated) ? "key" : "other_key"]) && isset($thing);
                ',
            ),
            array(
                '<?php
                if (isset($foo, $bar, $thing)) {}
                ',
                '<?php
                if (isset($foo) && isset($bar) && isset($thing)) {}
                ',
            ),
            array(
                '<?php
                $exists = isset($foo) && true || isset($bar, $thing, $other);
                ',
                '<?php
                $exists = isset($foo) && true || isset($bar) && isset($thing) && isset($other);
                ',
            ),
        );
    }
}
