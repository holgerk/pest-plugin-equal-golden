<?php

declare(strict_types=1);

namespace Holgerk\EqualGolden;

// use Pest\Contracts\Plugins\AddsOutput;
use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Contracts\Plugins\Terminable;
use Pest\Plugins\Concerns\HandleArguments;

/** @internal */
final class Plugin implements Terminable, HandlesArguments
{
    use HandleArguments;

    /** @var Insertion[] */
    private static array $insertions = [];

    public static bool $updateGolden = false;

    public function handleArguments(array $arguments): array
    {
        if ($this->hasArgument('--update-golden', $arguments)) {
            $arguments = $this->popArgument('--update-golden', $arguments);
            self::$updateGolden = true;
        }

        return $arguments;
    }

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
            $indent = $this->getIndent($insertion->startPos, $content);

            // add indention
            $replacement = $insertion->replacement;
            $replacementLines = explode("\n", $replacement);
            $replacement = implode("\n$indent", $replacementLines);

            // insert expectation
            $content = substr_replace(
                $content,
                $replacement,
                $insertion->startPos,
                $insertion->endPos - $insertion->startPos + 1
            );
            file_put_contents($insertion->file, $content);
        }
    }

    private function getIndent(int $startPos, string $content): string
    {
        // detect start of line position
        $offset = 0;
        $startOfLine = 0;
        while (true) {
            $offset -= 1;
            $charPos = $startPos + $offset;
            if ($charPos < 0) {
                break;
            }
            $char = $content[$charPos];
            if ($char === "\n" || $char === "\r") {
                $startOfLine = $startPos + $offset + 1;
                break;
            }
        }
        // detect indention
        $indent = '';
        $offset = 0;
        while (true) {
            $charPos = $startOfLine + $offset;
            $char = $content[$charPos];
            if ($char === ' ' || $char === "\t") {
                $indent .= $char;
            } else {
                break;
            }
            $offset += 1;
        }

        return $indent;
    }
}
