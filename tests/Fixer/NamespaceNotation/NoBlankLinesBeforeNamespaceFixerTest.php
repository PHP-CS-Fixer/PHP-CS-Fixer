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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractLinesBeforeNamespaceFixer
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\NoBlankLinesBeforeNamespaceFixer
 */
final class NoBlankLinesBeforeNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, WhitespacesFixerConfig $whitespaces = null): void
    {
        if (null !== $whitespaces) {
            $this->fixer->setWhitespacesConfig($whitespaces);
        }
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            ['<?php namespace Some\Name\Space;'],
            ["<?php\nnamespace X;"],
            ["<?php\nnamespace X;", "<?php\n\n\n\nnamespace X;"],
            ["<?php\r\nnamespace X;"],
            ["<?php\nnamespace X;", "<?php\r\n\r\n\r\n\r\nnamespace X;"],
            ["<?php\r\nnamespace X;", "<?php\r\n\r\n\r\n\r\nnamespace X;", new WhitespacesFixerConfig('    ', "\r\n")],
            ["<?php\n\nnamespace\\Sub\\Foo::bar();"],
            [
                '<?php
    // Foo
    namespace Foo;
',
                '<?php
    // Foo
    '.'
    namespace Foo;
',
            ],
            [
                '<?php
// Foo
namespace Foo;
',
                '<?php
// Foo
    '.'
namespace Foo;
',
            ],
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
