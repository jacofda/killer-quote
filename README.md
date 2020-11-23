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

- `Jacofda\Core\Models\Media`
- `Jacofda\Core\Models\Product`
- `Jacofda\Core\Models\Category`
- `\Spatie\Permission\Models\Permission`
