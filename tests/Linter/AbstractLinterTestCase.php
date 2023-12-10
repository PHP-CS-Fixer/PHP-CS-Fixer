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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractLinterTestCase extends TestCase
{
    abstract public function testIsAsync(): void;

    public function testLintingAfterTokenManipulation(): void
    {
        $linter = $this->createLinter();

        $tokens = Tokens::fromCode("<?php \n#EOF\n");
        $tokens->insertAt(1, new Token([T_NS_SEPARATOR, '\\']));

        $this->expectException(LintingException::class);
        $linter->lintSource($tokens->generateCode())->check();
    }

    /**
     * @dataProvider provideLintFileCases
     */
    public function testLintFile(string $file, ?string $errorMessage = null): void
    {
        if (null !== $errorMessage) {
            $this->expectException(LintingException::class);
            $this->expectExceptionMessage($errorMessage);
        } else {
            $this->expectNotToPerformAssertions();
        }

        $linter = $this->createLinter();
        $linter->lintFile($file)->check();
    }

    /**
     * @medium
     */
    public static function provideLintFileCases(): iterable
    {
        yield [
            __DIR__.'/../Fixtures/Linter/valid.php',
        ];

        yield [
            __DIR__.'/../Fixtures/Linter/invalid.php',
            sprintf('Parse error: syntax error, unexpected %s on line 5.', PHP_MAJOR_VERSION >= 8 ? 'token "echo"' : '\'echo\' (T_ECHO)'),
        ];

        yield [
            __DIR__.'/../Fixtures/Linter/multiple.php',
            'Fatal error: Multiple access type modifiers are not allowed on line 4.',
        ];
    }

    /**
     * @dataProvider provideLintSourceCases
     */
    public function testLintSource(string $source, ?string $errorMessage = null): void
    {
        if (null !== $errorMessage) {
            $this->expectException(LintingException::class);
            $this->expectExceptionMessage($errorMessage);
        } else {
            $this->expectNotToPerformAssertions();
        }

        $linter = $this->createLinter();
        $linter->lintSource($source)->check();
    }

    public static function provideLintSourceCases(): iterable
    {
        yield [
            '<?php echo 123;',
        ];

        yield [
            '<?php
                    print "line 2";
                    print "line 3";
                    print "line 4";
                    echo echo;
                ',
            sprintf('Parse error: syntax error, unexpected %s on line 5.', PHP_MAJOR_VERSION >= 8 ? 'token "echo"' : '\'echo\' (T_ECHO)'),
        ];
    }

    abstract protected function createLinter(): LinterInterface;
}
