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
use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokens;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Bram Gotink <bram@gotink.me>
 */
class Psr0Fixer extends AbstractFixer implements ConfigAwareInterface
{
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $namespace = false;
        $namespaceIndex = 0;
        $namespaceEndIndex = 0;

        $classyName = null;
        $classyIndex = 0;

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_NAMESPACE)) {
                if (false !== $namespace) {
                    return $content;
                }

                $tokens->getNextNonWhitespace($index, array(), $namespaceIndex);
                $tokens->getNextTokenOfKind($index, array(';'), $namespaceEndIndex);

                $namespace = trim($tokens->generatePartialCode($namespaceIndex, $namespaceEndIndex - 1));
            } elseif ($token->isClassy()) {
                if (null !== $classyName) {
                    return $content;
                }

                $classyName = $tokens->getNextNonWhitespace($index, array(), $classyIndex)->content;
            }
        }

        if (null === $classyName) {
            return $content;
        }

        if (false !== $namespace) {
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

            if ($classyName !== $filename) {
                $tokens[$classyIndex]->content = $filename;
            }

            if ($normNamespace !== $dir && strtolower($normNamespace) === strtolower($dir)) {
                for ($i = $namespaceIndex; $i <= $namespaceEndIndex; $i++) {
                    $tokens[$i]->clear();
                }
                $namespace = substr($namespace, 0, -strlen($dir)).strtr($dir, '/', '\\');

                $newNamespace = Tokens::fromCode('<?php namespace '.$namespace.';');
                $newNamespace[0]->clear();
                $newNamespace[1]->clear();
                $newNamespace[2]->clear();

                $tokens->insertAt($namespaceIndex, $newNamespace);
            }
        } else {
            $normClass = strtr($classyName, '_', '/');
            $path = strtr($file->getRealPath(), '\\', '/');
            $filename = substr($path, -strlen($normClass) - 4, -4);

            if ($normClass !== $filename && strtolower($normClass) === strtolower($filename)) {
                $tokens[$classyIndex]->content = strtr($filename, '/', '_');
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        if ('php' !== pathinfo($file->getFilename(), PATHINFO_EXTENSION)) {
            return false;
        }

        // ignore stubs/fixtures, since they are typically containing invalid files for various reasons
        return !preg_match('{[/\\\\](stub|fixture)s?[/\\\\]}i', $file->getRealPath());
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Classes must be in a path that matches their namespace, be at least one namespace deep, and the class name should match the file name.';
    }
}
