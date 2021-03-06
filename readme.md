# DataHive Development's Hive ID

## Quick Setup

```bash
composer require datahivedevelopment/hiveidsocialiteprovider
```

### 1. Add to `providers[]` array in `config\app.php`

``` php
'providers' => [
    \SocialiteProviders\Manager\ServiceProvider::class,
];
```

### 2. Add Event Listener in `app/Providers/EventServiceProvider`

```php
protected $listen = [
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        'DataHiveDevelopment\\HiveIDSocialiteProvider\\HiveIDExtendSocialite@handle',
    ],
];
```

### 3. Add configuration to `config/services.php`

```php
'hiveid' => [
    'server' => env('HIVEID_SERVER'), // Optional server parameter
    'client_id' => env('HIVEID_CLIENT_ID'),
    'client_secret' => env('HIVEID_CLIENT_SECRET'),
    'redirect' => env('HIVEID_REDIRECT')
],
```

## Step-by-step

### 1. Installation

```bash
// This assumes that you have composer installed globally
composer require datahivedevelopment/hiveidsocialiteprovider
```

### 2. Service Provider

* Remove `Laravel\Socialite\SocialiteServiceProvider` from your `providers[]` array in `config\app.php` if you have added it already.

* Add `\SocialiteProviders\Manager\ServiceProvider::class` to your `providers[]` array in `config\app.php`.

For example:

``` php
'providers' => [
    // a whole bunch of providers
    // remove 'Laravel\Socialite\SocialiteServiceProvider',
    \SocialiteProviders\Manager\ServiceProvider::class, // add
];
```

* Note: If you would like to use the Socialite Facade, you need to [install it.](https://github.com/laravel/socialite)

### 3. Event Listener

* Add `SocialiteProviders\Manager\SocialiteWasCalled` event to your `listen[]` array  in `app/Providers/EventServiceProvider`.

* Add your listeners (i.e. the ones from the providers) to the `SocialiteProviders\Manager\SocialiteWasCalled[]` that you just created.

* The listener that you add for this provider is `'DataHiveDevelopment\\HiveIDSocialiteProvider\\HiveIDExtendSocialite@handle',`.

* Note: You do not need to add anything for the built-in socialite providers unless you override them with your own providers.

For example:

```php
/**
 * The event handler mappings for the application.
 *
 * @var array
 */
protected $listen = [
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        // add your listeners (aka providers) here
        'DataHiveDevelopment\\HiveIDSocialiteProvider\\HiveIDExtendSocialite@handle',
    ],
];
```

#### Reference

* [Laravel docs about events](http://laravel.com/docs/5.0/events)
* [Laracasts video on events in Laravel 5](https://laracasts.com/lessons/laravel-5-events)

### 4. Configuration setup

You will need to add an entry to the services configuration file so that after config files are cached for usage in production environment (Laravel command `artisan config:cache`) all config is still available.

#### Add to `config/services.php`

```php
'hiveid' => [
    'server' => env('HIVEID_SERVER'), // Optional server parameter
    'client_id' => env('HIVEID_CLIENT_ID'),
    'client_secret' => env('HIVEID_CLIENT_SECRET'),
    'redirect' => env('HIVEID_REDIRECT')
],
```

#### `Server` configuration value

You can specify an optional `HIVEID_SERVER` in your .env file to override using the production Hive ID authentication system. This is most useful during development and needing to test a modification to Hive ID with another application. The value should be entered in the format of `http://id.test`, note the lack of a trailing `/`. The `/oauth/authorize` and `/oauth/token` endpoints will automatically be appended on the appropriate API calls.

### 5. Usage

* [Laravel docs on configuration](http://laravel.com/docs/master/configuration)

* You should now be able to use it like you would regularly use Socialite (assuming you have the facade installed):

```php
return Socialite::with('hiveid')->redirect();
```

#### Lumen Support

You can use Socialite providers with Lumen.  Just make sure that you have facade support turned on and that you follow the setup directions properly.

Also, configs cannot be parsed from the `services[]` in Lumen.  You can only set the values in the `.env` file as shown exactly in this document.  If needed, you can
  also override a config (shown below).

#### Stateless

* You can set whether or not you want to use the provider as stateless.  Remember that the OAuth provider (Twitter, Tumblr, etc) must support whatever option you choose.

**Note:** If you are using this with Lumen, all providers will automatically be stateless since **Lumen** does not keep track of state.

```php
// to turn off stateless
return Socialite::with('hiveid')->stateless(false)->redirect();

// to use stateless
return Socialite::with('hiveid')->stateless()->redirect();
```

#### Overriding a config

If you need to override the provider's environment or config variables dynamically anywhere in your application, you may use the following:

```php
$clientId = "secret";
$clientSecret = "secret";
$redirectUrl = "http://yourdomain.com/api/redirect";
$additionalProviderConfig = ['site' => 'meta.stackoverflow.com'];
$config = new \SocialiteProviders\Manager\Config($clientId, $clientSecret, $redirectUrl, $additionalProviderConfig);
return Socialite::with('hiveid')->setConfig($config)->redirect();
```

#### Retrieving the Access Token Response Body

Laravel Socialite by default only allows access to the `access_token`.  Which can be accessed
via the `\Laravel\Socialite\User->token` public property.  Sometimes you need access to the whole response body which
may contain items such as a `refresh_token`.

You can get the access token response body, after you called the `user()` method in Socialite, by accessing the property `$user->accessTokenResponseBody`;

```php
$user = Socialite::driver('hiveid')->user();
$accessTokenResponseBody = $user->accessTokenResponseBody;
```