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

namespace PhpCsFixer\Report;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class JsonReport implements ReportInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ReportConfig $reportConfig)
    {
        $jFiles = array();

        foreach ($reportConfig->getChanged() as $file => $fixResult) {
            $jfile = array('name' => $file);

            if ($reportConfig->shouldAddAppliedFixers()) {
                $jfile['appliedFixers'] = $fixResult['appliedFixers'];
            }

            if (!empty($fixResult['diff'])) {
                $jfile['diff'] = $fixResult['diff'];
            }

            $jFiles[] = $jfile;
        }

        $json = array(
            'files' => $jFiles,
        );

        if (null !== $reportConfig->getTime()) {
            $json['time'] = array(
                'total' => round($reportConfig->getTime() / 1000, 3),
            );
        }

        if (null !== $reportConfig->getMemory()) {
            $json['memory'] = round($reportConfig->getMemory() / 1024 / 1024, 3);
        }

        return json_encode($json);
    }
}
