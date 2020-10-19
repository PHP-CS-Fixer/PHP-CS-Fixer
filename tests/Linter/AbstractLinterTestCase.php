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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\LinterInterface;
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
    abstract public function testIsAsync();

    public function testLintingAfterTokenManipulation()
    {
        $linter = $this->createLinter();

        $tokens = Tokens::fromCode("<?php \n#EOF\n");
        $tokens->insertAt(1, new Token([T_NS_SEPARATOR, '\\']));

        $this->expectException(\PhpCsFixer\Linter\LintingException::class);
        $linter->lintSource($tokens->generateCode())->check();
    }

    /**
     * @param string      $file
     * @param null|string $errorMessage
     *
     * @dataProvider provideLintFileCases
     */
    public function testLintFile($file, $errorMessage = null)
    {
        if (null !== $errorMessage) {
            $this->expectException(\PhpCsFixer\Linter\LintingException::class);
            $this->expectExceptionMessage($errorMessage);
        }

        $linter = $this->createLinter();

        static::assertNull($linter->lintFile($file)->check());
    }

    /**
     * @return array
     */
    public function provideLintFileCases()
    {
        return [
            [
                __DIR__.'/../Fixtures/Linter/valid.php',
            ],
            [
                __DIR__.'/../Fixtures/Linter/invalid.php',
                sprintf('Parse error: syntax error, unexpected %s on line 5.', PHP_MAJOR_VERSION >= 8 ? 'token "echo"' : '\'echo\' (T_ECHO)'),
            ],
            [
                __DIR__.'/../Fixtures/Linter/multiple.php',
                'Fatal error: Multiple access type modifiers are not allowed on line 4.',
            ],
        ];
    }

    /**
     * @param string      $source
     * @param null|string $errorMessage
     *
     * @dataProvider provideLintSourceCases
     */
    public function testLintSource($source, $errorMessage = null)
    {
        if (null !== $errorMessage) {
            $this->expectException(\PhpCsFixer\Linter\LintingException::class);
            $this->expectExceptionMessage($errorMessage);
        }

        $linter = $this->createLinter();

        static::assertNull($linter->lintSource($source)->check());
    }

    /**
     * @return array
     */
    public function provideLintSourceCases()
    {
        return [
            [
                '<?php echo 123;',
            ],
            [
                '<?php
                    print "line 2";
                    print "line 3";
                    print "line 4";
                    echo echo;
                ',
                sprintf('Parse error: syntax error, unexpected %s on line 5.', PHP_MAJOR_VERSION >= 8 ? 'token "echo"' : '\'echo\' (T_ECHO)'),
            ],
        ];
    }

    /**
     * @return LinterInterface
     */
    abstract protected function createLinter();
}
