<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Test\AbstractIntegrationTestCase;

/**
 * Test that parses and runs the fixture '*.test' files found in '/Fixtures/Integration'.
 *
 * @author SpacePossum <possumfromspace@gmail.com>
 *
 * @internal
 */
final class IntegrationTest extends AbstractIntegrationTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function getFixturesDir()
    {
        return __DIR__.'/Fixtures/Integration';
    }

    /**
     * {@inheritdoc}
     */
    protected static function getTempFile()
    {
        return self::getFixturesDir().'/tmp/tmp.php';
    }
}
