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
 * @covers \PhpCsFixer\Fixer\Preload\PreloadExplicitClassSymbolsFixer
 */
final class PreloadExplicitClassSymbolsFixerTest extends AbstractFixerTestCase
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
        $finder->in($testDir)->name('*.test-out.php');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $output = file_get_contents($file->getRealPath());
            $inputFilePattern = substr($file->getFilename(), 0, -7).'in.php';
            $inputFinder = new Finder();
            $inputFinder->in($testDir)->name($inputFilePattern);

            /** @var SplFileInfo $input */
            foreach ($inputFinder as $input) {
                yield sprintf('%s => %s', $input->getFilename(), $file->getFilename()) => [$output, file_get_contents($input->getRealPath())];
            }

            yield $file->getFilename() => [$output, null];
        }
    }

    /**
     * This test is helpful when debugging.
     * Feel free to change $output and $input variables.
     */
    public function testSpecific()
    {
        $outfile = 'Case010.test-out.php';
        $infile = null;

        $testDir = \dirname(__DIR__, 2).'/Fixtures/Preload';
        $input = null;
        if (null !== $infile) {
            $input = file_get_contents($testDir.'/'.$infile);
        }

        $this->doTest(file_get_contents($testDir.'/'.$outfile), $input);
    }
}
