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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer
 */
final class NoAliasFunctionsFixerTest extends AbstractFixerTestCase
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
        /** @var $aliases string[] */
        $aliases = static::getStaticAttribute(\PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer::class, 'aliases');

        $cases = [];
        foreach ($aliases as $alias => $master) {
            // valid cases
            $cases[] = ["<?php \$smth->${alias}(\$a);"];
            $cases[] = ["<?php {$alias}Smth(\$a);"];
            $cases[] = ["<?php smth_${alias}(\$a);"];
            $cases[] = ["<?php new ${alias}(\$a);"];
            $cases[] = ["<?php new Smth\\${alias}(\$a);"];
            $cases[] = ["<?php Smth\\${alias}(\$a);"];
            $cases[] = ["<?php namespace\\${alias}(\$a);"];
            $cases[] = ["<?php Smth::${alias}(\$a);"];
            $cases[] = ["<?php new ${alias}\\smth(\$a);"];
            $cases[] = ["<?php ${alias}::smth(\$a);"];
            $cases[] = ["<?php ${alias}\\smth(\$a);"];
            $cases[] = ['<?php "SELECT ... '.$alias.'(\$a) ...";'];
            $cases[] = ['<?php "SELECT ... '.strtoupper($alias).'($a) ...";'];
            $cases[] = ["<?php 'test'.'${alias}' . 'in concatenation';"];
            $cases[] = ['<?php "test" . "'.$alias.'"."in concatenation";'];
            $cases[] = [
                '<?php
class '.ucfirst($alias).'ing
{
    const '.$alias.' = 1;

    public function '.$alias.'($'.$alias.')
    {
        if (defined("'.$alias.'") || $'.$alias.' instanceof '.$alias.') {
            echo '.$alias.';
        }
    }
}

class '.$alias.' extends '.ucfirst($alias).'ing{
    const '.$alias.' = "'.$alias.'";
}
',
            ];

            // cases to be fixed
            $cases[] = [
                "<?php ${master}(\$a);",
                "<?php ${alias}(\$a);",
            ];
            $cases[] = [
                "<?php \\${master}(\$a);",
                "<?php \\${alias}(\$a);",
            ];
            $cases[] = [
                "<?php \$ref = &${master}(\$a);",
                "<?php \$ref = &${alias}(\$a);",
            ];
            $cases[] = [
                "<?php \$ref = &\\${master}(\$a);",
                "<?php \$ref = &\\${alias}(\$a);",
            ];
            $cases[] = [
                "<?php ${master}
                            (\$a);",
                "<?php ${alias}
                            (\$a);",
            ];
            $cases[] = [
                "<?php /* foo */ ${master} /** bar */ (\$a);",
                "<?php /* foo */ ${alias} /** bar */ (\$a);",
            ];
            $cases[] = [
                "<?php a(${master}());",
                "<?php a(${alias}());",
            ];
            $cases[] = [
                "<?php a(\\${master}());",
                "<?php a(\\${alias}());",
            ];
        }

        // static case to fix - in case previous generation is broken
        $cases[] = [
            '<?php is_int($a);',
            '<?php is_integer($a);',
        ];

        $cases[] = [
            '<?php $b=is_int(count(implode($b,$a)));',
            '<?php $b=is_integer(sizeof(join($b,$a)));',
        ];

        $cases[] = [
            '<?php
interface JoinInterface
{
    public function &join();
}

abstract class A
{
    abstract public function join($a);

    public function is_integer($a)
    {
        $fputs = "is_double(\$a);\n"; // key_exists($b, $c);
        echo $fputs."\$is_writable";
        \B::close();
        Scope\is_long();
        namespace\is_long();
        $a->pos();
        new join();
        new \join();
        new ScopeB\join(mt_rand(0, 100));
    }
}',
        ];

        return $cases;
    }
}
