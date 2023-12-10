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
     * @param FixerInterface[] $fixers
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

        if ($definition->isRisky()) {
            $doc .= ' This set contains rules that are risky.';
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
     * @param array<string, string> $setDefinitions
     */
    public function generateRuleSetsDocumentationIndex(array $setDefinitions): string
    {
        $documentation = <<<'RST'
            ===========================
            List of Available Rule sets
            ===========================
            RST;
        foreach ($setDefinitions as $name => $path) {
            $path = substr($path, strrpos($path, '/'));
            $documentation .= "\n- `{$name} <.{$path}>`_";
        }

        return $documentation."\n";
    }
}
