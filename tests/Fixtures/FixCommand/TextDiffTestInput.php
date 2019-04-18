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

/**
 * Input for @see \PhpCsFixer\Tests\TextDiffTest.
 *
 * @author SpacePossum
 *
 * @internal
 */
final class TextDiffTestInput
{
    public function foo($output)
    {
        $output->writeln('<error>'.(int)$output.'</error>');
        $output->writeln('<error> TEST </error>');
        $output->writeln('<error>'.(int)$output.'</error>');
    }
}
