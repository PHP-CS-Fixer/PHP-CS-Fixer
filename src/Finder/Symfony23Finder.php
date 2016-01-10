<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Finder;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Symfony23Finder extends DefaultFinder
{
    public function __construct()
    {
        @trigger_error(
            sprintf('The "%s" class is deprecated. You should stop using it, as it will soon be removed in 2.0 version.', __CLASS__),
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
