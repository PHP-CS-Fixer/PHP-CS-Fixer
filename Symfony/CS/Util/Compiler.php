<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Util;

use Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles the Symfony CS Fixer utility.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Compiler
{
    public function compile($pharFile = 'php-cs-fixer.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'PHP CS Fixer');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        // CLI Component files
        foreach ($this->getFiles() as $file) {
            $path = str_replace(__DIR__.'/', '', $file);
            $phar->addFromString($path, file_get_contents($file));
        }

        // Stubs
        $phar['_cli_stub.php'] = $this->getCliStub();
        $phar['_web_stub.php'] = $this->getWebStub();
        $phar->setDefaultStub('_cli_stub.php', '_web_stub.php');

        $phar->stopBuffering();

        // $phar->compressFiles(\Phar::GZ);

        unset($phar);

        chmod($pharFile, 0777);
    }

    protected function getCliStub()
    {
        return "<?php require_once __DIR__.'/fixer'; __HALT_COMPILER();";
    }

    protected function getWebStub()
    {
        return "<?php throw new \LogicException('This PHAR file can only be used from the CLI.'); __HALT_COMPILER();";
    }

    protected function getLicense()
    {
        return '
    /*
     * This file is part of the Symfony CS utility.
     *
     * (c) Fabien Potencier <fabien@symfony.com>
     *
     * This source file is subject to the MIT license that is bundled
     * with this source code in the file LICENSE.
     */';
    }

    protected function getFiles()
    {
        $iterator = Finder::create()->files()->name('*.php')->in(array('vendor', 'Symfony'));

        return array_merge(array('LICENSE', 'fixer'), iterator_to_array($iterator));
    }
}
