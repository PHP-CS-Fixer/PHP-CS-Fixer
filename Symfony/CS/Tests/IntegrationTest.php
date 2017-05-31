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

/**
 * Test that parses and runs the fixture *.test files found in
 * /Fixtures/Integration.
 *
 * @author SpacePossum
 */
class IntegrationTest extends AbstractIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    protected function getFixturesDir()
    {
        return __DIR__.'/Fixtures/Integration';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTempDir()
    {
        return $this->getFixturesDir().'/tmp';
    }
}
