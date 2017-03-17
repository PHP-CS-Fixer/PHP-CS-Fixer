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

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractPsrAutoloadingFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Bram Gotink <bram@gotink.me>
 * @author Graham Campbell <graham@alt-three.com>
 */
final class Psr0Fixer extends AbstractPsrAutoloadingFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigurationDefinition()
    {
        $dir = new FixerOption('dir', 'The directory where the project code is placed.');
        $dir->setAllowedTypes(array('string'));

        return new FixerConfigurationResolver(array(
            $dir,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $namespace = false;
        $namespaceIndex = 0;
        $namespaceEndIndex = 0;

        $classyName = null;
        $classyIndex = 0;

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_NAMESPACE)) {
                if (false !== $namespace) {
                    return;
                }

                $namespaceIndex = $tokens->getNextMeaningfulToken($index);
                $namespaceEndIndex = $tokens->getNextTokenOfKind($index, array(';'));

                $namespace = trim($tokens->generatePartialCode($namespaceIndex, $namespaceEndIndex - 1));
            } elseif ($token->isClassy()) {
                if (null !== $classyName) {
                    return;
                }

                $classyIndex = $tokens->getNextMeaningfulToken($index);
                $classyName = $tokens[$classyIndex]->getContent();
            }
        }

        if (null === $classyName) {
            return;
        }

        if (false !== $namespace) {
            $normNamespace = str_replace('\\', '/', $namespace);
            $path = str_replace('\\', '/', $file->getRealPath());
            $dir = dirname($path);

            if (isset($this->configuration['dir'])) {
                $dir = substr($dir, strlen(realpath($this->configuration['dir'])) + 1);

                if (false === $dir) {
                    $dir = '';
                }

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
                $newNamespace->clearEmptyTokens();

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
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Classes must be in a path that matches their namespace, be at least one namespace deep and the class name should match the file name.',
            array(
                new FileSpecificCodeSample(
                    '<?php
namespace PhpCsFixer\FIXER\Basic;
class InvalidName {}
',
                    new \SplFileInfo(__FILE__)
                ),
                new FileSpecificCodeSample(
                    '<?php
namespace PhpCsFixer\FIXER\Basic;
class InvalidName {}
',
                    new \SplFileInfo(__FILE__),
                    array('dir' => realpath(__DIR__.'/../..'))
                ),
            ),
            null,
            'This fixer may change you class name, which will break the code that is depended on old name.'
        );
    }
}
