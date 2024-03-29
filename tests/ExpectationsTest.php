<?php

use Holgerk\EqualGolden\Plugin;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;

test('pass', function () {
    expect([1, 2, 3])->toEqualGolden([1, 2, 3]);
});

test('generate golden', function (string $caseDirectory) {
    // GIVEN
    $inputFile = $caseDirectory.'/input.php';
    $testFile = $caseDirectory.'/test.php';
    $expectedFile = $caseDirectory.'/expected.php';
    copy($inputFile, $testFile);

    // WHEN
    include $testFile;
    (new Plugin())->terminate(); // <- forces write

    // THEN
    Assert::assertFileEquals($expectedFile, $testFile);
})->with(/* [__DIR__ . '/cases/chain'] */ glob(__DIR__.'/cases/*'));

test('failures', function () {
    expect([1, 2, 3])->toEqualGolden([4, 5, 6]);
})->throws(ExpectationFailedException::class);
