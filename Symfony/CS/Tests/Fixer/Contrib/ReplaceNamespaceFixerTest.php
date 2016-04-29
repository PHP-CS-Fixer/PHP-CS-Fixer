<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Your name <your@email.com>
 *
 * @internal
 */
final class ReplaceNamespaceFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
/**
 * @project Test Test
 *
 * @copyright Copyright (c) 2016. Test Test Test (http://www.test.test)
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

require_once "test.php";

/**
 * Class CheckVerifyTokenRequest
 * @package Symfony\Core\Authorization\Requests
 */
class CheckVerifyTokenRequest extends AuthorizationRequest
{',
                '<?php
/**
 * @project Test Test
 *
 * @copyright Copyright (c) 2016. Test Test Test (http://www.test.test)
 */

namespace Symfony\Authentication\Core\Requests;

require_once "test.php";

/**
 * Class CheckVerifyTokenRequest
 * @package Symfony\Core\Authorization\Requests
 */
class CheckVerifyTokenRequest extends AuthorizationRequest
{',
            ),
            array(
                '<?php
/**
 * @project Test Test
 *
 * @copyright Copyright (c) 2016. Test Test Test (http://www.test.test)
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

/**
 * Class CheckVerifyTokenRequest
 * @package Symfony\Core\Authorization\Requests
 */
class CheckVerifyTokenRequest extends AuthorizationRequest
{',
                '<?php
/**
 * @project Test Test
 *
 * @copyright Copyright (c) 2016. Test Test Test (http://www.test.test)
 */

namespace Symfony\Authentication\Core\Requests;

/**
 * Class CheckVerifyTokenRequest
 * @package Symfony\Core\Authorization\Requests
 */
class CheckVerifyTokenRequest extends AuthorizationRequest
{',
            ),
            array(
                '<?php
/**
 * @project Test Test
 *
 * @copyright Copyright (c) 2016. Test Test Test (http://www.test.test)
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use App\Repositories\TwoFactorAuthMessageRepository;
use App\TwoFactorAuthMessage;
use DateTime;
use Exception;
use MessageBird\Client as MessageBirdClient;
use MessageBird\Objects\Verify;
',
                '<?php
/**
 * @project Test Test
 *
 * @copyright Copyright (c) 2016. Test Test Test (http://www.test.test)
 */

namespace Symfony\Core\Services\Auth\TwoFactor;

use App\Repositories\TwoFactorAuthMessageRepository;
use App\TwoFactorAuthMessage;
use DateTime;
use Exception;
use MessageBird\Client as MessageBirdClient;
use MessageBird\Objects\Verify;
',
            ),
        );
    }

    protected function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }

//    protected function makeTest($expected, $input = null, \SplFileInfo $file = null, FixerInterface $fixer = null)
//    {
//        if ($expected === $input) {
//            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
//        }
//
//        $fixer = $fixer ?: $this->getFixer();
//        $file = $file ?: $this->getTestFile();
//        $fileIsSupported = $fixer->supports($file);
//
//        if (null !== $input) {
//            $fixedCode = $fileIsSupported ? $fixer->fix($file, $input) : $input;
//
//            $this->assertSame($expected, $fixedCode);
//
//            $tokens = Tokens::fromCode($fixedCode); // Load cached collection (used by the fixer)
//            Tokens::clearCache();
//            $expectedTokens = Tokens::fromCode($fixedCode); // Load the expected collection based on PHP parsing
//            $this->assertTokens($expectedTokens, $tokens);
//        }
//
//        $this->assertSame($expected, $fileIsSupported ? $fixer->fix($file, $expected) : $expected);
//    }

//    private function assertTokens(Tokens $expectedTokens, Tokens $tokens)
//    {
        //        foreach ($expectedTokens as $index => $expectedToken) {
//            $token = $tokens[$index];
//
//            $expectedPrototype = $expectedToken->getPrototype();
//            if (is_array($expectedPrototype)) {
//                unset($expectedPrototype[2]); // don't compare token lines as our token mutations don't deal with line numbers
//            }
//
//            $this->assertTrue($token->equals($expectedPrototype), sprintf('The token at index %d should be %s, got %s', $index, json_encode($expectedPrototype), $token->toJson()));
//        }

//        $this->assertSame($expectedTokens->count(), $tokens->count(), 'The collection should have the same length than the expected one');
//    }
}
