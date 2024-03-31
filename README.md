# Golden Testing for [Pest](https://pestphp.com)

[![run-tests](https://github.com/holgerk/pest-plugin-equal-golden/actions/workflows/tests.yml/badge.svg)](https://github.com/holgerk/pest-plugin-equal-golden/actions/workflows/tests.yml)

Provides a `toEqualGolden` expectation, which behaves identically to the `toEqual` expectation, 
with the only difference being that if `null` is passed as the expectation, the expectation is 
automatically created from the current value.

So, if you add:

```php 
expect(['color' => 'golden'])
    ->toEqualGolden(null);
```
...to your test and execute it. The `null` expression is replaced by the actual value:

```php
expect(['color' => 'golden'])
    ->toEqualGolden(['color' => 'golden']);
```

In principle, it's about saving oneself the recurring work of writing, updating and copying 
an expectation.


## Installation

You can install the package via composer:

```bash
composer require holgerk/pest-plugin-equal-golden --dev
```


## Usage

Just pass `null` to the `toEqualGolden` expectation and `null` will be automatically replaced during the 
first test run.

```php
expect($actual)->toEqualGolden(null);
```

Later you can edit the expectation by hand or insert `null` again to have it automatically replaced.  
If you want to regenerate all expectations at once you can add the argument: `--update-golden` to your pest
invocation.

```bash
# regenerate all expectations at once from their actual values
./vendor/bin/pest --update-golden
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## See Also

- [pest-plugin-snapshots](https://github.com/spatie/pest-plugin-snapshots)  
  This plugin also facilitates the automatic generation of expectations from the actual value, but it 
  will store the generated expectation in separate files.


## Credits

- [Nuno Maduro](https://github.com/nunomaduro)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
