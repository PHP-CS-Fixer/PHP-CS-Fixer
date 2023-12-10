<?php

declare(strict_types=1);

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

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\Indentation;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;

/**
 * @deprecated
 */
final class CurlyBracesPositionFixer extends AbstractProxyFixer implements ConfigurableFixerInterface, DeprecatedFixerInterface, WhitespacesAwareFixerInterface
{
    use Indentation;

    /**
     * @internal
     */
    public const NEXT_LINE_UNLESS_NEWLINE_AT_SIGNATURE_END = 'next_line_unless_newline_at_signature_end';

    /**
     * @internal
     */
    public const SAME_LINE = 'same_line';

    private BracesPositionFixer $bracesPositionFixer;

    public function __construct()
    {
        $this->bracesPositionFixer = new BracesPositionFixer();

        parent::__construct();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        $fixerDefinition = $this->bracesPositionFixer->getDefinition();

        return new FixerDefinition(
            'Curly braces must be placed as configured.',
            $fixerDefinition->getCodeSamples(),
            $fixerDefinition->getDescription(),
            $fixerDefinition->getRiskyDescription()
        );
    }

    public function configure(array $configuration): void
    {
        $this->bracesPositionFixer->configure($configuration);

        parent::configure($configuration);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before SingleLineEmptyBodyFixer, StatementIndentationFixer.
     * Must run after ControlStructureBracesFixer, NoMultipleStatementsPerLineFixer.
     */
    public function getPriority(): int
    {
        return $this->bracesPositionFixer->getPriority();
    }

    public function getSuccessorsNames(): array
    {
        return [
            $this->bracesPositionFixer->getName(),
        ];
    }

    protected function createProxyFixers(): array
    {
        return [
            $this->bracesPositionFixer,
        ];
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return $this->bracesPositionFixer->createConfigurationDefinition();
    }
}
