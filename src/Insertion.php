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
    public static function make(string $filePath, int $lineToFind, string $replacement): self
    {
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

        return new self(
            $filePath,
            $argumentNode->getStartFilePos(),
            $argumentNode->getEndFilePos(),
            $replacement
        );
    }

    private function __construct(
        public string $file,
        public int $startPos,
        public int $endPos,
        public string $replacement,
    ) {
    }
}
