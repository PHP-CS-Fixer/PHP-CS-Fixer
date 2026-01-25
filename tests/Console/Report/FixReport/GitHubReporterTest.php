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

namespace PhpCsFixer\Tests\Console\Report\FixReport;

use PhpCsFixer\Console\Report\FixReport\GitHubReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;

/**
 * @author HypeMC <hypemc@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\GitHubReporter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class GitHubReporterTest extends AbstractReporterTestCase
{
    protected function createReporter(): ReporterInterface
    {
        return new GitHubReporter();
    }

    protected function getFormat(): string
    {
        return 'github';
    }

    protected static function createNoErrorReport(): string
    {
        return '';
    }

    protected static function createSimpleReport(): string
    {
        return '::error file=someFile.php,line=5,title=PHP-CS-Fixer.some_fixer_name_here::PHP-CS-Fixer.some_fixer_name_here (custom rule)'.\PHP_EOL;
    }

    protected static function createWithDiffReport(): string
    {
        return self::createSimpleReport();
    }

    protected static function createWithAppliedFixersReport(): string
    {
        return '::error file=someFile.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here_1::PHP-CS-Fixer.some_fixer_name_here_1 (custom rule)'.\PHP_EOL
            .'::error file=someFile.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here_2::PHP-CS-Fixer.some_fixer_name_here_2 (custom rule)'.\PHP_EOL;
    }

    protected static function createWithTimeAndMemoryReport(): string
    {
        return self::createSimpleReport();
    }

    protected static function createComplexReport(): string
    {
        return '::error file=someFile.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here_1::PHP-CS-Fixer.some_fixer_name_here_1 (custom rule)'.\PHP_EOL
            .'::error file=someFile.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here_2::PHP-CS-Fixer.some_fixer_name_here_2 (custom rule)'.\PHP_EOL
            .'::error file=anotherFile.php,line=0,title=PHP-CS-Fixer.another_fixer_name_here::PHP-CS-Fixer.another_fixer_name_here (custom rule)'.\PHP_EOL;
    }

    protected function assertFormat(string $expected, string $input): void
    {
        self::assertSame($expected, $input);
    }
}
