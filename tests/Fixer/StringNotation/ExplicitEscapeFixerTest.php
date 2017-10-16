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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\ExplicitEscapeFixer
 */
final class ExplicitEscapeFixerTest extends AbstractFixerTestCase
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
<<<'EOF'
<?php $var = 'String (\\\'\\r\\n\\x0) for My\\Prefix\\';
EOF
,
<<<'EOF'
<?php $var = 'String (\\\'\r\n\x0) for My\Prefix\\';
EOF
,
            ],
            [
<<<'EOF'
<?php
$var = "\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z \\' \\8\\9 \\xZ \\u";
$var = <<<HEREDOC_SYNTAX
\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z
\\'
\\8\\9
\\xZ
\\u
HEREDOC_SYNTAX;
$var = <<<'NOWDOC_SYNTAX'
\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
\'
\8\9
\xZ
\u
NOWDOC_SYNTAX;

EOF
,
<<<'EOF'
<?php
$var = "\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z \' \8\9 \xZ \u";
$var = <<<HEREDOC_SYNTAX
\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
\'
\8\9
\xZ
\u
HEREDOC_SYNTAX;
$var = <<<'NOWDOC_SYNTAX'
\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
\'
\8\9
\xZ
\u
NOWDOC_SYNTAX;

EOF
,
            ],
            [
<<<'EOF'
<?php
$var = "\e\f\n\r\t\v \\ \$ \"";
$var = <<<HEREDOC_SYNTAX
\e\f\n\r\t\v \\ \$ \"
HEREDOC_SYNTAX;

EOF
,
            ],
            [
<<<'EOF'
<?php
$var = "\0 \00 \000 \0000 \00000";
$var = <<<HEREDOC_SYNTAX
\0 \00 \000 \0000 \00000
HEREDOC_SYNTAX;

EOF
,
            ],
            [
<<<'EOF'
<?php
$var = "\xA \x99 \u{0}";
$var = <<<HEREDOC_SYNTAX
\xA \x99 \u{0}
HEREDOC_SYNTAX;

EOF
,
            ],
            [
<<<'EOF'
<?php
$var = 'backslash \\ alread escaped';
$var = 'code coverage';
$var = "backslash \\ alread escaped";
$var = "code coverage";
$var = <<<HEREDOC_SYNTAX
backslash \\ alread escaped
HEREDOC_SYNTAX;
$var = <<<HEREDOC_SYNTAX
code coverage
HEREDOC_SYNTAX;
$var = <<<'NOWDOC_SYNTAX'
backslash \\ alread escaped
NOWDOC_SYNTAX;
$var = <<<'NOWDOC_SYNTAX'
code coverage
NOWDOC_SYNTAX;

EOF
,
            ],
        ];
    }
}
