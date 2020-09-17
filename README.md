# Double Quote Fixer
PHP CS Fixer rule for using double quotes. Code copied and edited from `single_quote` rule.

## Installation

    composer require eislambey/double-quote-fixer --dev

## Usage
```php
PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->registerCustomFixers([
        new \Islambey\Fixers\DoubleQuoteFixer(),
    ])
    ->setRules([
        "Islambey/double_quote" => ["strings_containing_double_quote_chars" => true],
    ]);
```

## License
The MIT License