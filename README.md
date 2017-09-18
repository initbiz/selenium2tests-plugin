# Selenium2Tests plugin

## Introduction
The plugin was introduced to help people with writing tests based on Selenium 2.

## How-to
### Installation

1. Clone the code into <project_root>/plugins/initbiz/selenium2tests directory.
1. Go to <project_root>/plugins/initbiz/selenium2tests
1. Run composer install here
1. Copy selenium.php.example to selenium.php and configure your environment

### Testing OctoberCMS basics
It is a good practice not to use the same DB for testing and developing purposes.

In order to configure different database for testing purposes create directory `testing` in `config` directory and copy `database.php` to the newly created directory. Then change the default connection as you wish.

After that if you use `APP_ENV=testing` in your `.env` file, configuration from `testing` directory will be used.

## Writing tests in Selenium 2
There are example tests in `tests/functional` directory. Modelizer/selenium-laravel is used here, so methods in its API should work fine:

https://github.com/Modelizer/Laravel-Selenium/wiki/APIs

## Running tests
After you change directory to <project_root>/plugins/initbiz/selenium2tests you can run `vendor/bin/phpunit` which will run all tests in `tests` directory.