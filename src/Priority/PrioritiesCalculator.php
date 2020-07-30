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

namespace PhpCsFixer\Priority;

use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\AutoReview\FixerFactoryTest;

/**
 * @internal
 */
final class PrioritiesCalculator
{
    public function calculate()
    {
        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();

        $priorities = [];
        foreach ($fixerFactory->getFixers() as $fixer) {
            $priorities[$fixer->getName()] = new Priority();
        }

        $cases = array_merge(
            FixerFactoryTest::provideFixersPriorityCases(),
            FixerFactoryTest::provideFixersPrioritySpecialPhpdocCases()
        );

        foreach ($cases as $beforeAfter) {
            list($before, $after) = $beforeAfter;

            $beforeName = $before->getName();
            $afterName = $after->getName();

            $priorities[$beforeName]->addLowerPriority($priorities[$afterName]);
        }

        $priorities = array_map(
            function (Priority $x) {
                return $x->getPriority();
            },
            $priorities
        );

        // update edge cases
        $priorities['final_class'] = $priorities['final_internal_class'];
        $priorities['no_multiline_whitespace_before_semicolons'] = $priorities['multiline_whitespace_before_semicolons'];
        $priorities['no_extra_consecutive_blank_lines'] = $priorities['no_extra_blank_lines'];
        $priorities['full_opening_tag'] = max($priorities) + 1;
        $priorities['encoding'] = max($priorities) + 1;
        $priorities['single_blank_line_at_eof'] = min($priorities) - 1;
        asort($priorities);

        return $priorities;
    }
}
