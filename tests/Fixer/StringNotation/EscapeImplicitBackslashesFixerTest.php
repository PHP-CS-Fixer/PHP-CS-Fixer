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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\EscapeImplicitBackslashesFixer
 */
final class EscapeImplicitBackslashesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases(): array
    {
        return [
            [
                <<<'EOF'
<?php $var = 'String (\\\'\r\n\x0) for My\Prefix\\';
EOF
                ,
            ],
            [
                <<<'EOF'
<?php $var = 'String (\\\'\\r\\n\\x0) for My\\Prefix\\';
EOF
                ,
                <<<'EOF'
<?php $var = 'String (\\\'\r\n\x0) for My\Prefix\\';
EOF
                ,
                ['single_quoted' => true],
            ],
            [
                <<<'EOF'
<?php
$var = "\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z";
$var = "\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z \\' \\8\\9 \\xZ \\u";
$var = "$foo \\A \\a \\' \\8\\9 \\xZ \\u ${bar}";
$var = <<<HEREDOC_SYNTAX
\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z
\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z
\\"
\\'
\\8\\9
\\xZ
\\u
HEREDOC_SYNTAX;
$var = <<<HEREDOC_SYNTAX
$foo \\A \\a \\" \\' \\8\\9 \\xZ \\u ${bar}
HEREDOC_SYNTAX;
$var = <<<'NOWDOC_SYNTAX'
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
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
$var = "\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z";
$var = "\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z \' \8\9 \xZ \u";
$var = "$foo \A \a \' \8\9 \xZ \u ${bar}";
$var = <<<HEREDOC_SYNTAX
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
\"
\'
\8\9
\xZ
\u
HEREDOC_SYNTAX;
$var = <<<HEREDOC_SYNTAX
$foo \A \a \" \' \8\9 \xZ \u ${bar}
HEREDOC_SYNTAX;
$var = <<<'NOWDOC_SYNTAX'
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
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
$var = "$foo \e\f\n\r\t\v \\ \$ \" ${bar}";
$var = <<<HEREDOC_SYNTAX
\e\f\n\r\t\v \\ \$
HEREDOC_SYNTAX;
$var = <<<HEREDOC_SYNTAX
$foo \e\f\n\r\t\v \\ \$ ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = "\0 \00 \000 \0000 \00000";
$var = "$foo \0 \00 \000 \0000 \00000 ${bar}";
$var = <<<HEREDOC_SYNTAX
\0 \00 \000 \0000 \00000
HEREDOC_SYNTAX;
$var = <<<HEREDOC_SYNTAX
$foo \0 \00 \000 \0000 \00000 ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = "\xA \x99 \u{0}";
$var = "$foo \xA \x99 \u{0} ${bar}";
$var = <<<HEREDOC_SYNTAX
\xA \x99 \u{0}
HEREDOC_SYNTAX;
$var = <<<HEREDOC_SYNTAX
$foo \xA \x99 \u{0} ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = 'backslash \\ already escaped';
$var = 'code coverage';
$var = "backslash \\ already escaped";
$var = "code coverage";
$var = <<<HEREDOC_SYNTAX
backslash \\ already escaped
HEREDOC_SYNTAX;
$var = <<<HEREDOC_SYNTAX
code coverage
HEREDOC_SYNTAX;
$var = <<<'NOWDOC_SYNTAX'
backslash \\ already escaped
NOWDOC_SYNTAX;
$var = <<<'NOWDOC_SYNTAX'
code coverage
NOWDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = "\A\a \' \8\9 \xZ \u";
$var = "$foo \A\a \' \8\9 \xZ \u ${bar}";
EOF
                ,
                null,
                ['double_quoted' => false],
            ],
            [
                <<<'EOF'
<?php
$var = <<<HEREDOC_SYNTAX
\A\Z
\a\z
\'
\8\9
\xZ
\u
HEREDOC_SYNTAX;
$var = <<<HEREDOC_SYNTAX
$foo
\A\Z
\a\z
\'
\8\9
\xZ
\u
${bar}
HEREDOC_SYNTAX;

EOF
                ,
                null,
                ['heredoc_syntax' => false],
            ],
            [
                <<<'EOF'
<?php $var = b'String (\\\'\r\n\x0) for My\Prefix\\';
EOF
                ,
            ],
            [
                <<<'EOF'
<?php $var = b'String (\\\'\\r\\n\\x0) for My\\Prefix\\';
EOF
                ,
                <<<'EOF'
<?php $var = b'String (\\\'\r\n\x0) for My\Prefix\\';
EOF
                ,
                ['single_quoted' => true],
            ],
            [
                <<<'EOF'
<?php
$var = b"\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z";
$var = b"\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z \\' \\8\\9 \\xZ \\u";
$var = b"$foo \\A \\a \\' \\8\\9 \\xZ \\u ${bar}";
$var = b<<<HEREDOC_SYNTAX
\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z
\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z
\\"
\\'
\\8\\9
\\xZ
\\u
HEREDOC_SYNTAX;
$var = b<<<HEREDOC_SYNTAX
$foo \\A \\a \\" \\' \\8\\9 \\xZ \\u ${bar}
HEREDOC_SYNTAX;
$var = b<<<'NOWDOC_SYNTAX'
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
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
$var = b"\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z";
$var = b"\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z \' \8\9 \xZ \u";
$var = b"$foo \A \a \' \8\9 \xZ \u ${bar}";
$var = b<<<HEREDOC_SYNTAX
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
\"
\'
\8\9
\xZ
\u
HEREDOC_SYNTAX;
$var = b<<<HEREDOC_SYNTAX
$foo \A \a \" \' \8\9 \xZ \u ${bar}
HEREDOC_SYNTAX;
$var = b<<<'NOWDOC_SYNTAX'
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
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
$var = b"\e\f\n\r\t\v \\ \$ \"";
$var = b"$foo \e\f\n\r\t\v \\ \$ \" ${bar}";
$var = b<<<HEREDOC_SYNTAX
\e\f\n\r\t\v \\ \$
HEREDOC_SYNTAX;
$var = b<<<HEREDOC_SYNTAX
$foo \e\f\n\r\t\v \\ \$ ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = b"\0 \00 \000 \0000 \00000";
$var = b"$foo \0 \00 \000 \0000 \00000 ${bar}";
$var = b<<<HEREDOC_SYNTAX
\0 \00 \000 \0000 \00000
HEREDOC_SYNTAX;
$var = b<<<HEREDOC_SYNTAX
$foo \0 \00 \000 \0000 \00000 ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = b"\xA \x99 \u{0}";
$var = b"$foo \xA \x99 \u{0} ${bar}";
$var = b<<<HEREDOC_SYNTAX
\xA \x99 \u{0}
HEREDOC_SYNTAX;
$var = b<<<HEREDOC_SYNTAX
$foo \xA \x99 \u{0} ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = b'backslash \\ already escaped';
$var = b'code coverage';
$var = b"backslash \\ already escaped";
$var = b"code coverage";
$var = b<<<HEREDOC_SYNTAX
backslash \\ already escaped
HEREDOC_SYNTAX;
$var = b<<<HEREDOC_SYNTAX
code coverage
HEREDOC_SYNTAX;
$var = b<<<'NOWDOC_SYNTAX'
backslash \\ already escaped
NOWDOC_SYNTAX;
$var = b<<<'NOWDOC_SYNTAX'
code coverage
NOWDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = b"\A\a \' \8\9 \xZ \u";
$var = b"$foo \A\a \' \8\9 \xZ \u ${bar}";
EOF
                ,
                null,
                ['double_quoted' => false],
            ],
            [
                <<<'EOF'
<?php
$var = b<<<HEREDOC_SYNTAX
\A\Z
\a\z
\'
\8\9
\xZ
\u
HEREDOC_SYNTAX;
$var = b<<<HEREDOC_SYNTAX
$foo
\A\Z
\a\z
\'
\8\9
\xZ
\u
${bar}
HEREDOC_SYNTAX;

EOF
                ,
                null,
                ['heredoc_syntax' => false],
            ],
            [
                <<<'EOF'
<?php $var = B'String (\\\'\r\n\x0) for My\Prefix\\';
EOF
                ,
            ],
            [
                <<<'EOF'
<?php $var = B'String (\\\'\\r\\n\\x0) for My\\Prefix\\';
EOF
                ,
                <<<'EOF'
<?php $var = B'String (\\\'\r\n\x0) for My\Prefix\\';
EOF
                ,
                ['single_quoted' => true],
            ],
            [
                <<<'EOF'
<?php
$var = B"\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z";
$var = B"\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z \\' \\8\\9 \\xZ \\u";
$var = B"$foo \\A \\a \\' \\8\\9 \\xZ \\u ${bar}";
$var = B<<<HEREDOC_SYNTAX
\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z
\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z
\\"
\\'
\\8\\9
\\xZ
\\u
HEREDOC_SYNTAX;
$var = B<<<HEREDOC_SYNTAX
$foo \\A \\a \\" \\' \\8\\9 \\xZ \\u ${bar}
HEREDOC_SYNTAX;
$var = B<<<'NOWDOC_SYNTAX'
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
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
$var = B"\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z";
$var = B"\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z \' \8\9 \xZ \u";
$var = B"$foo \A \a \' \8\9 \xZ \u ${bar}";
$var = B<<<HEREDOC_SYNTAX
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
\"
\'
\8\9
\xZ
\u
HEREDOC_SYNTAX;
$var = B<<<HEREDOC_SYNTAX
$foo \A \a \" \' \8\9 \xZ \u ${bar}
HEREDOC_SYNTAX;
$var = B<<<'NOWDOC_SYNTAX'
\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
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
$var = B"\e\f\n\r\t\v \\ \$ \"";
$var = B"$foo \e\f\n\r\t\v \\ \$ \" ${bar}";
$var = B<<<HEREDOC_SYNTAX
\e\f\n\r\t\v \\ \$
HEREDOC_SYNTAX;
$var = B<<<HEREDOC_SYNTAX
$foo \e\f\n\r\t\v \\ \$ ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = B"\0 \00 \000 \0000 \00000";
$var = B"$foo \0 \00 \000 \0000 \00000 ${bar}";
$var = B<<<HEREDOC_SYNTAX
\0 \00 \000 \0000 \00000
HEREDOC_SYNTAX;
$var = B<<<HEREDOC_SYNTAX
$foo \0 \00 \000 \0000 \00000 ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = B"\xA \x99 \u{0}";
$var = B"$foo \xA \x99 \u{0} ${bar}";
$var = B<<<HEREDOC_SYNTAX
\xA \x99 \u{0}
HEREDOC_SYNTAX;
$var = B<<<HEREDOC_SYNTAX
$foo \xA \x99 \u{0} ${bar}
HEREDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = B'backslash \\ already escaped';
$var = B'code coverage';
$var = B"backslash \\ already escaped";
$var = B"code coverage";
$var = B<<<HEREDOC_SYNTAX
backslash \\ already escaped
HEREDOC_SYNTAX;
$var = B<<<HEREDOC_SYNTAX
code coverage
HEREDOC_SYNTAX;
$var = B<<<'NOWDOC_SYNTAX'
backslash \\ already escaped
NOWDOC_SYNTAX;
$var = B<<<'NOWDOC_SYNTAX'
code coverage
NOWDOC_SYNTAX;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = B"\A\a \' \8\9 \xZ \u";
$var = B"$foo \A\a \' \8\9 \xZ \u ${bar}";
EOF
                ,
                null,
                ['double_quoted' => false],
            ],
            [
                <<<'EOF'
<?php
$var = B<<<HEREDOC_SYNTAX
\A\Z
\a\z
\'
\8\9
\xZ
\u
HEREDOC_SYNTAX;
$var = B<<<HEREDOC_SYNTAX
$foo
\A\Z
\a\z
\'
\8\9
\xZ
\u
${bar}
HEREDOC_SYNTAX;

EOF
                ,
                null,
                ['heredoc_syntax' => false],
            ],
            [
                <<<'EOF'
<?php
$var = "\\bar";
$var = "\\bar";
$var = "\\\\bar";
$var = "\\\\bar";
$var = "\\\\\\bar";
$var = "\\\\\\bar";
EOF
                ,
                <<<'EOF'
<?php
$var = "\bar";
$var = "\\bar";
$var = "\\\bar";
$var = "\\\\bar";
$var = "\\\\\bar";
$var = "\\\\\\bar";
EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = '\\bar';
$var = '\\bar';
$var = '\\\\bar';
$var = '\\\\bar';
$var = '\\\\\\bar';
$var = '\\\\\\bar';
EOF
                ,
                <<<'EOF'
<?php
$var = '\bar';
$var = '\\bar';
$var = '\\\bar';
$var = '\\\\bar';
$var = '\\\\\bar';
$var = '\\\\\\bar';
EOF
                ,
                ['single_quoted' => true],
            ],
            [
                <<<'EOF'
<?php
$var = <<<TXT
\\bar
\\bar
\\\\bar
\\\\bar
\\\\\\bar
\\\\\\bar
TXT;

EOF
                ,
                <<<'EOF'
<?php
$var = <<<TXT
\bar
\\bar
\\\bar
\\\\bar
\\\\\bar
\\\\\\bar
TXT;

EOF
                ,
            ],
            [
                <<<'EOF'
<?php
$var = <<<'TXT'
\bar
\\bar
\\\bar
\\\\bar
\\\\\bar
\\\\\\bar
TXT;

EOF
                ,
            ],
        ];
    }
}
