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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class RandomApiMigrationFixerTest extends AbstractFixerTestBase
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
        /** @var $replacements string[] */
        $replacements = static::getStaticAttribute('\Symfony\CS\Fixer\Contrib\RandomApiMigrationFixer', 'replacements');

        $cases = array();
        foreach ($replacements as $older => $newer) {
            // valid cases
            $cases[] = array("<?php \$smth->$older(\$a);");
            $cases[] = array("<?php {$older}Smth(\$a);");
            $cases[] = array("<?php smth_$older(\$a);");
            $cases[] = array("<?php new $older(\$a);");
            $cases[] = array("<?php new Smth\\$older(\$a);");
            $cases[] = array("<?php Smth\\$older(\$a);");
            $cases[] = array("<?php namespace\\$older(\$a);");
            $cases[] = array("<?php Smth::$older(\$a);");
            $cases[] = array("<?php new $older\\smth(\$a);");
            $cases[] = array("<?php $older::smth(\$a);");
            $cases[] = array("<?php $older\\smth(\$a);");
            $cases[] = array('<?php "SELECT ... '.$older.'(\$a) ...";');
            $cases[] = array('<?php "SELECT ... '.strtoupper($older).'($a) ...";');
            $cases[] = array("<?php 'test'.'$older' . 'in concatenation';");
            $cases[] = array('<?php "test" . "'.$older.'"."in concatenation";');
            $cases[] = array(
                '<?php
class '.ucfirst($older).'ing
{
    public function '.$older.'($'.$older.')
    {
        if (!defined("'.$older.'") || $'.$older.' instanceof '.$older.') {
            const '.$older.' = 1;
        }
        echo '.$older.';
    }
}

class '.$older.' extends '.ucfirst($older).'ing{
    const '.$older.' = "'.$older.'"
}
',
            );

            // cases to be fixed
            $cases[] = array(
                "<?php $newer(\$a);",
                "<?php $older(\$a);",
            );
            $cases[] = array(
                "<?php \\$newer(\$a);",
                "<?php \\$older(\$a);",
            );
            $cases[] = array(
                "<?php \$a = &$newer(\$a);",
                "<?php \$a = &$older(\$a);",
            );
            $cases[] = array(
                "<?php \$a = &\\$newer(\$a);",
                "<?php \$a = &\\$older(\$a);",
            );
            $cases[] = array(
                "<?php $newer
                            (\$a);",
                "<?php $older
                            (\$a);",
            );
            $cases[] = array(
                "<?php /* foo */ $newer /** bar */ (\$a);",
                "<?php /* foo */ $older /** bar */ (\$a);",
            );
            $cases[] = array(
                "<?php a($newer());",
                "<?php a($older());",
            );
            $cases[] = array(
                "<?php a(\\$newer());",
                "<?php a(\\$older());",
            );
        }

        // static case to fix - in case previous generation is broken
        $cases[] = array(
            '<?php mt_rand($a);',
            '<?php rand($a);',
        );

        return $cases;
    }
}
