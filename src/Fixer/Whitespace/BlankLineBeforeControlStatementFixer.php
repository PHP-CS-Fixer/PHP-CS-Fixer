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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 */
final class BlankLineBeforeControlStatementFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var array
     */
    private static $defaultConfiguration = [
        'return',
    ];

    /**
     * @var array
     */
    private static $tokenMap = [
        'break' => T_BREAK,
        'continue' => T_CONTINUE,
        'declare' => T_DECLARE,
        'do' => T_DO,
        'else' => T_ELSE,
        'elseif' => T_ELSEIF,
        'for' => T_FOR,
        'foreach' => T_FOREACH,
        'if' => T_IF,
        'include' => T_INCLUDE,
        'include_once' => T_INCLUDE_ONCE,
        'require' => T_REQUIRE,
        'require_once' => T_REQUIRE_ONCE,
        'return' => T_RETURN,
        'switch' => T_SWITCH,
        'throw' => T_THROW,
        'try' => T_TRY,
        'while' => T_WHILE,
    ];

    /**
     * @var array
     */
    private $fixTokenMap = [];

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $configuration = self::$defaultConfiguration;
        }

        foreach ($configuration as $controlStatement) {
            if (!is_string($controlStatement) || !array_key_exists($controlStatement, self::$tokenMap)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf(
                    'Expected one of "%s", got "%s".',
                    implode('", "', array_keys(self::$tokenMap)),
                    is_object($controlStatement) ? get_class($controlStatement) : gettype($controlStatement)
                ));
            }

            $this->fixTokenMap[$controlStatement] = self::$tokenMap[$controlStatement];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(array_values($this->fixTokenMap))) {
                continue;
            }

            $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

            if (!$prevNonWhitespaceToken->equalsAny([';', '}'])) {
                continue;
            }

            $prevToken = $tokens[$index - 1];

            if ($prevToken->isWhitespace()) {
                $parts = explode("\n", $prevToken->getContent());
                $countParts = count($parts);

                if (1 === $countParts) {
                    $prevToken->setContent(rtrim($prevToken->getContent(), " \t").$lineEnding.$lineEnding);
                } elseif (count($parts) <= 2) {
                    $prevToken->setContent($lineEnding.$prevToken->getContent());
                }
            } else {
                $tokens->insertAt($index, new Token([T_WHITESPACE, $lineEnding.$lineEnding]));

                ++$index;
                ++$limit;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'An empty line feed should precede a control statement.',
            [
                new CodeSample(
                    '<?php
function A() {
    echo 1;
    return 1;
}'
                ),
                new CodeSample(
                    '<?php
switch ($foo) {
    case 42:
        $bar->process();
        break;
    case 44:
        break;
}',
                    [
                        'break',
                    ]
                ),
                new CodeSample(
                    '<?php
foreach ($foo as $bar) {
    if ($bar->isTired()) {
        $bar->sleep();
        continue;
    }
}',
                    [
                        'continue',
                    ]
                ),
                new CodeSample(
                    '<?php
$i = 0;
do {
    echo $i;
} while ($i > 0);
',
                    [
                        'do',
                    ]
                ),
                new CodeSample(
                    '<?php
$a = 9000;
if (true) {
    $foo = $bar;
}',
                    [
                        'if',
                    ]
                ),
                new CodeSample(
                    '<?php

if (true) {
    $foo = $bar;
    return;
}',
                    [
                        'return',
                    ]
                ),
                new CodeSample(
                    '<?php
$a = 9000;
switch ($a) {
    case 42:
        break;
}',
                    [
                        'switch',
                    ]
                ),
                new CodeSample(
'<?php
if (null === $a) {
    $foo->bar();
    throw new \UnexpectedValueException("A cannot be null");
}',
                    [
                        'throw',
                    ]
                ),
                new CodeSample(
'<?php
$a = 9000;
try {
    $foo->bar();
} catch (\Exception $exception) {
    $a = -1;
}',
                    [
                        'try',
                    ]
                ),
            ],
            null,
            sprintf(
                'Provide an array with any of "%s".',
                implode('", "', array_keys(self::$tokenMap))
            ),
            self::$defaultConfiguration
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after NoUselessReturnFixer
        return -19;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array_values($this->fixTokenMap));
    }
}
