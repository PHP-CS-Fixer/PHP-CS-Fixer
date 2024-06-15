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

namespace PhpCsFixer\Documentation;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\RuleSet\DeprecatedRuleSetDescriptionInterface;
use PhpCsFixer\RuleSet\RuleSetDescriptionInterface;
use PhpCsFixer\Utils;

/**
 * @internal
 */
final class RuleSetDocumentationGenerator
{
    private DocumentationLocator $locator;

    public function __construct(DocumentationLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param list<FixerInterface> $fixers
     */
    public function generateRuleSetsDocumentation(RuleSetDescriptionInterface $definition, array $fixers): string
    {
        $fixerNames = [];

        foreach ($fixers as $fixer) {
            $fixerNames[$fixer->getName()] = $fixer;
        }

        $title = "Rule set ``{$definition->getName()}``";
        $titleLine = str_repeat('=', \strlen($title));
        $doc = "{$titleLine}\n{$title}\n{$titleLine}\n\n".$definition->getDescription();

        $warnings = [];
        if ($definition instanceof DeprecatedRuleSetDescriptionInterface) {
            $deprecationDescription = <<<'RST'

                This rule set is deprecated and will be removed in the next major version
                ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                RST;
            $alternatives = $definition->getSuccessorsNames();

            if (0 !== \count($alternatives)) {
                $deprecationDescription .= RstUtils::toRst(
                    sprintf(
                        "\n\nYou should use %s instead.",
                        Utils::naturalLanguageJoinWithBackticks($alternatives)
                    ),
                    0
                );
            } else {
                $deprecationDescription .= 'No replacement available.';
            }

            $warnings[] = $deprecationDescription;
        }

        if ($definition->isRisky()) {
            $warnings[] = <<<'RST'

                This set contains rules that are risky
                ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

                Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.
                RST;
        }

        if ([] !== $warnings) {
            $warningsHeader = 1 === \count($warnings) ? 'Warning' : 'Warnings';

            $warningsHeaderLine = str_repeat('-', \strlen($warningsHeader));
            $doc .= "\n\n".implode(
                "\n",
                [
                    $warningsHeader,
                    $warningsHeaderLine,
                    ...$warnings,
                ]
            );
        }

        $rules = $definition->getRules();

        if ([] === $rules) {
            $doc .= "\n\nThis is an empty set.";
        } else {
            $enabledRules = array_filter($rules, static fn ($config) => false !== $config);
            $disabledRules = array_filter($rules, static fn ($config) => false === $config);

            $listRules = function (array $rules) use (&$doc, $fixerNames): void {
                foreach ($rules as $rule => $config) {
                    if (str_starts_with($rule, '@')) {
                        $ruleSetPath = $this->locator->getRuleSetsDocumentationFilePath($rule);
                        $ruleSetPath = substr($ruleSetPath, strrpos($ruleSetPath, '/'));

                        $doc .= "\n- `{$rule} <.{$ruleSetPath}>`_";
                    } else {
                        $path = Preg::replace(
                            '#^'.preg_quote($this->locator->getFixersDocumentationDirectoryPath(), '#').'/#',
                            './../rules/',
                            $this->locator->getFixerDocumentationFilePath($fixerNames[$rule])
                        );

                        $doc .= "\n- `{$rule} <{$path}>`_";
                    }

                    if (!\is_bool($config)) {
                        $doc .= " with config:\n\n  ``".Utils::toString($config)."``\n";
                    }
                }
            };

            if ([] !== $enabledRules) {
                $doc .= "\n\nRules\n-----\n";
                $listRules($enabledRules);
            }

            if ([] !== $disabledRules) {
                $doc .= "\n\nDisabled rules\n--------------\n";
                $listRules($disabledRules);
            }
        }

        return $doc."\n";
    }

    /**
     * @param array<string, RuleSetDescriptionInterface> $setDefinitions
     */
    public function generateRuleSetsDocumentationIndex(array $setDefinitions): string
    {
        $documentation = <<<'RST'
            ===========================
            List of Available Rule sets
            ===========================
            RST;

        foreach ($setDefinitions as $path => $definition) {
            $path = substr($path, strrpos($path, '/'));

            $attributes = [];

            if ($definition instanceof DeprecatedRuleSetDescriptionInterface) {
                $attributes[] = 'deprecated';
            }

            $attributes = 0 === \count($attributes)
                ? ''
                : ' *('.implode(', ', $attributes).')*';

            $documentation .= "\n- `{$definition->getName()} <.{$path}>`_{$attributes}";
        }

        return $documentation."\n";
    }
}
