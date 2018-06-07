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

namespace PhpCsFixer\Tests;

/**
 * @internal
  */
final class TokensTest extends TestCase
{
    public function testTokens()
    {
        $code = '<?php echo 1;';
        $asText = '';

        foreach (token_get_all($code) as $token) {
            if (is_array($token)) {
                $asText .= sprintf(
                    "%3d|%3d|%-20s|%s\n",
                    $token[2],
                    $token[0],
                    is_int($token[0]) ? token_name($token[0]) : 'n/a',
                    $token[1]
                );

                continue;
            }

            $asText .= sprintf("   |   |                    |%s\n", $token);
        }

        $expected =
'  1|379|T_OPEN_TAG          |<?php 
  1|328|T_ECHO              |echo
  1|382|T_WHITESPACE        | 
  1|317|T_LNUMBER           |1
   |   |                    |;
';

        $this->assertSame($expected, $asText);
    }
}
