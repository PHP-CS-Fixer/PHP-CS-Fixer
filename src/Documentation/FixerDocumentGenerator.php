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

use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Differ\FullDiffer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\ExperimentalFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerConfiguration\AliasedFixerOption;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\FixerDefinition\CodeSampleInterface;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\RuleSet\AutomaticRuleSetDefinitionInterface;
use PhpCsFixer\RuleSet\DeprecatedRuleSetDescriptionInterface;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSetDefinitionInterface;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FixerDocumentGenerator
{
    private DocumentationLocator $locator;

    private FullDiffer $differ;

    /** @var array<string, RuleSetDefinitionInterface> */
    private array $ruleSetDefinitions;

    public function __construct(DocumentationLocator $locator)
    {
        $this->locator = $locator;
        $this->differ = new FullDiffer();
        $this->ruleSetDefinitions = RuleSets::getSetDefinitions();
    }

    public function generateFixerDocumentation(FixerInterface $fixer): string
    {
        $name = $fixer->getName();
        $title = "Rule ``{$name}``";
        $titleLine = str_repeat('=', \strlen($title));
        $doc = "{$titleLine}\n{$title}\n{$titleLine}";

        $definition = $fixer->getDefinition();
        $doc .= "\n\n".RstUtils::toRst($definition->getSummary());

        $description = $definition->getDescription();

        if (null !== $description) {
            $description = RstUtils::toRst($description);
            $doc .= <<<RST


                Description
                -----------

                {$description}
                RST;
        }

        $deprecationDescription = '';

        if ($fixer instanceof DeprecatedFixerInterface) {
            $deprecationDescription = <<<'RST'

                This rule is deprecated and will be removed in the next major version
                ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                RST;
            $alternatives = $fixer->getSuccessorsNames();

            if (0 !== \count($alternatives)) {
                $deprecationDescription .= RstUtils::toRst(\sprintf(
                    "\n\nYou should use %s instead.",
                    Utils::naturalLanguageJoinWithBackticks($alternatives)
                ), 0);
            }
        }

        $experimentalDescription = '';

        if ($fixer instanceof ExperimentalFixerInterface) {
            $experimentalDescriptionRaw = RstUtils::toRst('Rule is not covered with backward compatibility promise, use it at your own risk. Rule\'s behaviour may be changed at any point, including rule\'s name; its options\' names, availability and allowed values; its default configuration. Rule may be even removed without prior notice. Feel free to provide feedback and help with determining final state of the rule.', 0);
            $experimentalDescription = <<<RST

                This rule is experimental
                ~~~~~~~~~~~~~~~~~~~~~~~~~

                {$experimentalDescriptionRaw}
                RST;
        }

        $riskyDescription = '';
        $riskyDescriptionRaw = $definition->getRiskyDescription();

        if (null !== $riskyDescriptionRaw) {
            $riskyDescriptionRaw = RstUtils::toRst($riskyDescriptionRaw, 0);
            $riskyDescription = <<<RST

                Using this rule is risky
                ~~~~~~~~~~~~~~~~~~~~~~~~

                {$riskyDescriptionRaw}
                RST;
        }

        if ('' !== $deprecationDescription || '' !== $riskyDescription) {
            $warningsHeader = 'Warning';

            if ('' !== $deprecationDescription && '' !== $riskyDescription) {
                $warningsHeader = 'Warnings';
            }

            $warningsHeaderLine = str_repeat('-', \strlen($warningsHeader));
            $doc .= "\n\n".implode("\n", array_filter(
                [
                    $warningsHeader,
                    $warningsHeaderLine,
                    $deprecationDescription,
                    $experimentalDescription,
                    $riskyDescription,
                ],
                static fn (string $text): bool => '' !== $text
            ));
        }

        if ($fixer instanceof ConfigurableFixerInterface) {
            $doc .= <<<'RST'


                Configuration
                -------------
                RST;

            $configurationDefinition = $fixer->getConfigurationDefinition();

            foreach ($configurationDefinition->getOptions() as $option) {
                $optionInfo = "``{$option->getName()}``";
                $optionInfo .= "\n".str_repeat('~', \strlen($optionInfo));

                if ($option instanceof DeprecatedFixerOptionInterface) {
                    $deprecationMessage = RstUtils::toRst($option->getDeprecationMessage());
                    $optionInfo .= "\n\n.. warning:: This option is deprecated and will be removed in the next major version. {$deprecationMessage}";
                }

                $optionInfo .= "\n\n".RstUtils::toRst($option->getDescription());

                if ($option instanceof AliasedFixerOption) {
                    $optionInfo .= "\n\n.. note:: The previous name of this option was ``{$option->getAlias()}`` but it is now deprecated and will be removed in the next major version.";
                }

                $allowed = HelpCommand::getDisplayableAllowedValues($option);

                if (null === $allowed) {
                    $allowedKind = 'Allowed types';
                    $allowedTypes = $option->getAllowedTypes();
                    if (null !== $allowedTypes) {
                        $allowed = array_map(
                            static fn (string $value): string => '``'.Utils::convertArrayTypeToList($value).'``',
                            $allowedTypes,
                        );
                    }
                } else {
                    $allowedKind = 'Allowed values';
                    $allowed = array_map(static fn ($value): string => $value instanceof AllowedValueSubset
                        ? 'a subset of ``'.Utils::toString($value->getAllowedValues()).'``'
                        : '``'.Utils::toString($value).'``', $allowed);
                }

                if (null !== $allowed) {
                    $allowed = Utils::naturalLanguageJoin($allowed, '');
                    $optionInfo .= "\n\n{$allowedKind}: {$allowed}";
                }

                if ($option->hasDefault()) {
                    $default = Utils::toString($option->getDefault());
                    $optionInfo .= "\n\nDefault value: ``{$default}``";
                } else {
                    $optionInfo .= "\n\nThis option is required.";
                }

                $doc .= "\n\n{$optionInfo}";
            }
        }

        $samples = $definition->getCodeSamples();

        if (0 !== \count($samples)) {
            $doc .= <<<'RST'


                Examples
                --------
                RST;

            foreach ($samples as $index => $sample) {
                $title = \sprintf('Example #%d', $index + 1);
                $titleLine = str_repeat('~', \strlen($title));
                $doc .= "\n\n{$title}\n{$titleLine}";

                if ($fixer instanceof ConfigurableFixerInterface) {
                    if (null === $sample->getConfiguration()) {
                        $doc .= "\n\n*Default* configuration.";
                    } else {
                        $doc .= \sprintf(
                            "\n\nWith configuration: ``%s``.",
                            Utils::toString($sample->getConfiguration())
                        );
                    }
                }

                $doc .= "\n".$this->generateSampleDiff($fixer, $sample, $index + 1, $name);
            }
        }

        $ruleSetConfigs = self::getSetsOfRule($name);

        if ([] !== $ruleSetConfigs) {
            $plural = 1 !== \count($ruleSetConfigs) ? 's' : '';
            $doc .= <<<RST


                Rule sets
                ---------

                The rule is part of the following rule set{$plural}:\n\n
                RST;

            foreach ($ruleSetConfigs as $set => $config) {
                $ruleSetPath = $this->locator->getRuleSetsDocumentationFilePath($set);
                $ruleSetPath = substr($ruleSetPath, strrpos($ruleSetPath, '/'));

                \assert(isset($this->ruleSetDefinitions[$set]));
                $ruleSetDescription = $this->ruleSetDefinitions[$set];

                if ($ruleSetDescription instanceof AutomaticRuleSetDefinitionInterface) {
                    continue;
                }

                $deprecatedDesc = ($ruleSetDescription instanceof DeprecatedRuleSetDescriptionInterface) ? ' *(deprecated)*' : '';

                $configInfo = (null !== $config)
                    ? " with config:\n\n  ``".Utils::toString($config)."``\n"
                    : '';

                $doc .= <<<RST
                    - `{$set} <./../../ruleSets{$ruleSetPath}>`_{$deprecatedDesc}{$configInfo}\n
                    RST;
            }

            $doc = trim($doc);
        }

        $reflectionObject = new \ReflectionObject($fixer);
        $className = str_replace('\\', '\\\\', $reflectionObject->getName());
        $fileName = $reflectionObject->getFileName();
        $fileName = str_replace('\\', '/', $fileName);
        $fileName = substr($fileName, strrpos($fileName, '/src/Fixer/') + 1);
        $fileName = "`{$className} <./../../../{$fileName}>`_";

        $testFileName = Preg::replace('~.*\K/src/(?=Fixer/)~', '/tests/', $fileName);
        $testFileName = Preg::replace('~PhpCsFixer\\\\\\\\\K(?=Fixer\\\\\\\)~', 'Tests\\\\\\\\', $testFileName);
        $testFileName = Preg::replace('~(?= <|\.php>)~', 'Test', $testFileName);

        $doc .= <<<RST


            References
            ----------

            - Fixer class: {$fileName}
            - Test class: {$testFileName}

            The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
            RST;

        $doc = str_replace("\t", '<TAB>', $doc);

        return "{$doc}\n";
    }

    /**
     * @internal
     *
     * @return array<string, null|array<string, mixed>>
     */
    public static function getSetsOfRule(string $ruleName): array
    {
        $ruleSetConfigs = [];

        foreach (RuleSets::getSetDefinitionNames() as $set) {
            $ruleSet = new RuleSet([$set => true]);

            if ($ruleSet->hasRule($ruleName)) {
                $ruleSetConfigs[$set] = $ruleSet->getRuleConfiguration($ruleName);
            }
        }

        return $ruleSetConfigs;
    }

    /**
     * @param list<FixerInterface> $fixers
     */
    public function generateFixersDocumentationIndex(array $fixers): string
    {
        $overrideGroups = [
            'PhpUnit' => 'PHPUnit',
            'PhpTag' => 'PHP Tag',
            'Phpdoc' => 'PHPDoc',
        ];

        usort($fixers, static fn (FixerInterface $a, FixerInterface $b): int => \get_class($a) <=> \get_class($b));

        $documentation = <<<'RST'
            =======================
            List of Available Rules
            =======================
            RST;

        $currentGroup = null;

        foreach ($fixers as $fixer) {
            $namespace = Preg::replace('/^.*\\\(.+)\\\.+Fixer$/', '$1', \get_class($fixer));
            $group = $overrideGroups[$namespace] ?? Preg::replace('/(?<=[[:lower:]])(?=[[:upper:]])/', ' ', $namespace);

            if ($group !== $currentGroup) {
                $underline = str_repeat('-', \strlen($group));
                $documentation .= "\n\n{$group}\n{$underline}\n";

                $currentGroup = $group;
            }

            $path = './'.$this->locator->getFixerDocumentationFileRelativePath($fixer);

            $attributes = [];

            if ($fixer instanceof DeprecatedFixerInterface) {
                $attributes[] = 'deprecated';
            }

            if ($fixer instanceof ExperimentalFixerInterface) {
                $attributes[] = 'experimental';
            }

            if ($fixer->isRisky()) {
                $attributes[] = 'risky';
            }

            $attributes = 0 === \count($attributes)
                ? ''
                : ' *('.implode(', ', $attributes).')*';

            $summary = str_replace('`', '``', $fixer->getDefinition()->getSummary());

            $documentation .= <<<RST

                - `{$fixer->getName()} <{$path}>`_{$attributes}

                  {$summary}
                RST;
        }

        return "{$documentation}\n";
    }

    private function generateSampleDiff(FixerInterface $fixer, CodeSampleInterface $sample, int $sampleNumber, string $ruleName): string
    {
        if ($sample instanceof VersionSpecificCodeSampleInterface && !$sample->isSuitableFor(\PHP_VERSION_ID)) {
            $existingFile = @file_get_contents($this->locator->getFixerDocumentationFilePath($fixer));

            if (false !== $existingFile) {
                Preg::match("/\\RExample #{$sampleNumber}\\R.+?(?<diff>\\R\\.\\. code-block:: diff\\R\\R.*?)\\R(?:\\R\\S|$)/s", $existingFile, $matches);

                if (isset($matches['diff'])) {
                    return $matches['diff'];
                }
            }

            $error = <<<RST

                .. error::
                   Cannot generate diff for code sample #{$sampleNumber} of rule {$ruleName}:
                   the sample is not suitable for current version of PHP (%s).
                RST;

            return \sprintf($error, \PHP_VERSION);
        }

        $old = $sample->getCode();

        $tokens = Tokens::fromCode($old);
        $file = $sample instanceof FileSpecificCodeSampleInterface
            ? $sample->getSplFileInfo()
            : new StdinFileInfo();

        if ($fixer instanceof ConfigurableFixerInterface) {
            $fixer->configure($sample->getConfiguration() ?? []);
        }

        $fixer->fix($file, $tokens);

        $diff = $this->differ->diff($old, $tokens->generateCode());
        $diff = Preg::replace('/@@[ \+\-\d,]+@@\n/', '', $diff);
        $diff = Preg::replace('/\r/', '^M', $diff);
        $diff = Preg::replace('/^ $/m', '', $diff);
        $diff = Preg::replace('/\n$/', '', $diff);
        $diff = RstUtils::indent($diff, 3);

        return <<<RST

            .. code-block:: diff

               {$diff}
            RST;
    }
}
