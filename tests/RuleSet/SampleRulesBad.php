<?php
declare(strict_types = 1);

namespace PhpCsFixer\Tests\RuleSet;

/**
 * Sample external RuleSet.
 *
 * This class is not extending the required class `\PhpCsFixer\RuleSet\AbstractRuleSetDescription`,
 * so it will not be a valid class to be registered as a RuleSet.
 */
class SampleRulesBad
{
    public function getName(): string
    {
        return '@RulesBad';
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Description';
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return [
            'align_multiline_comment' => false,
        ];
    }
}
