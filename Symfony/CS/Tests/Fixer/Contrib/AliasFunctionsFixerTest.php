<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * Fixes AliasFunctionsUsageInspection warnings from Php Inspections (EA Extended)
 */
class AliasFunctionsFixerTest extends AbstractFixerTestBase
{
    /**
     * @var array|string[]
     */
    private static $aliases = array(
        'is_double' => 'is_float',
        'is_integer' => 'is_int',
        'is_long' => 'is_int',
        'is_real' => 'is_float',
        'sizeof' => 'count',
        'doubleval' => 'floatval',
        'fputs' => 'fwrite',
        'join' => 'implode',
        'key_exists' => 'array_key_exists',

        'chop' => 'rtrim',
        'close' => 'closedir',
        'ini_alter' => 'ini_set',
        'is_writeable' => 'is_writable',
        'magic_quotes_runtime' => 'set_magic_quotes_runtime',
        'pos' => 'current',
        'rewind' => 'rewinddir',
        'show_source' => 'highlight_file',
        'strchr' => 'strstr',
        );

    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        $validCases = array();
        $fixCases = array();
        foreach (static::$aliases as $alias => $master) {
            $validCases[] = array('<?php $smth->'.$alias.'($a);');
            $validCases[] = array('<?php '.$alias.'Smth($a);');
            $validCases[] = array('<?php smth_'.$alias.'($a);');
            $validCases[] = array('<?php new '.$alias.'($a);');
            $validCases[] = array('<?php Smth::'.$alias.'($a);');
            $validCases[] = array('<?php new '.$alias.'\smth($a);');
            $validCases[] = array('<?php '.$alias.'::smth($a);');
            $validCases[] = array('<?php '.$alias.'\smth($a);');
            $validCases[] = array('<?php "SELECT ... '.$alias.'($a) ...";');
            $validCases[] = array('<?php "SELECT ... '.strtoupper($alias).'($a) ...";');
            $validCases[] = array("<?php 'test'.'".$alias."' . 'in concatenation';");
            $validCases[] = array('<?php "test" . "'.$alias.'"."in concatenation";');
            $validCases[] = array(
                '<?php
class '.ucfirst($alias).'ing
{
    public function '.$alias.'($'.$alias.')
    {
        //expressions here
    }
}',
            );

            $fixCases[] = array(
                '<?php '.$master.'($a);',
                '<?php '.$alias.'($a);',
            );
            $fixCases[] = array(
                '<?php $a = &'.$master.'($a);',
                '<?php $a = &'.$alias.'($a);',
            );
            $fixCases[] = array(
                '<?php '.$master.'
                            ($a);',
                '<?php '.$alias.'
                            ($a);',
            );
            $fixCases[] = array(
                '<?php /* foo */ '.$master.' /** bar */ ($a);',
                '<?php /* foo */ '.$alias.' /** bar */ ($a);',
            );
            $fixCases[] = array(
                '<?php a('.$master.'());',
                '<?php a('.$alias.'());',
            );
        }

        return array_merge($validCases, $fixCases);
    }
}
