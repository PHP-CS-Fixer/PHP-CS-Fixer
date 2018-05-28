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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoEmptyBlockFixer
 */
final class NoEmptyBlockFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            [
                '<?php if ($foo) { echo 1; } ',
                '<?php if ($foo) { echo 1; } while ($foo);',
            ],
            [
                '<?php ',
                '<?php do {} while ($foo);',
            ],
            [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/',
                '<?php do/*1*/{}/*2*/while/*3*/(/*4*/$foo/*5*/)/*6*/;/*7*/',
            ],
            [
                '<?php try { foo(); } ',
                '<?php try { foo(); } finally {}',
            ],
            [
                '<?php try { foo(); } catch (Throwable $e) {} ',
                '<?php try { foo(); } catch (Throwable $e) {} finally {}',
            ],
            [
                '<?php try { foo(); } catch (Throwable $e) {}/*1*//*2*//*3*/',
                '<?php try { foo(); } catch (Throwable $e) {}/*1*/finally/*2*/{}/*3*/',
            ],
            [
                '<?php try { foo(); } catch (Throwable $e) {} catch (Exception $e) {} ',
                '<?php try { foo(); } catch (Throwable $e) {} catch (Exception $e) {} finally {}',
            ],
            [
                '<?php ',
                '<?php try {} catch (Throwable $e) { handle($e); } catch (Exception $e) { handle($e); } finally { echo "hi"; }',
            ],
            [
                '<?php ',
                '<?php try {} catch (Throwable $e) { handle($e); } finally { echo "hi"; }',
            ],
            [
                '<?php ',
                '<?php try {} catch (Throwable $e) { handle($e); }',
            ],
            [
                '<?php ',
                '<?php try {} finally { echo "hi"; }',
            ],
            [
                '<?php ',
                '<?php if ($foo) {}',
            ],
            [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/',
            ],
            [
                '<?php ',
                '<?php if ($foo): endif;',
            ],
            [
                '<?php ',
                '<?php if ($foo): EnDiF;',
            ],
            [
                '<?php ',
                '<?php switch ($foo) {}',
            ],
            [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
                '<?php /*1*/switch/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/',
            ],
            [
                '<?php ',
                '<?php switch ($foo): endswitch;',
            ],
            [
                '<?php ',
                '<?php switch ($foo): eNdSwItCh;',
            ],
            [
                '<?php ',
                '<?php while ($foo) {}',
            ],
            [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
                '<?php /*1*/while/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/',
            ],
            [
                '<?php ',
                '<?php while ($foo);',
            ],
            [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
                '<?php /*1*/while/*2*/(/*3*/$foo/*4*/)/*5*/;/*6*/',
            ],
            [
                '<?php ',
                '<?php while ($foo): endwhile;',
            ],
            [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/',
                '<?php /*1*/while/*2*/(/*3*/$foo/*4*/)/*5*/:endwhile/*6*/;/*7*/',
            ],
            [
                '<?php ',
                '<?php while ($foo): eNdWhIlE;',
            ],
            [
                "<?php\n",
                "<?php\ndo {\n\n}\nwhile (\$foo);",
            ],
            [
                "<?php\ntry {\nfoo();\n} ",
                "<?php\ntry {\nfoo();\n} finally {\n}",
            ],
            [
                "<?php\n",
                "<?php\nif (\$foo) {\n}",
            ],
            [
                "<?php\n",
                "<?php\nswitch (\$foo) {\n}",
            ],
            [
                "<?php\n",
                "<?php\ntry {\n} catch (Throwable \$e) {\n}",
            ],
            [
                "<?php\n",
                "<?php\nwhile (\$foo) {\n}",
            ],
            ['<?php if (foo()) {}'],
            ['<?php if ($foo->bar()) {}'],
            ['<?php if ($foo->bar) {}'],
            ['<?php if ($a = $b) {}'],
            ['<?php if ($a++) {}'],
            ['<?php if (++$a) {}'],
            ['<?php if ($a--) {}'],
            ['<?php if (--$a) {}'],
            ['<?php if ($a .= $b) {}'],
            ['<?php if ($a /= $b) {}'],
            ['<?php if ($a -= $b) {}'],
            ['<?php if ($a %= $b) {}'],
            ['<?php if ($a *= $b) {}'],
            ['<?php if ($a += $b) {}'],
            ['<?php if ($a **= $b) {}'],
            ['<?php if ($a &= $b) {}'],
            ['<?php if ($a |= $b) {}'],
            ['<?php if ($a ^= $b) {}'],
            ['<?php if ($a <<= $b) {}'],
            ['<?php if ($a >>= $b) {}'],
            ['<?php if (require "foo.php") {}'],
            ['<?php if (require_once "foo.php") {}'],
            ['<?php if (include "foo.php") {}'],
            ['<?php if (include_once "foo.php") {}'],
            ['<?php if ($a[$b]) {}'],
            ['<?php do {} while (foo());'],
            ['<?php do {} while ($foo->bar());'],
            ['<?php do {} while ($foo->bar);'],
            ['<?php do { echo 1; } while ($foo);'],
            ['<?php switch (foo()) {}'],
            ['<?php switch ($foo->bar()) {}'],
            ['<?php switch ($foo->bar) {}'],
            ['<?php while (foo()) {}'],
            ['<?php while ($foo->bar()) {}'],
            ['<?php while ($foo->bar) {}'],
            ['<?php while ($foo->bar);'],
            ['<?php if ($foo) { doSomething(); }'],
            ['<?php if ($foo) {} else { doSomething(); }'],
            ['<?php if ($foo) {} elseif ($bar) { doSomething(); }'],
            ['<?php if ($foo) {} elseif ($bar) {} else { doSomething(); }'],
            ['<?php if ($foo) { doSomething(); }'],
            ['<?php if ($foo): else: doSomething(); endif;'],
            ['<?php if ($foo): elseif ($bar): doSomething(); endif;'],
            ['<?php if ($foo): elseif ($bar): else: doSomething(); endif;'],
            ['<?php do { /* keep */ } while ($foo);'],
            ['<?php try { foo(); } finally { /* keep */ }'],
            ['<?php if ($foo) { /* keep */ }'],
            ['<?php switch ($foo) { /* keep */ }'],
            ['<?php try { /* keep */ } catch (Throwable $e) {}'],
            ['<?php while ($foo) { /* keep */ }'],
            ['<?php do { /** keep */ } while ($foo);'],
            ['<?php try { foo(); } finally { /** keep */ }'],
            ['<?php if ($foo) { /** keep */ }'],
            ['<?php switch ($foo) { /** keep */ }'],
            ['<?php try { /** keep */ } catch (Throwable $e) {}'],
            ['<?php while ($foo) { /** keep */ }'],
        ];
    }
}
