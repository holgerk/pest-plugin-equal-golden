<?php

namespace Holgerk\EqualGolden;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\ParserFactory;

/** @internal */
final class Insertion
{
    /** @var self[] */
    public static array $insertions = [];

    public static function register(string $replacement): void
    {
        $filePath = null;
        $lineToFind = null;
        foreach (debug_backtrace() as $stackItem) {
            if (($stackItem['type'] ?? '') === '->' && ($stackItem['args'][0] ?? '') === 'toEqualGolden') {
                $filePath = $stackItem['file'];
                $lineToFind = $stackItem['line'];
                break;
            }
        }
        assert((bool) $filePath);

        $parser = (new ParserFactory())->createForHostVersion();
        $fileContent = file_get_contents($filePath);
        $ast = $parser->parse($fileContent);

        // connect sibling nodes (see: https://github.com/nikic/PHP-Parser/blob/master/doc/component/FAQ.markdown)
        (new NodeTraverser(new NodeConnectingVisitor))->traverse($ast);

        $nodeFinder = new NodeFinder();
        $node = $nodeFinder->findFirst($ast, fn (Node $node): bool => $node instanceof Identifier
            && $node->name === 'toEqualGolden'
            && $node->getStartLine() === $lineToFind
            && $node->getEndLine() === $lineToFind);
        /** @var Node $argumentNode */
        $argumentNode = $node->getAttribute('next');

        self::$insertions[] = new self(
            $filePath,
            $argumentNode->getStartFilePos(),
            $argumentNode->getEndFilePos(),
            $replacement
        );
    }

    public static function writeAndResetInsertions(): void
    {
        $insertions = self::$insertions;
        self::$insertions = [];

        // arrange insertions starting from the end of the file to prevent disrupting the positions during replacements
        usort($insertions, function (Insertion $a, Insertion $b): int {
            if ($a->file !== $b->file) {
                return $a->file <=> $b->file;
            }

            return $b->startPos <=> $a->startPos;
        });

        foreach ($insertions as $insertion) {
            $content = file_get_contents($insertion->file);
            $indent = self::getIndent($insertion->startPos, $content);

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

    private static function getIndent(int $startPos, string $content): string
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

    private function __construct(
        public string $file,
        public int $startPos,
        public int $endPos,
        public string $replacement,
    ) {
    }
}
