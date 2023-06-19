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

namespace PhpCsFixer\RuleSet;

use PhpCsFixer\Preg;
use Symfony\Component\Finder\Finder;

/**
 * Set of rule sets to be used by fixer.
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RuleSets
{
    /**
     * @var null|array<string, RuleSetDescriptionInterface>
     */
    private static ?array $setDefinitions = null;

    /**
     * @return array<string, RuleSetDescriptionInterface>
     */
    public static function getSetDefinitions(): array
    {
        if (null === self::$setDefinitions) {
            self::$setDefinitions = [];

            foreach (Finder::create()->files()->in(__DIR__.'/Sets') as $file) {
                $class = 'PhpCsFixer\RuleSet\Sets\\'.$file->getBasename('.php');

                /** @var RuleSetDescriptionInterface */
                $set = new $class();

                self::$setDefinitions[$set->getName()] = $set;
            }

            self::sortSetDefinitions();
        }

        return self::$setDefinitions;
    }

    /**
     * @return list<string>
     */
    public static function getSetDefinitionNames(): array
    {
        return array_keys(self::getSetDefinitions());
    }

    public static function getSetDefinition(string $name): RuleSetDescriptionInterface
    {
        $definitions = self::getSetDefinitions();

        if (!isset($definitions[$name])) {
            throw new \InvalidArgumentException(\sprintf('Set "%s" does not exist.', $name));
        }

        return $definitions[$name];
    }

    /**
     * @param class-string<RuleSetDescriptionInterface> $class
     */
    public static function registerRuleSet(string $name, string $class): void
    {
        $preg = new Preg();

        if (1 !== $preg->match('/^@[a-zA-Z][a-zA-Z0-9_\/\.-]*$/', $name)) {
            throw new \InvalidArgumentException('RuleSet name must begin with "@" and a letter (a-z, A-Z), and can contain only letters (a-z, A-Z), numbers, underscores, slashes, dots and hyphens.');
        }

        if (!class_exists($class, true)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $preDefinedDefinitions = self::getSetDefinitions();

        if (\array_key_exists($name, $preDefinedDefinitions)) {
            throw new \InvalidArgumentException(sprintf('Set "%s" is already defined.', $name));
        }

        if (!\in_array(RuleSetDescriptionInterface::class, class_implements($class), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Class "%s" must be an instance of "%s".',
                    $class,
                    RuleSetDescriptionInterface::class
                )
            );
        }

        self::$setDefinitions[$name] = new $class();

        self::sortSetDefinitions();
    }

    private static function sortSetDefinitions(): void
    {
        uksort(self::$setDefinitions, static fn (string $x, string $y): int => strnatcmp($x, $y));
    }
}
