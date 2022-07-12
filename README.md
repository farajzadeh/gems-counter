# Gems Counter
This package counts the number of gems in your game.

## Quick Installation

Add this lines to your composer.json
```
{
    ....
    
    "repositories": [
        ....
        {
            "url": "https://github.com/farajzadeh/gems-counter.git",
            "type": "git"
        }
    ],
}
```

Then run this command
```bash
$ composer require farajzadeh/gems-counter "@dev"
```

## How to use

Just add `HasGems` trait to your user model.

Then get number of user gems by `gems_count` attribute
and create transaction with `createTransaction($amount, $tag)` function.

## Testing

```bash
composer test
```
