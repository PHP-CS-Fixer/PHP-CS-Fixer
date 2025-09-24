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
use PhpCsFixer\RuleSet\AutomaticRuleSetDefinitionInterface;
use PhpCsFixer\RuleSet\DeprecatedRuleSetDefinitionInterface;
use PhpCsFixer\RuleSet\RuleSetDefinitionInterface;
use PhpCsFixer\Utils;

/**
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
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
    public function generateRuleSetsDocumentation(RuleSetDefinitionInterface $definition, array $fixers): string
    {
        $fixerNames = [];

        foreach ($fixers as $fixer) {
            $fixerNames[$fixer->getName()] = $fixer;
        }

        $title = "Rule set ``{$definition->getName()}``";
        $titleLine = str_repeat('=', \strlen($title));
        $doc = "{$titleLine}\n{$title}\n{$titleLine}\n\n".$definition->getDescription();

        $warnings = [];
        if ($definition instanceof DeprecatedRuleSetDefinitionInterface) {
            $deprecationDescription = <<<'RST'

                This rule set is deprecated and will be removed in the next major version
                ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                RST;
            $alternatives = $definition->getSuccessorsNames();

            if (0 !== \count($alternatives)) {
                $deprecationDescription .= RstUtils::toRst(
                    \sprintf(
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

        $header = static function (string $message, string $underline = '-'): string {
            $line = str_repeat($underline, \strlen($message));

            return "{$message}\n{$line}\n";
        };

        if ($definition instanceof AutomaticRuleSetDefinitionInterface) {
            $warnings[] = "\n".$header('Automatic rule set', '~')."\n⚡ ".strip_tags(AutomaticRuleSetDefinitionInterface::WARNING_MESSAGE_DECORATED);
        }

        if ([] !== $warnings) {
            $warningsHeader = 1 === \count($warnings) ? 'Warning' : 'Warnings';

            $doc .= "\n\n".$header($warningsHeader).implode("\n", $warnings);
        }

        $rules = $definition instanceof AutomaticRuleSetDefinitionInterface
                ? $definition->getRulesCandidates()
                : $definition->getRules();

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

            $rulesCandidatesDescriptionHeader = $definition instanceof AutomaticRuleSetDefinitionInterface
                ? ' candidates'
                : '';

            if ([] !== $enabledRules) {
                $doc .= "\n\n".$header("Rules{$rulesCandidatesDescriptionHeader}");
                $listRules($enabledRules);
            }

            if ([] !== $disabledRules) {
                $doc .= "\n\n".$header("Disabled rules{$rulesCandidatesDescriptionHeader}");

                $listRules($disabledRules);
            }
        }

        return $doc."\n";
    }

    /**
     * @param array<string, RuleSetDefinitionInterface> $setDefinitions
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

            if ($definition instanceof DeprecatedRuleSetDefinitionInterface) {
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
