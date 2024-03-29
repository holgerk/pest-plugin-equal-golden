<?php

declare(strict_types=1);

namespace Holgerk\EqualGolden;

// use Pest\Contracts\Plugins\AddsOutput;
use Pest\Contracts\Plugins\Terminable;
// use Pest\Contracts\Plugins\HandlesArguments;

/** @internal */
final class Plugin implements Terminable
{
    /** @var Insertion[] */
    private static array $insertions = [];

    public static function registerInsertion(Insertion $insertion): void
    {
        self::$insertions[] = $insertion;
    }

    public function terminate(): void
    {
        $this->writeAndResetInsertions();
    }

    private function writeAndResetInsertions(): void
    {
        $insertions = self::$insertions;
        self::$insertions = [];

        // sort insertions from end of file to the beginning, so we do net mess up the positions
        // through the replacements
        usort($insertions, function (Insertion $a, Insertion $b): int {
            if ($a->file !== $b->file) {
                return $a->file <=> $b->file;
            }

            return $b->startPos <=> $a->startPos;
        });
        foreach ($insertions as $insertion) {
            $content = file_get_contents($insertion->file);

            // detect indention
            $indent = '';
            while (true) {
                $char = $content[$insertion->startPos - strlen($indent) - 1];
                if ($char === ' ' || $char === "\t") {
                    $indent = $char . $indent;
                } else {
                    break;
                }
            }

            // add indention
            $replacement = $insertion->replacement;
            $replacementLines = explode("\n", $replacement);
            $replacement = implode("\n$indent", $replacementLines);

            $content = substr_replace($content, $replacement, $insertion->startPos,
                $insertion->endPos - $insertion->startPos + 1);
            file_put_contents($insertion->file, $content);
        }
    }
}
