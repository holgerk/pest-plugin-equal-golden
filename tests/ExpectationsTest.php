<?php

use Holgerk\EqualGolden\Plugin;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;

beforeEach(fn () => Plugin::$updateGolden = false);

test('pass', function () {
    expect([1, 2, 3])->toEqualGolden([
        1,
        2,
        3,
    ]);
});

foreach (array_map('basename', glob(__DIR__.'/cases/*')) as $case) {

    test('generate golden - '.$case, function () use ($case) {
        // GIVEN
        $caseDirectory = __DIR__.'/cases/'.$case;
        $inputFile = $caseDirectory.'/input.php';
        $testFile = $caseDirectory.'/test.php';
        $expectedFile = $caseDirectory.'/expected.php';
        copy($inputFile, $testFile);

        // WHEN
        include $testFile;
        (new Plugin())->terminate(); // <- forces write

        // THEN
        Assert::assertFileEquals($expectedFile, $testFile);
    });

}

test('failures', function () {
    expect([1, 2, 3])->toEqualGolden([4]);
})->throws(ExpectationFailedException::class);
