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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\FixerTagAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\FixerTagAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FixerTagAnalyzerTest extends TestCase
{
    /**
     * @dataProvider provideFindCases
     *
     * @param array<string, list<string>> $expectedTags
     */
    public function testFind(string $source, array $expectedTags, ?string $error = null): void
    {
        $analyzer = new FixerTagAnalyzer();

        if (null !== $error) {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage($error);
        }

        $actualTags = $analyzer->find(Tokens::fromCode($source));

        self::assertSame($expectedTags, $actualTags);
    }

    /**
     * @return iterable<string, array{string, array<string, list<string>>, 3?: string}>
     */
    public static function provideFindCases(): iterable
    {
        yield 'no tags' => [
            <<<'PHP'
                <?php

                declare(strict_types=1);

                class MyApp {}

                PHP,
            [],
        ];

        yield 'tags somewhere on head and on bottom' => [
            <<<'PHP'
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

                // @php-cs-fixer-ignore no_binary_string Ignore because of reasons

                /*
                 * @php-cs-fixer-ignore no_trailing_whitespace also ignore
                 */

                /*
                 * @php-cs-fixer-ignore no_unneeded_braces,no_unset_on_property,no_useless_else ignore multiple
                 */

                /**
                 * @php-cs-fixer-ignore no_empty_phpdoc also ignore
                 */
                class MyApp {}

                // @php-cs-fixer-ignore no_empty_statement
                // @php-cs-fixer-ignore no_extra_blank_lines

                ?>

                PHP,
            [
                'php-cs-fixer-ignore' => [
                    'no_binary_string',
                    'no_empty_phpdoc',
                    'no_empty_statement',
                    'no_extra_blank_lines',
                    'no_trailing_whitespace',
                    'no_unneeded_braces',
                    'no_unset_on_property',
                    'no_useless_else',
                ],
            ],
        ];

        yield 'duplicated values' => [
            <<<'PHP'
                <?php

                // @php-cs-fixer-ignore no_extra_blank_lines

                declare(strict_types=1);

                class MyApp {}

                // @php-cs-fixer-ignore no_empty_statement
                // @php-cs-fixer-ignore no_extra_blank_lines

                PHP,
            [],
            'Duplicated values found for tag "@php-cs-fixer-ignore": "no_extra_blank_lines".',
        ];
    }
}
