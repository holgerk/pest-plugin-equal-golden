# Golden Testing for Pest

[![run-tests](https://github.com/holgerk/pest-plugin-equal-golden/actions/workflows/tests.yml/badge.svg)](https://github.com/holgerk/pest-plugin-equal-golden/actions/workflows/tests.yml)

Golden Master Testing verifies software changes by comparing their output with a known good version.
This package helps to automate the creation of the good version by adding a `toEqualGolden` expectation to
**[Pest](https://pestphp.com)**.

In principle, it's about saving oneself the recurring work of writing or copying an expectation.


## Installation

You can install the package via composer:

```bash
composer require holgerk/pest-plugin-equal-golden --dev
```


## ✨ Usage ✨

Just pass `null` to the `toEqualGolden` expectation and `null` will be automatically replaced during the 
first test run.

Given you run this test:

```php
function makeCustomer(): array {
    return [
        'name' => 'Frank',
        'favoriteColor' => 'red',
    ];
}
it('returns customer record', function () {
    expect(makeCustomer())->toEqualGolden(null);
});
```
...after the first execution, the code is:
```php
// [...]
it('returns customer record', function () {
    expect(makeCustomer())->toEqualGolden([
        'name' => 'Frank',
        'favoriteColor' => 'red',
    ]);
});
```

Later you can edit the expectation by hand or insert `null` again to have it automatically replaced.  
If you want to regenerate all expectations at once you can add the argument: `--update-golden` to your pest
invocation.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Nuno Maduro](https://github.com/nunomaduro)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
