<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR0;

use Symfony\CS\ConfigInterface;
use Symfony\CS\ConfigAwareInterface;
use Symfony\CS\FixerInterface;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Psr0Fixer implements FixerInterface, ConfigAwareInterface
{
    protected $config;

    public function fix(\SplFileInfo $file, $content)
    {
        $namespace = false;
        if (preg_match('{^[^\S\n]*(?:<\?php\s+)?namespace\s+(\S+)\s*;}um', $content, $match)) {
            $namespace = $match[1];
            if (stripos($match[0], 'namespace') > 0) {
                $content = str_replace($match[0], ltrim($match[0], " \t"), $content);
            }
        }

        if (!preg_match_all('{^((abstract\s+|final\s+)?class|interface|trait)\s+(\S+)}um', $content, $matches, PREG_SET_ORDER)) {
            return $content;
        }

        if (!$matches || count($matches) > 1) {
            return $content;
        }

        $match = $matches[0];
        $keyword = $match[1];
        $class = $match[3];

        if ($namespace) {
            $normNamespace = strtr($namespace, '\\', '/');
            $path = strtr($file->getRealPath(), '\\', '/');
            $dir = dirname($path);
            $filename = basename($path, '.php');

            if ($this->config) {
                $dir = substr($dir, strlen(realpath($this->config->getDir())) + 1);
                if (strlen($normNamespace) > strlen($dir)) {
                    if (strlen($dir)) {
                        $normNamespace = substr($normNamespace, -strlen($dir));
                    } else {
                        $normNamespace = '';
                    }
                }
            }

            $dir = substr($dir, -strlen($normNamespace));
            if (false === $dir) {
                $dir = '';
            }
            $filename = basename($path, '.php');

            if ($class !== $filename) {
                $content = preg_replace('{^'.$keyword.'\s+(\S+)}um', $keyword.' '.$filename, $content, 1);
            }

            if ($normNamespace !== $dir && strtolower($normNamespace) === strtolower($dir)) {
                $namespace = substr($namespace, 0, -strlen($dir)).strtr($dir, '/', '\\');
                $content = preg_replace('{^namespace\s+(\S+)\s*;}um', 'namespace '.$namespace.';', $content, 1);
            }
        } else {
            $normClass = strtr($class, '_', '/');
            $path = strtr($file->getRealPath(), '\\', '/');
            $filename = substr($path, -strlen($normClass) - 4, -4);

            if ($normClass !== $filename && strtolower($normClass) === strtolower($filename)) {
                $content = preg_replace('{^'.$keyword.'\s+(\S+)}um', $keyword.' '.strtr($filename, '/', '_'), $content, 1);
            }
        }

        return $content;
    }

    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
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
        if ('php' !== pathinfo($file->getFilename(), PATHINFO_EXTENSION)) {
            return false;
        }

        // ignore stubs/fixtures, since they are typically containing invalid files for various reasons
        return !preg_match('{[/\\\\](stub|fixture)s?[/\\\\]}i', $file->getRealPath());
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
