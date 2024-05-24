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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Jean LECORDIER <jean.lecordier@infoclimat.fr>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\StringConcatenationToInterpolationFixer
 */
final class StringConcatenationToInterpolationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield ['<?php $str = \'str\';'];
        yield ['<?php $str = "str";'];
        yield ['<?php $str = "left " . date("c");'];
        yield ['<?php $str = date("c") . " right";'];
        yield ['<?php $str = "left " . date("c") . " right";'];
        yield ['<?php $str = "left " . M_PI;'];
        yield ['<?php $str = M_PI . " right";'];
        yield ['<?php $str = "left " . M_PI . " right";'];
        yield ['<?php $str = "left " . __DIR__;'];
        yield ['<?php $str = __DIR__ . " right";'];
        yield ['<?php $str = "left " . __DIR__ . " right";'];
        yield ['<?php $str = "left {$var}";'];
        yield ['<?php $str = "{$var} right";'];
        yield ['<?php $str = "left {$var} right";'];
        yield ['<?php $str = "left {$tab[0]}";'];
        yield ['<?php $str = "{$tab[0]} right";'];
        yield ['<?php $str = "left {$tab[0]} right";'];
        yield [<<<'PHP'
            <?php $str = "left {$tab['key']}";
            PHP];
        yield [<<<'PHP'
            <?php $str = "{$tab['key']} right";
            PHP];
        yield [<<<'PHP'
            <?php $str = "left {$tab['key']} right";
            PHP];
        yield [
            '<?php $str = "{$var}{$var}";',
            '<?php $str = $var . $var;',
        ];
        yield [
            '<?php $str = $var;',
            '<?php $str = "" . $var;',
        ];
        yield [
            '<?php $str = $var;',
            '<?php $str = $var . "";',
        ];
        yield [
            '<?php $str = $var;',
            '<?php $str = \'\' . $var;',
        ];
        yield [
            '<?php $str = $var;',
            '<?php $str = $var . \'\';',
        ];
        yield [
            '<?php $str = "left {$var}";',
            '<?php $str = "left " . $var;',
        ];
        yield [
            '<?php $str = "{$var} right";',
            '<?php $str = $var . " right";',
        ];
        yield [
            '<?php $str = "left {$var} {$var}";',
            '<?php $str = "left {$var} " . $var;',
        ];
        yield [
            '<?php $str = "{$var} {$var} right";',
            '<?php $str = $var . " {$var} right";',
        ];
        yield [
            '<?php $str = "left {$var} right";',
            '<?php $str = "left {$var}" . " right";',
        ];
        yield [
            '<?php $str = "left {$var} right";',
            '<?php $str = "left " . "{$var} right";',
        ];
        yield [
            '<?php $str = "left {$var} right";',
            '<?php $str = "left " . $var . " right";',
        ];
        yield [
            '<?php $str = $var;',
            '<?php $str = "" . $var . "";',
        ];
        yield [
            '<?php $str = "{$var}{$var}";',
            '<?php $str = $var . "" . $var;',
        ];
        yield [
            '<?php $str = "left {$tab[0]}";',
            '<?php $str = "left " . $tab[0];',
        ];
        yield [
            '<?php $str = "{$tab[0]} right";',
            '<?php $str = $tab[0] . " right";',
        ];
        yield [
            '<?php $str = "left {$tab[0]} right";',
            '<?php $str = "left " . $tab[0] . " right";',
        ];
        yield [
            '<?php $str = "left {$tab[0][0]}";',
            '<?php $str = "left " . $tab[0][0];',
        ];
        yield [
            '<?php $str = "{$tab[0][0]} right";',
            '<?php $str = $tab[0][0] . " right";',
        ];
        yield [
            '<?php $str = "left {$tab[0][0]} right";',
            '<?php $str = "left " . $tab[0][0] . " right";',
        ];
        yield [
            '<?php $str = "{$tab[0]} {$tab[0]}";',
            '<?php $str = "{$tab[0]} " . $tab[0];',
        ];
        yield [
            '<?php $str = "{$tab[0]} {$tab[0]}";',
            '<?php $str = $tab[0] . " {$tab[0]}";',
        ];
        yield [
            '<?php $str = "{$tab[0]} {$tab[0]} {$tab[0]}";',
            '<?php $str = "{$tab[0]} " . $tab[0] . " {$tab[0]}";',
        ];
        yield [
            '<?php $str = "{$tab[0][0]} {$tab[0][0]}";',
            '<?php $str = "{$tab[0][0]} " . $tab[0][0];',
        ];
        yield [
            '<?php $str = "{$tab[0][0]} {$tab[0][0]}";',
            '<?php $str = $tab[0][0] . " {$tab[0][0]}";',
        ];
        yield [
            '<?php $str = "{$tab[0][0]} {$tab[0][0]} {$tab[0][0]}";',
            '<?php $str = "{$tab[0][0]} " . $tab[0][0] . " {$tab[0][0]}";',
        ];
        yield [
            '<?php $str = "left {$obj->prop}";',
            '<?php $str = "left " . $obj->prop;',
        ];
        yield [
            '<?php $str = "{$obj->prop} right";',
            '<?php $str = $obj->prop . " right";',
        ];
        yield [
            '<?php $str = "left {$obj->prop} right";',
            '<?php $str = "left " . $obj->prop . " right";',
        ];
        yield [
            '<?php $str = "left {$obj->method()}";',
            '<?php $str = "left " . $obj->method();',
        ];
        yield [
            '<?php $str = "{$obj->method()} right";',
            '<?php $str = $obj->method() . " right";',
        ];
        yield [
            '<?php $str = "left {$obj->method()} right";',
            '<?php $str = "left " . $obj->method() . " right";',
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))}";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'));
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "{$obj->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))} right";
                PHP,
            <<<'PHP'
                <?php $str = $obj->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab')) . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))} right";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab')) . " right";
                PHP,
        ];
        yield [
            '<?php $str = "left {$obj->tabi[0]}";',
            '<?php $str = "left " . $obj->tabi[0];',
        ];
        yield [
            '<?php $str = "{$obj->tabi[0]} right";',
            '<?php $str = $obj->tabi[0] . " right";',
        ];
        yield [
            '<?php $str = "left {$obj->tabi[0]} right";',
            '<?php $str = "left " . $obj->tabi[0] . " right";',
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->prop->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop}";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->prop->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop;
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "{$obj->prop->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop} right";
                PHP,
            <<<'PHP'
                <?php $str = $obj->prop->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->prop->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop} right";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->prop->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$tab['key']}";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $tab['key'];
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "{$tab['key']} right";
                PHP,
            <<<'PHP'
                <?php $str = $tab['key'] . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$tab['key']} right";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $tab['key'] . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->tabk['key']}";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->tabk['key'];
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "{$obj->tabk['key']} right";
                PHP,
            <<<'PHP'
                <?php $str = $obj->tabk['key'] . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->tabk['key']} right";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->tabk['key'] . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]}";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()];
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "{$obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]} right";
                PHP,
            <<<'PHP'
                <?php $str = $obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()] . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]} right";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()] . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop}";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop;
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "{$obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop} right";
                PHP,
            <<<'PHP'
                <?php $str = $obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop . " right";
                PHP,
        ];
        yield [
            <<<'PHP'
                <?php $str = "left {$obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop} right";
                PHP,
            <<<'PHP'
                <?php $str = "left " . $obj->tab[$arg][$tabi[0]][$tabk['key']][my_function()]->prop->method()->method('arg', $arg, $tab[0], $obj->prop, $obj->method(), my_function(), M_PI, new MyClass(), ($exp), ['tab'], array('tab'))->tabi[0]->tabi[0][Ø]->method()->prop . " right";
                PHP,
        ];
    }
}
