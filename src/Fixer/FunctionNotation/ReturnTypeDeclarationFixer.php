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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ReturnTypeDeclarationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var string
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->config = 'none';

            return;
        }

        $key = 'space_before';
        $values = array(
            'one',
            'none',
        );

        if (!array_key_exists($key, $configuration) || !in_array($configuration[$key], $values, true)) {
            throw new InvalidFixerConfigurationException(
                $this->getName(),
                sprintf(
                    'Configuration must define "%s" being "%s".',
                    $key,
                    implode('" or "', $values)
                )
            );
        }

        $this->config = $configuration[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(CT::T_TYPE_COLON)) {
                continue;
            }

            $previousToken = $tokens[$index - 1];

            if ($previousToken->isWhitespace()) {
                if ('none' === $this->config) {
                    $previousToken->clear();
                } else {
                    $previousToken->setContent(' ');
                }
            } elseif ('one' === $this->config) {
                $tokens->ensureWhitespaceAtIndex($index, 0, ' ');
                ++$index;
            }

            ++$index;
            $tokens->ensureWhitespaceAtIndex($index, 0, ' ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $versionSpecification = new VersionSpecification(70000);

        return new FixerDefinition(
            'There should be one or no space before colon, and one space after it in return type declarations, according to configuration.',
            array(
                new VersionSpecificCodeSample(
                    "<?php\nfunction foo(int \$a):string {};",
                    $versionSpecification
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction foo(int \$a): string {};",
                    $versionSpecification,
                    array('space_before' => 'none')
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction foo(int \$a): string {};",
                    $versionSpecification,
                    array('space_before' => 'one')
                ),
            ),
            null,
            "Configuration must have one element 'space_before' with value 'none' (default) or 'one'.",
            array('space_before' => 'none')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(CT::T_TYPE_COLON);
    }
}
