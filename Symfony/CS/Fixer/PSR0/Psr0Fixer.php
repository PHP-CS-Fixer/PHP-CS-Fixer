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

namespace Symfony\CS\Fixer\PSR0;

use Symfony\CS\AbstractFixer;
use Symfony\CS\ConfigAwareInterface;
use Symfony\CS\ConfigInterface;
use Symfony\CS\StdinFileInfo;
use Symfony\CS\Tokenizer\Tokens;

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

                $namespaceIndex = $tokens->getNextMeaningfulToken($index);
                $namespaceEndIndex = $tokens->getNextTokenOfKind($index, array(';'));

                $namespace = trim($tokens->generatePartialCode($namespaceIndex, $namespaceEndIndex - 1));
            } elseif ($token->isClassy()) {
                if (null !== $classyName) {
                    return $content;
                }

                $classyIndex = $tokens->getNextMeaningfulToken($index);
                $classyName = $tokens[$classyIndex]->getContent();
            }
        }

        if (null === $classyName) {
            return $content;
        }

        if (false !== $namespace) {
            $normNamespace = str_replace('\\', '/', $namespace);
            $path = str_replace('\\', '/', $file->getRealPath());
            $dir = dirname($path);

            if ($this->config) {
                $dir = substr($dir, strlen(realpath($this->config->getDir())) + 1);
                if (strlen($normNamespace) > strlen($dir)) {
                    if ('' !== $dir) {
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
                $tokens[$classyIndex]->setContent($filename);
            }

            if ($normNamespace !== $dir && strtolower($normNamespace) === strtolower($dir)) {
                for ($i = $namespaceIndex; $i <= $namespaceEndIndex; ++$i) {
                    $tokens[$i]->clear();
                }
                $namespace = substr($namespace, 0, -strlen($dir)).str_replace('/', '\\', $dir);

                $newNamespace = Tokens::fromCode('<?php namespace '.$namespace.';');
                $newNamespace[0]->clear();
                $newNamespace[1]->clear();
                $newNamespace[2]->clear();

                $tokens->insertAt($namespaceIndex, $newNamespace);
            }
        } else {
            $normClass = str_replace('_', '/', $classyName);
            $path = str_replace('\\', '/', $file->getRealPath());
            $filename = substr($path, -strlen($normClass) - 4, -4);

            if ($normClass !== $filename && strtolower($normClass) === strtolower($filename)) {
                $tokens[$classyIndex]->setContent(str_replace('/', '_', $filename));
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
    public function getDescription()
    {
        return 'Classes must be in a path that matches their namespace, be at least one namespace deep, and the class name should match the file name.';
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        if ($file instanceof StdinFileInfo) {
            return false;
        }

        $filenameParts = explode('.', $file->getBasename(), 2);

        if (
            // ignore file with extension other than php
            (!isset($filenameParts[1]) || 'php' !== $filenameParts[1])
            // ignore file with name that cannot be a class name
            || 0 === preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $filenameParts[0])
        ) {
            return false;
        }

        $tokens = Tokens::fromCode(sprintf('<?php %s {}', $filenameParts[0]));
        if ($tokens[1]->isKeyword() || $tokens[1]->isMagicConstant()) {
            return false;
        }

        // ignore stubs/fixtures, since they are typically containing invalid files for various reasons
        return !preg_match('{[/\\\\](stub|fixture)s?[/\\\\]}i', $file->getRealPath());
    }
}
