<?php

use Holgerk\EqualGolden\Plugin;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\Console\Output\NullOutput;

beforeEach(fn () => Plugin::$updateGolden = false);

test('generate expectation', function (string $case) {
    // GIVEN
    $caseDirectory = __DIR__.'/cases/'.$case;
    $inputFile = $caseDirectory.'/input.php';
    $testFile = $caseDirectory.'/test.php';
    $expectedFile = $caseDirectory.'/expected.php';
    copy($inputFile, $testFile);

    // WHEN
    include $testFile;
    (new Plugin(new NullOutput()))->terminate(); // <- forces write

    // THEN
    Assert::assertFileEquals($expectedFile, $testFile);
})->with(array_map('basename', glob(__DIR__.'/cases/*')));

test('pass', function () {
    expect([1, 2, 3])->toEqualGolden([
        1,
        2,
        3,
    ]);
});

test('failures', function () {
    expect([1, 2, 3])->toEqualGolden([4]);
})->throws(ExpectationFailedException::class);
