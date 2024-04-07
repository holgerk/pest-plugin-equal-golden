<?php

declare(strict_types=1);

namespace Holgerk\EqualGolden;

use Pest\Contracts\Plugins\AddsOutput;
use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Contracts\Plugins\Terminable;
use Pest\Plugins\Concerns\HandleArguments;
use Symfony\Component\Console\Output\OutputInterface;

/** @internal */
final class Plugin implements AddsOutput, HandlesArguments, Terminable
{
    use HandleArguments;

    public static bool $forceUpdateGolden = false;

    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function handleArguments(array $arguments): array
    {
        if ($this->hasArgument('--update-golden', $arguments)) {
            $arguments = $this->popArgument('--update-golden', $arguments);
            self::$forceUpdateGolden = true;
        }

        return $arguments;
    }

    public function terminate(): void
    {
        Insertion::writeAndResetInsertions();
    }

    public function addOutput(int $exitCode): int
    {
        foreach (Insertion::$insertions as $insertion) {
            $this->output->writeln([
                sprintf(
                    '  <fg=white;options=bold;bg=blue> INFO </> Writing expectations to: %s.',
                    $insertion->file
                ),
            ]);
        }

        return $exitCode;
    }
}
