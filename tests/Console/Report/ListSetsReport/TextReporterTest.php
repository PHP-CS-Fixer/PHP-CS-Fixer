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

namespace PhpCsFixer\Tests\Console\Report\ListSetsReport;

use PhpCsFixer\Console\Report\ListSetsReport\TextReporter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\ListSetsReport\TextReporter
 */
final class TextReporterTest extends AbstractReporterTestCase
{
    protected function createReporter()
    {
        return new TextReporter();
    }

    protected function getFormat()
    {
        return 'txt';
    }

    protected function assertFormat($expected, $input)
    {
        static::assertSame($expected, $input);
    }

    /**
     * @return string
     */
    protected function createSimpleReport()
    {
        return str_replace("\n", PHP_EOL, ' 1) @PhpCsFixer
      Rule set as used by the PHP-CS-Fixer development team, highly opinionated.
 2) @Symfony:risky
      Rules that follow the official `Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html>`_.
      Set contains risky rules.
');
    }
}
