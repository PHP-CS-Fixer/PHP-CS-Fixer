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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\SimplifiedIfReturnFixer
 */
final class SimplifiedIfReturnFixerTest extends AbstractFixerTestCase
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
        return [
            'simple' => [
                '<?php return (bool) ($foo)      ;',
                '<?php if ($foo) { return true; } return false;',
            ],
            'simple-negative' => [
                '<?php return ! ($foo)      ;',
                '<?php if ($foo) { return false; } return true;',
            ],
            'simple-negative II' => [
                '<?php return ! (!$foo && $a())      ;',
                '<?php if (!$foo && $a()) { return false; } return true;',
            ],
            'simple-braceless' => [
                '<?php return (bool) ($foo)    ;',
                '<?php if ($foo) return true; return false;',
            ],
            'simple-braceless-negative' => [
                '<?php return ! ($foo)    ;',
                '<?php if ($foo) return false; return true;',
            ],
            'bug-consecutive-ifs' => [
                '<?php if ($bar) { return 1; } return (bool) ($foo)      ;',
                '<?php if ($bar) { return 1; } if ($foo) { return true; } return false;',
            ],
            'bug-consecutive-ifs-negative' => [
                '<?php if ($bar) { return 1; } return ! ($foo)      ;',
                '<?php if ($bar) { return 1; } if ($foo) { return false; } return true;',
            ],
            'bug-consecutive-ifs-braceless' => [
                '<?php if ($bar) return 1; return (bool) ($foo)    ;',
                '<?php if ($bar) return 1; if ($foo) return true; return false;',
            ],
            'bug-consecutive-ifs-braceless-negative' => [
                '<?php if ($bar) return 1; return ! ($foo)    ;',
                '<?php if ($bar) return 1; if ($foo) return false; return true;',
            ],
            [
                <<<'EOT'
<?php
function f1() { return (bool) ($f1)      ; }
function f2() { return true; } return false;
function f3() { return (bool) ($f3)      ; }
function f4() { return true; } return false;
function f5() { return (bool) ($f5)      ; }
function f6() { return false; } return true;
function f7() { return ! ($f7)      ; }
function f8() { return false; } return true;
function f9() { return ! ($f9)      ; }
EOT
                ,
                <<<'EOT'
<?php
function f1() { if ($f1) { return true; } return false; }
function f2() { return true; } return false;
function f3() { if ($f3) { return true; } return false; }
function f4() { return true; } return false;
function f5() { if ($f5) { return true; } return false; }
function f6() { return false; } return true;
function f7() { if ($f7) { return false; } return true; }
function f8() { return false; } return true;
function f9() { if ($f9) { return false; } return true; }
EOT
                ,
            ],
            'preserve-comments' => [
                <<<'EOT'
<?php
// C1
return (bool)
# C2
(
/* C3 */
$foo
/** C4 */
)
// C5

# C6

// C7

# C8

/* C9 */

/** C10 */

// C11

# C12
;
/* C13 */
EOT
                ,
                <<<'EOT'
<?php
// C1
if
# C2
(
/* C3 */
$foo
/** C4 */
)
// C5
{
# C6
return
// C7
true
# C8
;
/* C9 */
}
/** C10 */
return
// C11
false
# C12
;
/* C13 */
EOT
                ,
            ],
            'preserve-comments-braceless' => [
                <<<'EOT'
<?php
// C1
return (bool)
# C2
(
/* C3 */
$foo
/** C4 */
)
// C5
# C6

// C7

# C8

/* C9 */
/** C10 */

// C11

# C12
;
/* C13 */
EOT
                ,
                <<<'EOT'
<?php
// C1
if
# C2
(
/* C3 */
$foo
/** C4 */
)
// C5
# C6
return
// C7
true
# C8
;
/* C9 */
/** C10 */
return
// C11
false
# C12
;
/* C13 */
EOT
                ,
            ],
            'else-if' => [
                '<?php if ($bar) { return $bar; } else return (bool) ($foo)      ;',
                '<?php if ($bar) { return $bar; } else if ($foo) { return true; } return false;',
            ],
            'else-if-negative' => [
                '<?php if ($bar) { return $bar; } else return ! ($foo)      ;',
                '<?php if ($bar) { return $bar; } else if ($foo) { return false; } return true;',
            ],
            'else-if-braceless' => [
                '<?php if ($bar) return $bar; else return (bool) ($foo)    ;',
                '<?php if ($bar) return $bar; else if ($foo) return true; return false;',
            ],
            'else-if-braceless-negative' => [
                '<?php if ($bar) return $bar; else return ! ($foo)    ;',
                '<?php if ($bar) return $bar; else if ($foo) return false; return true;',
            ],
            'elseif' => [
                '<?php if ($bar) { return $bar; } return (bool) ($foo)      ;',
                '<?php if ($bar) { return $bar; } elseif ($foo) { return true; } return false;',
            ],
            'elseif-negative' => [
                '<?php if ($bar) { return $bar; } return ! ($foo)      ;',
                '<?php if ($bar) { return $bar; } elseif ($foo) { return false; } return true;',
            ],
            'elseif-braceless' => [
                '<?php if ($bar) return $bar; return (bool) ($foo)    ;',
                '<?php if ($bar) return $bar; elseif ($foo) return true; return false;',
            ],
            'elseif-braceless-negative' => [
                '<?php if ($bar) return $bar; return ! ($foo)    ;',
                '<?php if ($bar) return $bar; elseif ($foo) return false; return true;',
            ],
        ];
    }
}
