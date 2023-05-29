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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Tokenizer\Tokens;

final class Php83Test extends TestCase
{
    /**
     * @dataProvider providePhp83Fixtures
     */
    public function testPhp83Support(string $source): void
    {
        $tokens = Tokens::fromCode($source);
        $this->dump($tokens);
        $this->fail('foo');
    }

    public function providePhp83Fixtures(): iterable
    {
        yield 'anonymous readonly class' => [
            '<?php
                $readonly_anon = new #[AllowDynamicProperties] readonly class {
                    public int $field;
                    function __construct() {
                        $this->field = 2;
                    }
            };',
        ];

        yield 'typed const' => [
            '<?php
                class Test {
                    public const int TEST1 = 1;
                }
            ',
        ];
    }

    function dump(Tokens $tokens): void
    {
        echo "\n---------------\n";
        foreach ($tokens as $ii => $tt) {
            if (null === $tt) {
                $content = '!!!!!!!!!!! NULL !!!!!!!!!!!';
                $name = 'NULL';
                $empty = '| !!! NULL !!!';
            } else {
                $content = $tt->getContent();
                $content = str_replace("\n", '\n', $content);
                $content = str_replace("\t", '\t', $content);
                $name = $tt->getName();
                $empty = $tokens->isEmptyAt($ii) ? '| [empty]' : '';
            }

            echo sprintf(
                "\n%5d \033[31m|\033[0m \033[32m%-30s\033[0m\e[31m|\e[0m%s\e[31m|\e[0m%s",
                $ii,
                $name,
                $content,
                $empty
            );
        }
        echo "\n---------------\n";
        echo $tokens->generateCode();
        echo "\n---------------\n";
    }
}
