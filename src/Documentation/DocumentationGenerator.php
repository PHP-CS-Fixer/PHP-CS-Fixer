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

namespace PhpCsFixer\Documentation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Diff\GeckoPackages\DiffOutputBuilder\UnifiedDiffOutputBuilder;
use PhpCsFixer\Diff\v2_0\Differ;
use PhpCsFixer\Fixer\Basic\Psr0Fixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerConfiguration\AliasedFixerOption;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\FixerDefinition\CodeSampleInterface;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\RuleSet;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @internal
 */
final class DocumentationGenerator
{
    /**
     * @var Differ
     */
    private $differ;

    private $path;

    public function __construct()
    {
        $this->differ = new Differ(new UnifiedDiffOutputBuilder([
            'fromFile' => 'Original',
            'toFile' => 'New',
        ]));

        $this->path = \dirname(\dirname(__DIR__)).'/doc/rules';
    }

    /**
     * @return string
     */
    public function getFixersDocumentationDirectoryPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getFixersDocumentationIndexFilePath()
    {
        return "{$this->path}/index.rst";
    }

    /**
     * @param AbstractFixer[] $fixers
     *
     * @return string
     */
    public function generateFixersDocumentationIndex(array $fixers)
    {
        $overrideGroups = [
            'PhpUnit' => 'PHPUnit',
            'PhpTag' => 'PHP Tag',
            'Phpdoc' => 'PHPDoc',
        ];

        usort($fixers, function (FixerInterface $a, FixerInterface $b) {
            return strcmp(\get_class($a), \get_class($b));
        });

        $documentation = <<<'RST'
=======================
List of Available Rules
=======================
RST;

        $currentGroup = null;
        foreach ($fixers as $fixer) {
            $namespace = Preg::replace('/^.*\\\\(.+)\\\\.+Fixer$/', '$1', \get_class($fixer));
            if (isset($overrideGroups[$namespace])) {
                $group = $overrideGroups[$namespace];
            } else {
                $group = Preg::replace('/(?<=[[:lower:]])(?=[[:upper:]])/', ' ', $namespace);
            }

            if ($group !== $currentGroup) {
                $underline = str_repeat('-', \strlen($group));
                $documentation .= "\n\n{$group}\n{$underline}\n";

                $currentGroup = $group;
            }

            $summary = str_replace('`', '``', $fixer->getDefinition()->getSummary());

            $attributes = [];
            if ($fixer instanceof DeprecatedFixerInterface) {
                $attributes[] = 'deprecated';
            }
            if ($fixer->isRisky()) {
                $attributes[] = 'risky';
            }

            if ([] !== $attributes) {
                $attributes = ' *('.implode(', ', $attributes).')*';
            } else {
                $attributes = '';
            }

            $path = Preg::replace(
                '#^'.preg_quote($this->path, '#').'/#',
                './',
                $this->getFixerDocumentationFilePath($fixer)
            );

            $documentation .= <<<RST

- `{$fixer->getName()} <{$path}>`_{$attributes}
    {$summary}
RST;
        }

        return "{$documentation}\n";
    }

    /**
     * @return string
     */
    public function getFixerDocumentationFilePath(FixerInterface $fixer)
    {
        return $this->path.'/'.Preg::replaceCallback(
            '/^.*\\\\(.+)\\\\(.+)Fixer$/',
            function (array $matches) {
                return Utils::camelCaseToUnderscore($matches[1]).'/'.Utils::camelCaseToUnderscore($matches[2]);
            },
            \get_class($fixer)
        ).'.rst';
    }

    /**
     * @return string
     */
    public function generateFixerDocumentation(FixerInterface $fixer)
    {
        $name = $fixer->getName();
        $title = "Rule ``{$name}``";
        $titleLine = str_repeat('=', \strlen($title));

        $doc = "{$titleLine}\n{$title}\n{$titleLine}";

        if ($fixer instanceof DeprecatedFixerInterface) {
            $doc .= "\n\n.. warning:: This rule is deprecated and will be removed on next major version.";

            $alternatives = $fixer->getSuccessorsNames();
            if ([] !== $alternatives) {
                $doc .= $this->toRst(sprintf(
                    "\n\nYou should use %s instead.",
                    Utils::naturalLanguageJoinWithBackticks($alternatives)
                ), 3);
            }
        }

        $riskyDescription = null;
        $samples = [];

        if ($fixer instanceof DefinedFixerInterface) {
            $definition = $fixer->getDefinition();

            $doc .= "\n\n".$this->toRst($definition->getSummary());

            $description = $definition->getDescription();
            if (null !== $description) {
                $description = $this->toRst($description);
                $doc .= <<<RST


Description
-----------

{$description}
RST;
            }

            $riskyDescription = $definition->getRiskyDescription();
            $samples = $definition->getCodeSamples();
        } elseif ($fixer->isRisky()) {
            $riskyDescription = 'Changes applied by the rule to your code might change its behavior.';
        }

        if (null !== $riskyDescription) {
            $riskyDescription = $this->toRst($riskyDescription, 3);

            $doc .= <<<RST


.. warning:: Using this rule is risky.

   {$riskyDescription}
RST;
        }

        if ($fixer instanceof ConfigurationDefinitionFixerInterface) {
            $doc .= <<<'RST'


Configuration
-------------
RST;

            $configurationDefinition = $fixer->getConfigurationDefinition();

            foreach ($configurationDefinition->getOptions() as $option) {
                $optionInfo = "``{$option->getName()}``";
                $optionInfo .= "\n".str_repeat('~', \strlen($optionInfo));

                if ($option instanceof DeprecatedFixerOptionInterface) {
                    $optionInfo .= "\n\n.. warning:: This option is deprecated and will be removed on next major version. {$this->toRst($option->getDeprecationMessage())}";
                }

                $optionInfo .= "\n\n".$this->toRst($option->getDescription());

                if ($option instanceof AliasedFixerOption) {
                    $optionInfo .= "\n\n.. note:: The previous name of this option was ``{$option->getAlias()}`` but it is now deprecated and will be removed on next major version.";
                }

                $allowed = HelpCommand::getDisplayableAllowedValues($option);
                $allowedKind = 'Allowed values';
                if (null !== $allowed) {
                    foreach ($allowed as &$value) {
                        if ($value instanceof AllowedValueSubset) {
                            $value = 'a subset of ``'.HelpCommand::toString($value->getAllowedValues()).'``';
                        } else {
                            $value = '``'.HelpCommand::toString($value).'``';
                        }
                    }
                } else {
                    $allowedKind = 'Allowed types';
                    $allowed = array_map(function ($value) {
                        return '``'.$value.'``';
                    }, $option->getAllowedTypes());
                }

                if (null !== $allowed) {
                    $allowed = implode(', ', $allowed);
                    $optionInfo .= "\n\n{$allowedKind}: {$allowed}";
                }

                if ($option->hasDefault()) {
                    $default = HelpCommand::toString($option->getDefault());
                    $optionInfo .= "\n\nDefault value: ``{$default}``";
                } else {
                    $optionInfo .= "\n\nThis option is required.";
                }

                $doc .= "\n\n{$optionInfo}";
            }
        } elseif ($fixer instanceof ConfigurableFixerInterface) {
            $doc .= "\n\nThis rule is configurable.";
        }

        if (0 !== \count($samples)) {
            $doc .= <<<'RST'


Examples
--------
RST;

            foreach ($samples as $index => $sample) {
                $title = sprintf('Example #%d', $index + 1);
                $titleLine = str_repeat('~', \strlen($title));
                $doc .= "\n\n{$title}\n{$titleLine}";

                if ($fixer instanceof ConfigurableFixerInterface) {
                    if (null === $sample->getConfiguration()) {
                        $doc .= "\n\n*Default* configuration.";
                    } else {
                        $doc .= sprintf(
                            "\n\nWith configuration: ``%s``.",
                            HelpCommand::toString($sample->getConfiguration())
                        );
                    }
                }

                $doc .= "\n".$this->generateSampleDiff($fixer, $sample, $index, $name);
            }
        }

        $ruleSetConfigs = [];

        foreach ((new RuleSet())->getSetDefinitionNames() as $set) {
            $ruleSet = new RuleSet([$set => true]);

            if ($ruleSet->hasRule($name)) {
                $ruleSetConfigs[$set] = $ruleSet->getRuleConfiguration($name);
            }
        }

        if ([] !== $ruleSetConfigs) {
            $plural = 1 !== \count($ruleSetConfigs) ? 's' : '';
            $doc .= <<<RST


Rule sets
---------

The rule is part of the following rule set{$plural}:
RST;

            foreach ($ruleSetConfigs as $set => $config) {
                $doc .= <<<RST


{$set}
  Using the ``{$set}`` rule set will enable the ``{$name}`` rule
RST;

                if (null !== $config) {
                    $doc .= " with the config below:\n\n  ``".HelpCommand::toString($config).'``';
                } elseif ($fixer instanceof ConfigurationDefinitionFixerInterface) {
                    $doc .= ' with the default config.';
                } else {
                    $doc .= '.';
                }
            }
        }

        return "{$doc}\n";
    }

    private function generateSampleDiff(FixerInterface $fixer, CodeSampleInterface $sample, $sampleIndex, $ruleName)
    {
        if ($sample instanceof VersionSpecificCodeSampleInterface && !$sample->isSuitableFor(\PHP_VERSION_ID)) {
            $error = <<<RST

.. error::
   Cannot generate diff for code sample #{$sampleIndex} of rule {$ruleName}:
   the sample is not suitable for current version of PHP (%s).
RST;

            return sprintf($error, \PHP_VERSION);
        }

        $old = $sample->getCode();

        $tokens = Tokens::fromCode($old);
        $file = $sample instanceof FileSpecificCodeSampleInterface
            ? $sample->getSplFileInfo()
            : new StdinFileInfo()
        ;

        if ($fixer instanceof ConfigurableFixerInterface) {
            $configuration = $sample->getConfiguration();

            if (null === $configuration) {
                $configuration = [];
            }

            if ($fixer instanceof Psr0Fixer && isset($configuration['dir']) && 0 === strpos($configuration['dir'], './')) {
                // Psr0Fixer relies on realpath() which fails for directories
                // relative to some path when the working directory is a
                // different path. Using an absolute path prevents this issue.
                $configuration['dir'] = \dirname(\dirname(__DIR__)).substr($configuration['dir'], 1);
            }

            $fixer->configure($configuration);
        }

        $fixer->fix($file, $tokens);

        $diff = $this->differ->diff($old, $tokens->generateCode());
        $diff = Preg::replace('/\r/', '^M', $diff);
        $diff = Preg::replace('/^ $/m', '', $diff);
        $diff = Preg::replace('/\n$/', '', $diff);

        return <<<RST

.. code-block:: diff

   {$this->indent($diff, 3)}
RST;
    }

    private function toRst($string, $indent = 0)
    {
        $string = wordwrap(Preg::replace('/(?<!`)(`.*?`)(?!`)/', '`$1`', $string), 80 - $indent);

        if (0 !== $indent) {
            $string = $this->indent($string, $indent);
        }

        return $string;
    }

    private function indent($string, $indent)
    {
        return Preg::replace('/(\n)(?!\n|$)/', '$1'.str_repeat(' ', $indent), $string);
    }
}
