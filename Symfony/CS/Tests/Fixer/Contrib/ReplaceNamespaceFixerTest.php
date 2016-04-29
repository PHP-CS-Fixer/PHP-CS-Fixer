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

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

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
}
