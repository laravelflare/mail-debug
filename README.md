
# Mail Debug

This package is designed to help you easily preview and debug emails sent by your Laravel application in the browser. It provides a pop-up email preview on the subsequent page request after an email has been triggered by your application.

It is based off of ![themsaid's](https://github.com/themsaid/) ![Laravel Mail Preview Driver](https://github.com/themsaid/laravel-mail-preview) which is extremely helpful for more persistent logging and previewing (including .eml files).

![Example Animation](https://raw.githubusercontent.com/laravelflare/mail-debug/master/example.gif)

[![Latest Stable Version](https://poser.pugx.org/laravelflare/mail-debug/v/stable)](https://packagist.org/packages/laravelflare/mail-debug) [![Total Downloads](https://poser.pugx.org/laravelflare/mail-debug/downloads)](https://packagist.org/packages/laravelflare/mail-debug) [![Latest Unstable Version](https://poser.pugx.org/laravelflare/mail-debug/v/unstable)](https://packagist.org/packages/laravelflare/mail-debug) [![License](https://poser.pugx.org/laravelflare/mail-debug/license)](LICENSE.md)


## Quick Start Guide

Install the package into your project using composer:
```
    composer require "laravelflare/mail-debug"
```

Add the Mail Debug Service Provider to your Applications Service Providers list:
```php
    LaravelFlare\MailDebug\MailDebugServiceProvider::class,
```

Publish the configuration file using:
``` 
    php artisan vendor:publish
```

In your .env file, set your mail driver to `debug`:
``` 
    MAIL_DRIVER=debug
```


## How It Works

By registering your mail driver as `debug`, when an email is 'sent' Mail Transport stores the contents of the email to the application storage folder. It then sets the filename of the stored email into the Session.

On the next request (page load) the Mail Debug middleware will check the Session to determine if an email is available to be previewed. If it is, it will trigger the email to be displayed in a browser pop up window.

**Note:** You may need to allow pop-ups in your browser to see the preview emails.


## Configuration Options


### Path

This is the Mail Preview Storage Path to store mail preview files in. By default this is /storage/email-previews, but you can use whatever you like!


### Lifetime

This is the amount of time to store the preview files for (in minutes). By default this is one minute but can be increased if you would like a certain level of persistence.


## Improvements Required

Currently this only works for the last mail sent in a request. So if your request cycle sends several emails, only the final email will be displayed on the subsequent page request pop-up.