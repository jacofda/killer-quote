# Setup

```
$ php artisan vendor:publish --provider="KillerQuote\KillerQuoteServiceProvider" --tag="killerquote.seeds"
$ php artisan vendor:publish --provider="KillerQuote\KillerQuoteServiceProvider" --tag="killerquote.public"

$ composer dump-autoload

$ php artisan migrate
$ php artisan db:seed --class=KillerQuoteSettingsSeeder
$ php artisan db:seed --class=KillerQuotePermissionsSeeder

```

# Dependencies 

- `Areaseb\Core\Models\Media`
- `Areaseb\Core\Models\Product`
- `Areaseb\Core\Models\Category`
- `\Spatie\Permission\Models\Permission`
