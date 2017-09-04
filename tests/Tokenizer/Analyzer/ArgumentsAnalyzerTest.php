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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer
 */
final class ArgumentsAnalyzerTest extends TestCase
{
    /**
     * @param string $code
     * @param int    $openIndex
     * @param int    $closeIndex
     * @param array  $arguments
     *
     * @dataProvider provideArgumentsCases
     */
    public function testArguments($code, $openIndex, $closeIndex, array $arguments)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        $this->assertSame(count($arguments), $analyzer->countArguments($tokens, $openIndex, $closeIndex));
        $this->assertSame($arguments, $analyzer->getArguments($tokens, $openIndex, $closeIndex));
    }

    public function provideArgumentsCases()
    {
        return array(
            array('<?php fnc();', 2, 3, array()),
            array('<?php fnc($a);', 2, 4, array(3 => 3)),
            array('<?php fnc($a, $b);', 2, 7, array(3 => 3, 5 => 6)),
            array('<?php fnc($a, $b = array(1,2), $c = 3);', 2, 23, array(3 => 3, 5 => 15, 17 => 22)),
        );
    }
}
