<?php

declare(strict_types=1);

use Holgerk\EqualGolden\Insertion;
use Holgerk\EqualGolden\Plugin;
use Pest\Expectation;
use Symfony\Component\VarExporter\VarExporter;

expect()->extend('toEqualGolden', function (mixed $golden): Expectation {
    if ($golden === null || Plugin::$forceUpdateGolden) {
        $golden = $this->value;
        $replacement = VarExporter::export($golden);
        Insertion::register($replacement);
    }

    return $this->toEqual($golden);
});
