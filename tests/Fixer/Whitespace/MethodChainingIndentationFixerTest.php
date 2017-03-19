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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Boliev <voff.web@gmail.com>
 *
 * @internal
 */
final class MethodChainingIndentationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param mixed      $expected
     * @param null|mixed $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php

    $user->setEmail(\'voff.web@gmail.com\')
        ->setPassword(\'233434\')
        ->setEmailConfirmed(false)
        ->setEmailConfirmationCode(\'123456\')
        ->setHashsalt(\'1234\')
        ->setTncAccepted(true);
', // This is expected output
                '<?php

    $user->setEmail(\'voff.web@gmail.com\')

     ->setPassword(\'233434\')
        ->setEmailConfirmed(false)
->setEmailConfirmationCode(\'123456\')

                ->setHashsalt(\'1234\')
  ->setTncAccepted(true);
', // This is input
            ),
        );
    }
}
