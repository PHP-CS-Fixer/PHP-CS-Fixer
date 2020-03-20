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

namespace PhpCsFixer\Tests\Fixer\Preload;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Preload\ExplicitlyLoadClass
 */
final class ExplicitlyLoadClassTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $testDir = \dirname(__DIR__, 2).'/Fixtures/Preload';
        $finder = new Finder();
        $finder->in($testDir)->name('*_Out.php');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $path = $file->getRealPath();
            $output = file_get_contents($path);

            $input = null;
            $inputFile = substr($path, 0, -7).'In.php';
            if (is_file($inputFile)) {
                $input = file_get_contents($inputFile);
            }

            yield $file->getFilename() => [$output, $input];
        }
    }
}
