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

namespace Symfony\CS\Finder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated
 */
class Symfony23Finder extends DefaultFinder
{
    public function __construct()
    {
        @trigger_error(
            sprintf(
                'The "%s" class is deprecated. You should stop using it, as it will soon be removed in 2.0 version. Use "%s" instead.',
                __CLASS__,
                'Symfony\CS\Finder'
            ),
            E_USER_DEPRECATED
        );

        parent::__construct();
    }

    protected function getDirs($dir)
    {
        return array($dir.'/src');
    }

    protected function getFilesToExclude()
    {
        return array(
            'Symfony/Component/Console/Tests/Fixtures/application_1.xml',
            'Symfony/Component/Console/Tests/Fixtures/application_2.xml',
            'Symfony/Component/Console/Tests/Helper/TableHelperTest.php',
            'Symfony/Component/DependencyInjection/Tests/Fixtures/yaml/services1.yml',
            'Symfony/Component/DependencyInjection/Tests/Fixtures/yaml/services8.yml',
            'Symfony/Component/Yaml/Tests/Fixtures/sfTests.yml',
        );
    }
}
