# Golden Testing for [Pest](https://pestphp.com)

[![run-tests](https://github.com/holgerk/pest-plugin-equal-golden/actions/workflows/tests.yml/badge.svg)](https://github.com/holgerk/pest-plugin-equal-golden/actions/workflows/tests.yml)

The provided `toEqualGolden` assertion is similar to the `toEqual` assertion, but with automatic 
expectation generation.

So, if you add:

```php 
expect(['color' => 'golden'])
    ->toEqualGolden(null);
```
...to your test and execute it. The `null` is replaced by the actual value:

```php
expect(['color' => 'golden'])
    ->toEqualGolden(['color' => 'golden']);
```

In principle, it's about saving oneself the recurring work of writing or copying an expectation.


## Installation

You can install the package via composer:

```bash
composer require holgerk/pest-plugin-equal-golden --dev
```


## ✨ Usage ✨

Just pass `null` to the `toEqualGolden` expectation and `null` will be automatically replaced during the 
first test run.

Later you can edit the expectation by hand or insert `null` again to have it automatically replaced.  
If you want to regenerate all expectations at once you can add the argument: `--update-golden` to your pest
invocation.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## See Also

- [pest-plugin-snapshots](https://github.com/spatie/pest-plugin-snapshots)


## Credits

- [Nuno Maduro](https://github.com/nunomaduro)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
