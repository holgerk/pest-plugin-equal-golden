<?php

declare(strict_types=1);

namespace Holgerk\EqualGolden;

use Pest\Expectation;
use Symfony\Component\VarExporter\VarExporter;

expect()->extend('toEqualGolden', function (mixed $golden): Expectation {
    if ($golden === null) {
        $golden = $this->value;

        $file = null;
        $line = null;
        foreach (debug_backtrace() as $stackItem) {
            if (($stackItem['type'] ?? '') === '->' && ($stackItem['args'][0] ?? '') === 'toEqualGolden') {
                $file = $stackItem['file'];
                $line = $stackItem['line'];
                break;
            }
        }

        if ($file) {
            $replacement = VarExporter::export($this->value);
            Plugin::registerInsertion(Insertion::make($file, $line, $replacement));
        } else {
            // TODO report error
        }
    }

    return $this->toEqual($golden);
});
