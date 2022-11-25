# Laravel Quick Migrations

[![Build Status](https://travis-ci.org/mnabialek/laravel-quick-migrations.svg?branch=master)](https://travis-ci.org/mnabialek/laravel-quick-migrations)
[![Coverage Status](https://coveralls.io/repos/github/mnabialek/laravel-quick-migrations/badge.svg)](https://coveralls.io/github/mnabialek/laravel-quick-migrations)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mnabialek/laravel-quick-migrations/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mnabialek/laravel-quick-migrations/)
[![Packagist](https://img.shields.io/packagist/dt/mnabialek/laravel-quick-migrations.svg)](https://packagist.org/packages/mnabialek/laravel-quick-migrations)

This package is intended to **improve speed of Laravel tests that needs to use migrations**. In case you use Laravel's `DatabaseMigrations` trait (especially in Browser tests) you might be interested in using this package to save a lot of time.

Be aware this package doesn't improve speed of normal migrations you apply to database - it should be used only if have tests in your application and you want to improve their speed. 

## Installation

1. Run
   ```php   
   composer require mnabialek/laravel-quick-migrations --dev
   ```
   in console to install this module (Notice `--dev` flag - it's recommended to use this package only for development).
   
2. Run:
    
    ```php
    php artisan vendor:publish
    ```
    
    and choose the number matching `"Mnabialek\LaravelQuickMigrations\Providers\ServiceProvider"` provider.
    
    By default you should not edit published file because all the settings are loaded from `.env` file by default.
    
    Depending on your needs you might add now into `.env` (or other env files used for tests):
    
    ```php
    QUICK_MIGRATIONS_ENABLED=true
    QUICK_MIGRATIONS_DUMP_FILE="/custom/directory/custom_filename.sql"
    ```
    
    and customize it with your own values. Keep if mind if you set `QUICK_MIGRATIONS_ENABLED` you will automatically use default
    Laravel migrations again.
    
3. Update all your tests where you use `DatabaseMigrations` trait with `QuickDatabaseMigrations`. You should add import line into those files too:

    ```php
    use Mnabialek\LaravelQuickMigrations\QuickDatabaseMigrations;
    ```
    
4. Run:    

    ```php
    php artisan migrate:fresh --database=selected_sql_connection
    ```
    
    into empty database. Of course as `selected_sql_connection` you should use connection you really use in your app (usually `mysql` or `mysql_testing`).
    
    Now you can manually dump structure of this database into single file (or use `mysqldump`) and save file as `storage/tests/dump.sql` (If you set custom value of `QUICK_MIGRATIONS_DUMP_FILE` in your env file then you should of course put it into your custom location)
    
5. Run your tests and enjoy!

## Benchmarks

My test suite (real application) had 53 Laravel Dusk tests with 890 assertions running in Docker container. Database had 93 migrations.
    
| Run  | DatabaseMigrations | QuickDatabaseMigrations | Difference |
|----- |:---:|:---:|:---:|
| 1st  | 24.7 min (28s/test)  |8.29 min (9.4s/test)|16.41 min (**2.98 times faster**) |
| 2nd  | 25.08 min (28.4s/test)  |9.23 min (10.45s/test)|15.85 min (**2.72 times faster**)|
| 3rd  | 24.37 min (27.57s/test)  |7.92 min (8.97s/test)|16.45 min (**3.08 times faster**)|

As you see in real-application scenario difference is quite impressive. Using modified trait makes tests running almost 3 times faster comparing to original tests. 

Of course in your case results might be different. A lot of depends on migrations you have in your app - how long they take by default. For example assuming you have 100 tests using migrations and applying your migrations takes 10 seconds (for each test) and using your dump would take 6 seconds (for each test) then you would save 100 * 4 seconds that gives 6.66 minutes each time you are running your tests. As you see in my scenario difference was much bigger.

## Cons

- You need to manually update dump whenever you add/change migrations. But assuming you are using tests it might be really worth it to spend < 1 minute for dumping fresh migrations to save hundreds of minutes.
- After running tests in your database you have data you created during your tests. From my point of view, if you are using database for tests only you should not care much about it. But of course you can create command that will always remove everything from your database after completing tests if you really need it.

### Authors

Author of this package is **[Marcin Nabiałek](http://marcin.nabialek.org/en/)**  and [Contributors](https://github.com/mnabialek/laravel-quick-migrations/graphs/contributors)

### Changes

All changes are listed in [Changelog](CHANGELOG.md)

### License

This package is licenced under the [MIT license](LICENSE).
