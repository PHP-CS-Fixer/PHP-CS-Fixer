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

namespace PhpCsFixer\FixerBlame;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder;

final class FixerBlame
{
    private Differ $differ;

    /**
     * @var array<array{
     *     fixerName: string,
     *     source: string
     *     }>
     */
    private array $changeStack = [];

    public function __construct()
    {
        $this->differ = new Differ(new StrictUnifiedDiffOutputBuilder([
            'collapseRanges' => true,
            'commonLineThreshold' => 1,
            'contextLines' => 1,
            'fromFile' => 'Original',
            'toFile' => 'New',
        ]));
    }

    /**
     * @param string|Tokens $code
     */
    public function originalCode($code): void
    {
        if ($code instanceof Tokens) {
            $code = $code->generateCode();
        }

        $this->changeStack = [
            [
                'fixerName' => '__initial__',
                'source' => $code,
            ],
        ];
    }

    public function snapshotCode(string $fixerName, string $source): void
    {
        $this->changeStack[] = [
            'fixerName' => $fixerName,
            'source' => $source,
        ];
    }

    public function snapshotTokens(FixerInterface $fixer, Tokens $tokens): void
    {
        $this->changeStack[] = [
            'fixerName' => $fixer->getName(),
            'source' => $tokens->generateCode(),
        ];
    }

    /**
     * @return list<FixerChange>
     */
    public function calculateChanges(): array
    {
        $changes = [];

        foreach ($this->changeStack as $changeIndex => $change) {
            if (0 === $changeIndex) {
                continue;
            }

            $oldChangeContent = $this->changeStack[$changeIndex - 1]['source'];
            $newChangeContent = $change['source'];

            $fixerName = $change['fixerName'];

            $diffResults = $this->diff($oldChangeContent, $newChangeContent);
            $patches = $this->findPatches($diffResults);

            foreach ($patches as $patchInfo) {
                $patchContent = $patchInfo->getPatchContent($diffResults);

                $numberOfChanges = \count($patchContent);

                // simple remove
                if (1 === $numberOfChanges && Differ::REMOVED === $patchContent[0]->getChange()) {
                    $changes[] = [
                        'fixerName' => $fixerName,
                        'start' => $patchContent[0]->getOldLineNumber(),
                        'changedSum' => $patchInfo->getChangeSum(),
                        'changedAt' => 0,
                    ];

                    continue;
                }

                // line changed
                if (2 === $numberOfChanges && Differ::REMOVED === $patchContent[0]->getChange() && Differ::ADDED === $patchContent[1]->getChange()) {
                    $addedLine = $patchContent[1]->getContent();
                    $removedLine = $patchContent[0]->getContent();

                    $changedAt = null;

                    for ($i = 0; $i < min(\strlen($addedLine), \strlen($removedLine)); ++$i) {
                        if ($addedLine[$i] !== $removedLine[$i]) {
                            $changedAt = $i + 1;

                            break;
                        }
                    }

                    $changes[] = [
                        'fixerName' => $fixerName,
                        'start' => $patchContent[0]->getOldLineNumber(),
                        'changedSum' => $patchInfo->getChangeSum(),
                        'changedAt' => $changedAt ?? \strlen($removedLine) + 1,
                    ];

                    continue;
                }

                $onlyRemove = 0x1;
                $onlyAdd = 0x1;

                foreach ($patchContent as $patchRow) {
                    if (Differ::ADDED === $patchRow->getChange()) {
                        $onlyAdd &= 0x1;
                    } else {
                        $onlyAdd &= 0;
                    }

                    if (Differ::REMOVED === $patchRow->getChange()) {
                        $onlyRemove &= 0x1;
                    } else {
                        $onlyRemove &= 0;
                    }
                }

                if (1 === $onlyAdd xor 1 === $onlyRemove) {
                    if (1 === $onlyAdd) {
                        $lineNumber = $patchContent[0]->getNewLineNumber();
                    } else {
                        $lineNumber = $patchContent[0]->getOldLineNumber();
                    }

                    $changes[] = [
                        'fixerName' => $fixerName,
                        'start' => $lineNumber,
                        'changedSum' => $patchInfo->getChangeSum(),
                        'changedAt' => 0,
                    ];

                    continue;
                }
                if (Differ::ADDED === $patchContent[0]->getChange()) {
                    throw new \RuntimeException('added lines first?');
                }

                $changes[] = [
                    'fixerName' => $fixerName,
                    'start' => $patchContent[0]->getOldLineNumber(),
                    'changedSum' => $patchInfo->getChangeSum(),
                    'changedAt' => 0,
                ];
            }
        }

        $changeSet = [];
        foreach ($changes as $index => $change) {
            $lineChanges = 0;
            for ($i = $index - 1; $i >= 0; --$i) {
                if ($changes[$i]['start'] >= $change['start']) {
                    continue;
                }

                $lineChanges -= $changes[$i]['changedSum'];
            }

            $changeSet[] = new FixerChange($change['fixerName'], $change['start'] + $lineChanges, $change['changedAt']);
        }

        return $changeSet;
    }

    /**
     * @return array<CodeChange>
     */
    private function diff(string $oldCode, string $newCode): array
    {
        $diffResults = $this->differ->diffToArray($oldCode, $newCode);

        $linePointerInOldContent = 1;
        $linePointerInNewContent = 1;

        $buffer = [];
        foreach ($diffResults as $diffResult) {
            if (Differ::ADDED === $diffResult[1]) {
                $buffer[] = new CodeChange($diffResult[0], Differ::ADDED, $linePointerInNewContent++);

                continue;
            }

            if (Differ::REMOVED === $diffResult[1]) {
                $buffer[] = new CodeChange($diffResult[0], Differ::REMOVED, null, $linePointerInOldContent++);

                continue;
            }

            $buffer[] = new CodeChange($diffResult[0], Differ::OLD, $linePointerInNewContent++, $linePointerInOldContent++);
        }

        return $buffer;
    }

    /**
     * @param array<CodeChange> $diffs
     *
     * @return array<PatchInfo>
     */
    private function findPatches(array $diffs): array
    {
        /** @var array<PatchInfo> $patches */
        $patches = [];
        $patchInfo = null;
        $state = 'file_start';

        foreach ($diffs as $key => $diffResult) {
            if ('file_start' === $state) {
                if (Differ::OLD === $diffResult->getChange()) {
                    $state = 'between_patch';

                    continue;
                }

                if (Differ::ADDED === $diffResult->getChange() || Differ::REMOVED === $diffResult->getChange()) {
                    $patchInfo = new PatchInfo();
                    $patchInfo->setStartKey($key);
                    $patchInfo->countChange($diffResult->getChange());

                    $state = 'in_patch';

                    continue;
                }
            }

            if ('between_patch' === $state && (Differ::ADDED === $diffResult->getChange() || Differ::REMOVED === $diffResult->getChange())) {
                $patchInfo = new PatchInfo();
                $patchInfo->setStartKey($key);
                $patchInfo->countChange($diffResult->getChange());

                $state = 'in_patch';

                continue;
            }

            if ('in_patch' === $state && Differ::OLD === $diffResult->getChange()) {
                $state = 'between_patch';

                $patchInfo->setEndKey($key);
                $patches[] = $patchInfo;
                $patchInfo = null;

                continue;
            }

            if ('in_patch' === $state) {
                $patchInfo->countChange($diffResult->getChange());
            }
        }

        if ('in_patch' === $state) {
            $patchInfo->setEndKey(\count($diffs) - 1);
            $patches[] = $patchInfo;
            $patchInfo = null;
        }

        return $patches;
    }
}
