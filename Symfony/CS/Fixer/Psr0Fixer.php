<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class Psr0Fixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $namespace = false;
        if (preg_match('{namespace\s+(\S+)\s*;}u', $content, $match)) {
            $namespace = $match[1];
        }
        if (!preg_match('{(class|interface|trait)\s+(\S+)}u', $content, $match)) {
            return $content;
        }

        $keyword = $match[1];
        $class = $match[2];

        if ($namespace) {
            $normNamespace = strtr($namespace, '\\', '/');
            $path = strtr($file->getRealPath(), '\\', '/');
            $dir = substr(dirname($path), -strlen($namespace));
            $filename = basename($path, '.php');
            if ($class !== $filename) {
                $content = preg_replace('{^'.$keyword.'\s+(\S+)}um', $keyword.' '.$filename, $content, 1);
            }
            if ($normNamespace !== $dir) {
                if (strtolower($normNamespace) === strtolower($dir)) {
                    $content = preg_replace('{^namespace\s+(\S+)\s*;}um', 'namespace '.strtr($dir, '/', '\\').';', $content, 1);
                } else {
                    throw new \RuntimeException('The namespace '.$namespace.' in '.$path.' does not match the file path according to PSR-0 rules');
                }
            }
        } else {
            $normClass = strtr($class, '_', '/');
            $path = strtr($file->getRealPath(), '\\', '/');
            $filename = substr($path, -strlen($normClass)-4, -4);

            if (!strpos($class, '_')) {
                throw new \RuntimeException('Class '.$class.' in '.$path.' should have at least a vendor namespace according to PSR-0 rules');
            }

            if ($normClass !== $filename) {
                if (strtolower($normClass) === strtolower($filename)) {
                    $content = preg_replace('{^'.$keyword.'\s+(\S+)}um', $keyword.' '.strtr($filename, '/', '_'), $content, 1);
                } else {
                    throw new \RuntimeException('The class name '.$class.' in '.$path.' does not match the file path according to PSR-0 rules');
                }
            }
        }

        return $content;
    }

    public function getLevel()
    {
        return FixerInterface::PSR0_LEVEL;
    }

    public function getPriority()
    {
        return -10;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'psr0';
    }

    public function getDescription()
    {
        return 'Classes must be in a path that matches their namespace, be at least one namespace deep, and the class name should match the file name.';
    }
}
