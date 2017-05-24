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

use PhpCsFixer\Linter\ProcessLinter;
use PhpCsFixer\Test\AccessibleObject;
use Symfony\Component\Process\ProcessUtils;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\ProcessLinter
 * @covers \PhpCsFixer\Linter\ProcessLintingResult
 */
final class ProcessLinterTest extends AbstractLinterTestCase
{
    public function testIsAsync()
    {
        $this->assertTrue($this->createLinter()->isAsync());
    }

    public function testPrepareCommand()
    {
        $this->assertSame(
            $this->fixEscape('"php" -l "foo.php"'),
            AccessibleObject::create(new ProcessLinter('php'))->prepareCommand('foo.php')
        );

        $this->assertSame(
            $this->fixEscape('"C:\Program Files\php\php.exe" -l "foo bar\baz.php"'),
            AccessibleObject::create(new ProcessLinter('C:\Program Files\php\php.exe'))->prepareCommand('foo bar\baz.php')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createLinter()
    {
        return new ProcessLinter();
    }

    /**
     * Fix escaping character.
     *
     * Escape character may be different on various environments.
     * This method change used escape character into character that is default
     * for environment.
     *
     * @param string $value          value to be fixed
     * @param string $usedEscapeChar used escape char, may be only ' or "
     *
     * @return string
     */
    private function fixEscape($value, $usedEscapeChar = '"')
    {
        static $escapeChar = null;

        if (null === $escapeChar) {
            $escapeChar = ProcessUtils::escapeArgument('x')[0];
        }

        if ($usedEscapeChar === $escapeChar) {
            return $value;
        }

        return str_replace($usedEscapeChar, $escapeChar, $value);
    }
}
