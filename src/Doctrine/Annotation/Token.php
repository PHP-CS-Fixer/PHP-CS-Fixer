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

namespace PhpCsFixer\Doctrine\Annotation;

use Doctrine\Common\Annotations\DocLexer;

/**
 * A Doctrine annotation token.
 *
 * @internal
 */
final class Token
{
    private int $type;

    private string $content;

    /**
     * @param int    $type    The type
     * @param string $content The content
     */
    public function __construct(int $type = DocLexer::T_NONE, string $content = '')
    {
        $this->type = $type;
        $this->content = $content;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Returns whether the token type is one of the given types.
     *
     * @param int|int[] $types
     */
    public function isType($types): bool
    {
        if (!\is_array($types)) {
            $types = [$types];
        }

        return \in_array($this->getType(), $types, true);
    }

    /**
     * Overrides the content with an empty string.
     */
    public function clear(): void
    {
        $this->setContent('');
    }
}
