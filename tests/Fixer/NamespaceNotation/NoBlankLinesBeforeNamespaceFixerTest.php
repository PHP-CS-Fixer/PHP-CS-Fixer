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

namespace PhpCsFixer\Tests\Fixer\NamespaceNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\NoBlankLinesBeforeNamespaceFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\NamespaceNotation\NoBlankLinesBeforeNamespaceFixer>
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class NoBlankLinesBeforeNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, ?WhitespacesFixerConfig $whitespaces = null): void
    {
        if (null !== $whitespaces) {
            $this->fixer->setWhitespacesConfig($whitespaces);
        }
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string, 2?: WhitespacesFixerConfig}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php namespace Some\Name\Space;'];

        yield ["<?php\nnamespace X;"];

        yield ["<?php\nnamespace X;", "<?php\n\n\n\nnamespace X;"];

        yield ["<?php\r\nnamespace X;"];

        yield ["<?php\nnamespace X;", "<?php\r\n\r\n\r\n\r\nnamespace X;"];

        yield ["<?php\r\nnamespace X;", "<?php\r\n\r\n\r\n\r\nnamespace X;", new WhitespacesFixerConfig('    ', "\r\n")];

        yield ["<?php\n\nnamespace\\Sub\\Foo::bar();"];

        yield [
            '<?php
    // Foo
    namespace Foo;
',
            '<?php
    // Foo
    '.'
    namespace Foo;
',
        ];

        yield [
            '<?php
// Foo
namespace Foo;
',
            '<?php
// Foo
    '.'
namespace Foo;
',
        ];
    }

    public function testFixExampleWithComment(): void
    {
        $expected = <<<'EOF'
            <?php

            /*
             * This file is part of the PHP CS utility.
             *
             * (c) Fabien Potencier <fabien@symfony.com>
             *
             * This source file is subject to the MIT license that is bundled
             * with this source code in the file LICENSE.
             */
            namespace PhpCsFixer\Fixer\Contrib;
            EOF;

        $input = <<<'EOF'
            <?php

            /*
             * This file is part of the PHP CS utility.
             *
             * (c) Fabien Potencier <fabien@symfony.com>
             *
             * This source file is subject to the MIT license that is bundled
             * with this source code in the file LICENSE.
             */

            namespace PhpCsFixer\Fixer\Contrib;
            EOF;

        $this->doTest($expected, $input);
    }
}
