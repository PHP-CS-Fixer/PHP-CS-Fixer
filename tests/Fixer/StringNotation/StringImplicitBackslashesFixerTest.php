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
 * @author Michael Vorisek <https://github.com/mvorisek>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\StringImplicitBackslashesFixer
 */
final class StringImplicitBackslashesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int|string, array{string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php $var = 'String (\\\'\r\n\x0\) for My\Prefix\\';
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php $var = 'String (\\\'\\r\\n\\x0\\) for My\\Prefix\\';
                EOD,
            <<<'EOD'
                <?php $var = 'String (\\\'\r\n\x0\) for My\Prefix\\';
                EOD,
            ['single_quoted' => 'escape'],
        ];

        yield [
            <<<'EOD'
                <?php
                $var = "\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z";
                $var = "\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z \\' \\8\\9 \\xZ \\u";
                $var = "$foo \\A \\a \\' \\8\\9 \\xZ \\u ${bar}";
                $var = <<<HEREDOC
                \\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z
                \\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z
                \\"
                \\'
                \\8\\9
                \\xZ
                \\u
                HEREDOC;
                $var = <<<HEREDOC
                $foo \\A \\a \\" \\' \\8\\9 \\xZ \\u ${bar}
                HEREDOC;
                $var = <<<'NOWDOC'
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \'
                \8\9
                \xZ
                \u
                NOWDOC;

                EOD,
            <<<'EOD'
                <?php
                $var = "\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z";
                $var = "\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z \' \8\9 \xZ \u";
                $var = "$foo \A \a \' \8\9 \xZ \u ${bar}";
                $var = <<<HEREDOC
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \"
                \'
                \8\9
                \xZ
                \u
                HEREDOC;
                $var = <<<HEREDOC
                $foo \A \a \" \' \8\9 \xZ \u ${bar}
                HEREDOC;
                $var = <<<'NOWDOC'
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \'
                \8\9
                \xZ
                \u
                NOWDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = "\e\f\n\r\t\v \\ \$ \"";
                $var = "$foo \e\f\n\r\t\v \\ \$ \" ${bar}";
                $var = <<<HEREDOC
                \e\f\n\r\t\v \\ \$
                HEREDOC;
                $var = <<<HEREDOC
                $foo \e\f\n\r\t\v \\ \$ ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = "\0 \00 \000 \0000 \00000";
                $var = "$foo \0 \00 \000 \0000 \00000 ${bar}";
                $var = <<<HEREDOC
                \0 \00 \000 \0000 \00000
                HEREDOC;
                $var = <<<HEREDOC
                $foo \0 \00 \000 \0000 \00000 ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = "\xA \x99 \u{0}";
                $var = "$foo \xA \x99 \u{0} ${bar}";
                $var = <<<HEREDOC
                \xA \x99 \u{0}
                HEREDOC;
                $var = <<<HEREDOC
                $foo \xA \x99 \u{0} ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = 'backslash \ not escaped';
                $var = 'code coverage';
                $var = "backslash \\ already escaped";
                $var = "code coverage";
                $var = <<<HEREDOC
                backslash \\ already escaped
                HEREDOC;
                $var = <<<HEREDOC
                code coverage
                HEREDOC;
                $var = <<<'NOWDOC'
                backslash \\ already escaped
                NOWDOC;
                $var = <<<'NOWDOC'
                code coverage
                NOWDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = "\A\a \' \8\9 \xZ \u";
                $var = "$foo \A\a \' \8\9 \xZ \u ${bar}";
                EOD,
            null,
            ['double_quoted' => 'unescape'],
        ];

        yield [
            <<<'EOD'
                <?php
                $var = <<<HEREDOC
                \A\Z
                \a\z
                \'
                \8\9
                \xZ
                \u
                HEREDOC;
                $var = <<<HEREDOC
                $foo
                \A\Z
                \a\z
                \'
                \8\9
                \xZ
                \u
                ${bar}
                HEREDOC;

                EOD,
            null,
            ['heredoc' => 'unescape'],
        ];

        yield [
            <<<'EOD'
                <?php $var = b'String (\\\'\r\n\x0) for My\Prefix\\';
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php $var = b'String (\\\'\\r\\n\\x0) for My\\Prefix\\';
                EOD,
            <<<'EOD'
                <?php $var = b'String (\\\'\r\n\x0) for My\Prefix\\';
                EOD,
            ['single_quoted' => 'escape'],
        ];

        yield [
            <<<'EOD'
                <?php
                $var = b"\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z";
                $var = b"\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z \\' \\8\\9 \\xZ \\u";
                $var = b"$foo \\A \\a \\' \\8\\9 \\xZ \\u ${bar}";
                $var = b<<<HEREDOC
                \\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z
                \\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z
                \\"
                \\'
                \\8\\9
                \\xZ
                \\u
                HEREDOC;
                $var = b<<<HEREDOC
                $foo \\A \\a \\" \\' \\8\\9 \\xZ \\u ${bar}
                HEREDOC;
                $var = b<<<'NOWDOC'
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \'
                \8\9
                \xZ
                \u
                NOWDOC;

                EOD,
            <<<'EOD'
                <?php
                $var = b"\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z";
                $var = b"\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z \' \8\9 \xZ \u";
                $var = b"$foo \A \a \' \8\9 \xZ \u ${bar}";
                $var = b<<<HEREDOC
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \"
                \'
                \8\9
                \xZ
                \u
                HEREDOC;
                $var = b<<<HEREDOC
                $foo \A \a \" \' \8\9 \xZ \u ${bar}
                HEREDOC;
                $var = b<<<'NOWDOC'
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \'
                \8\9
                \xZ
                \u
                NOWDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = b"\e\f\n\r\t\v \\ \$ \"";
                $var = b"$foo \e\f\n\r\t\v \\ \$ \" ${bar}";
                $var = b<<<HEREDOC
                \e\f\n\r\t\v \\ \$
                HEREDOC;
                $var = b<<<HEREDOC
                $foo \e\f\n\r\t\v \\ \$ ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = b"\0 \00 \000 \0000 \00000";
                $var = b"$foo \0 \00 \000 \0000 \00000 ${bar}";
                $var = b<<<HEREDOC
                \0 \00 \000 \0000 \00000
                HEREDOC;
                $var = b<<<HEREDOC
                $foo \0 \00 \000 \0000 \00000 ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = b"\xA \x99 \u{0}";
                $var = b"$foo \xA \x99 \u{0} ${bar}";
                $var = b<<<HEREDOC
                \xA \x99 \u{0}
                HEREDOC;
                $var = b<<<HEREDOC
                $foo \xA \x99 \u{0} ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = b'backslash \ not escaped';
                $var = b'code coverage';
                $var = b"backslash \\ already escaped";
                $var = b"code coverage";
                $var = b<<<HEREDOC
                backslash \\ already escaped
                HEREDOC;
                $var = b<<<HEREDOC
                code coverage
                HEREDOC;
                $var = b<<<'NOWDOC'
                backslash \\ already escaped
                NOWDOC;
                $var = b<<<'NOWDOC'
                code coverage
                NOWDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = b"\A\a \' \8\9 \xZ \u";
                $var = b"$foo \A\a \' \8\9 \xZ \u ${bar}";
                EOD,
            null,
            ['double_quoted' => 'unescape'],
        ];

        yield [
            <<<'EOD'
                <?php
                $var = b<<<HEREDOC
                \A\Z
                \a\z
                \'
                \8\9
                \xZ
                \u
                HEREDOC;
                $var = b<<<HEREDOC
                $foo
                \A\Z
                \a\z
                \'
                \8\9
                \xZ
                \u
                ${bar}
                HEREDOC;

                EOD,
            null,
            ['heredoc' => 'unescape'],
        ];

        yield [
            <<<'EOD'
                <?php $var = B'String (\\\'\r\n\x0) for My\Prefix\\';
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php $var = B'String (\\\'\\r\\n\\x0) for My\\Prefix\\';
                EOD,
            <<<'EOD'
                <?php $var = B'String (\\\'\r\n\x0) for My\Prefix\\';
                EOD,
            ['single_quoted' => 'escape'],
        ];

        yield [
            <<<'EOD'
                <?php
                $var = B"\\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z";
                $var = B"\\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z \\' \\8\\9 \\xZ \\u";
                $var = B"$foo \\A \\a \\' \\8\\9 \\xZ \\u ${bar}";
                $var = B<<<HEREDOC
                \\A\\B\\C\\D\\E\\F\\G\\H\\I\\J\\K\\L\\M\\N\\O\\P\\Q\\R\\S\\T\\U\\V\\W\\X\\Y\\Z
                \\a\\b\\c\\d\\g\\h\\i\\j\\k\\l\\m\\o\\p\\q\\s\\w\\y\\z
                \\"
                \\'
                \\8\\9
                \\xZ
                \\u
                HEREDOC;
                $var = B<<<HEREDOC
                $foo \\A \\a \\" \\' \\8\\9 \\xZ \\u ${bar}
                HEREDOC;
                $var = B<<<'NOWDOC'
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \'
                \8\9
                \xZ
                \u
                NOWDOC;

                EOD,
            <<<'EOD'
                <?php
                $var = B"\A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z";
                $var = B"\a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z \' \8\9 \xZ \u";
                $var = B"$foo \A \a \' \8\9 \xZ \u ${bar}";
                $var = B<<<HEREDOC
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \"
                \'
                \8\9
                \xZ
                \u
                HEREDOC;
                $var = B<<<HEREDOC
                $foo \A \a \" \' \8\9 \xZ \u ${bar}
                HEREDOC;
                $var = B<<<'NOWDOC'
                \A\B\C\D\E\F\G\H\I\J\K\L\M\N\O\P\Q\R\S\T\U\V\W\X\Y\Z
                \a\b\c\d\g\h\i\j\k\l\m\o\p\q\s\w\y\z
                \'
                \8\9
                \xZ
                \u
                NOWDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = B"\e\f\n\r\t\v \\ \$ \"";
                $var = B"$foo \e\f\n\r\t\v \\ \$ \" ${bar}";
                $var = B<<<HEREDOC
                \e\f\n\r\t\v \\ \$
                HEREDOC;
                $var = B<<<HEREDOC
                $foo \e\f\n\r\t\v \\ \$ ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = B"\0 \00 \000 \0000 \00000";
                $var = B"$foo \0 \00 \000 \0000 \00000 ${bar}";
                $var = B<<<HEREDOC
                \0 \00 \000 \0000 \00000
                HEREDOC;
                $var = B<<<HEREDOC
                $foo \0 \00 \000 \0000 \00000 ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = B"\xA \x99 \u{0}";
                $var = B"$foo \xA \x99 \u{0} ${bar}";
                $var = B<<<HEREDOC
                \xA \x99 \u{0}
                HEREDOC;
                $var = B<<<HEREDOC
                $foo \xA \x99 \u{0} ${bar}
                HEREDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = B'backslash \ not escaped';
                $var = B'code coverage';
                $var = B"backslash \\ already escaped";
                $var = B"code coverage";
                $var = B<<<HEREDOC
                backslash \\ already escaped
                HEREDOC;
                $var = B<<<HEREDOC
                code coverage
                HEREDOC;
                $var = B<<<'NOWDOC'
                backslash \\ already escaped
                NOWDOC;
                $var = B<<<'NOWDOC'
                code coverage
                NOWDOC;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = B"\A\a \' \8\9 \xZ \u";
                $var = B"$foo \A\a \' \8\9 \xZ \u ${bar}";
                EOD,
            null,
            ['double_quoted' => 'unescape'],
        ];

        yield [
            <<<'EOD'
                <?php
                $var = B<<<HEREDOC
                \A\Z
                \a\z
                \'
                \8\9
                \xZ
                \u
                HEREDOC;
                $var = B<<<HEREDOC
                $foo
                \A\Z
                \a\z
                \'
                \8\9
                \xZ
                \u
                ${bar}
                HEREDOC;

                EOD,
            null,
            ['heredoc' => 'unescape'],
        ];

        yield [
            <<<'EOD'
                <?php
                $var = "\\bar";
                $var = "\\bar";
                $var = "\\\\bar";
                $var = "\\\\bar";
                $var = "\\\\\\bar";
                $var = "\\\\\\bar";
                EOD,
            <<<'EOD'
                <?php
                $var = "\bar";
                $var = "\\bar";
                $var = "\\\bar";
                $var = "\\\\bar";
                $var = "\\\\\bar";
                $var = "\\\\\\bar";
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = '\\bar';
                $var = '\\bar';
                $var = '\\\\bar';
                $var = '\\\\bar';
                $var = '\\\\\\bar';
                $var = '\\\\\\bar';
                EOD,
            <<<'EOD'
                <?php
                $var = '\bar';
                $var = '\\bar';
                $var = '\\\bar';
                $var = '\\\\bar';
                $var = '\\\\\bar';
                $var = '\\\\\\bar';
                EOD,
            ['single_quoted' => 'escape'],
        ];

        yield [
            <<<'EOD'
                <?php
                $var = <<<TXT
                \\bar
                \\bar
                \\\\bar
                \\\\bar
                \\\\\\bar
                \\\\\\bar
                TXT;

                EOD,
            <<<'EOD'
                <?php
                $var = <<<TXT
                \bar
                \\bar
                \\\bar
                \\\\bar
                \\\\\bar
                \\\\\\bar
                TXT;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = <<<'TXT'
                \bar
                \\bar
                \\\bar
                \\\\bar
                \\\\\bar
                \\\\\\bar
                TXT;

                EOD,
        ];

        yield 'unescaped backslashes in single quoted string - backslash' => [
            <<<'EOD'
                <?php
                '\\';
                '\\\\';
                '\\\\\\';
                EOD,
        ];

        yield 'unescaped backslashes in single quoted string - reserved double quote' => [
            <<<'EOD'
                <?php
                '\"';
                '\"';
                '\\\"';
                '\\\"';
                '\\\\\"';
                '\\\\\"';
                '\\\\\\\"';
                '\\\\\\\"';
                EOD,
            <<<'EOD'
                <?php
                '\"';
                '\\"';
                '\\\"';
                '\\\\"';
                '\\\\\"';
                '\\\\\\"';
                '\\\\\\\"';
                '\\\\\\\\"';
                EOD,
        ];

        yield 'unescaped backslashes in single quoted string - reserved chars' => [
            <<<'EOD'
                <?php
                '\b';
                '\b';
                '\\\b';
                '\\\b';
                '\\\\\b';
                '\\\\\b';
                '\\\\\\\b';
                '\\\\\\\b';
                '\$v';
                '\$v';
                '\{$v}';
                '\{$v}';
                '\n';
                '\n';
                EOD,
            <<<'EOD'
                <?php
                '\b';
                '\\b';
                '\\\b';
                '\\\\b';
                '\\\\\b';
                '\\\\\\b';
                '\\\\\\\b';
                '\\\\\\\\b';
                '\$v';
                '\\$v';
                '\{$v}';
                '\\{$v}';
                '\n';
                '\\n';
                EOD,
        ];

        yield 'unescaped backslashes in double quoted string - backslash' => [
            <<<'EOD'
                <?php
                "\\";
                "\\\\";
                "\\\\\\";
                EOD,
            null,
            ['double_quoted' => 'unescape'],
        ];

        yield 'unescaped backslashes in double quoted string - reserved chars' => [
            <<<'EOD'
                <?php
                "\b";
                "\b";
                "\\\b";
                "\\\b";
                "\\\\\b";
                "\\\\\b";
                "\\\\\\\b";
                "\\\\\\\b";
                "\$v";
                "\\$v";
                "\{$v}";
                "\\{$v}";
                "\n";
                "\\n";
                EOD,
            <<<'EOD'
                <?php
                "\b";
                "\\b";
                "\\\b";
                "\\\\b";
                "\\\\\b";
                "\\\\\\b";
                "\\\\\\\b";
                "\\\\\\\\b";
                "\$v";
                "\\$v";
                "\{$v}";
                "\\{$v}";
                "\n";
                "\\n";
                EOD,
            ['double_quoted' => 'unescape'],
        ];

        yield 'unescaped backslashes in heredoc - backslash' => [
            <<<'EOD_'
                <?php
                <<<EOD
                \
                \
                \\\
                \\\
                \\\\\
                \\\\\
                \\\\\\\
                EOD;
                EOD_,
            <<<'EOD_'
                <?php
                <<<EOD
                \
                \\
                \\\
                \\\\
                \\\\\
                \\\\\\
                \\\\\\\
                EOD;
                EOD_,
            ['heredoc' => 'unescape'],
        ];

        yield 'unescaped backslashes in heredoc - reserved single quote' => [
            <<<'EOD_'
                <?php
                <<<EOD
                \'
                \'
                \\\'
                \\\'
                \\\\\'
                \\\\\'
                \\\\\\\'
                EOD;
                EOD_,
            <<<'EOD_'
                <?php
                <<<EOD
                \'
                \\'
                \\\'
                \\\\'
                \\\\\'
                \\\\\\'
                \\\\\\\'
                EOD;
                EOD_,
            ['heredoc' => 'unescape'],
        ];

        yield 'unescaped backslashes in heredoc - reserved double quote' => [
            <<<'EOD_'
                <?php
                <<<EOD
                \"
                \"
                \\\"
                \\\"
                \\\\\"
                \\\\\"
                \\\\\\\"
                EOD;
                EOD_,
            <<<'EOD_'
                <?php
                <<<EOD
                \"
                \\"
                \\\"
                \\\\"
                \\\\\"
                \\\\\\"
                \\\\\\\"
                EOD;
                EOD_,
            ['heredoc' => 'unescape'],
        ];

        yield 'unescaped backslashes in heredoc - reserved chars' => [
            <<<'EOD_'
                <?php
                <<<EOD
                \$v
                \\$v
                \{$v}
                \\{$v}
                \n
                \\n
                \b
                \b
                \\\b
                \\\b
                \\\\\b
                \\\\\b
                \\\\\\\b
                EOD;
                EOD_,
            <<<'EOD_'
                <?php
                <<<EOD
                \$v
                \\$v
                \{$v}
                \\{$v}
                \n
                \\n
                \b
                \\b
                \\\b
                \\\\b
                \\\\\b
                \\\\\\b
                \\\\\\\b
                EOD;
                EOD_,
            ['heredoc' => 'unescape'],
        ];

        yield 'ignored mixed implicit backslashes in single quoted string' => [
            <<<'EOD'
                <?php
                $var = 'a\b\\c';
                EOD,
            null,
            ['single_quoted' => 'ignore'],
        ];

        yield 'ignored mixed implicit backslashes in double quoted string' => [
            <<<'EOD'
                <?php
                $var = "a\b\\c";
                EOD,
            null,
            ['double_quoted' => 'ignore'],
        ];

        yield 'ignored mixed implicit backslashes in heredoc' => [
            <<<'EOD'
                <?php
                $var = <<<HEREDOC
                    a\b\\c
                    HEREDOC;
                EOD,
            null,
            ['heredoc' => 'ignore'],
        ];
    }
}
