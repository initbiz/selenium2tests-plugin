# Selenium2Tests plugin

## Introduction
So you want to write tests in OctoberCMS using Selenium 2?

Using the `Ui2TestCase` class from this plugin this should be much easier. What is more you do not have to download Selenium bin, since it is included in the package.

## How-to
### Installation

1. Clone the code into `<project_root>/plugins/initbiz/selenium2tests` directory.
1. Run `composer dump-autoload` in project root
1. Go to `<project_root>/plugins/initbiz/selenium2tests`
1. Copy `selenium.php.example` to `selenium.php` and configure your environment

### Testing OctoberCMS basics
It is a good practice not to use the same DB for testing and developing purposes.

In order to configure different database for testing purposes create directory `testing` in `config` directory and copy `database.php` to the newly created directory. Then change the default connection as you wish.

After that if you use `APP_ENV=testing` in your `.env` file, configuration from `testing` directory will be used.

## Writing tests in Selenium 2
There are example tests in `tests/example` directory. API methods for Selenium 2 are not documented yet, but you can find all the methods in `traits` directory of plugin.

SeleniumHelpers was based on `Modelizer/Laravel-Selenium`.

## Running tests
First of all, you have to run Selenium 2 server. It is included in the package. You have to have Java installed on the local machine. In order to start Selenium 2 server change directory to `<project_root>/plugins/initbiz/selenium2tests` and run `java -jar selenium.jar`.

You are ready to run tests using PHPUnit from OctoberCMS's `vendor/bin/phpunit`.

You can keep test files wherever you want, but `.gitignore` of the plugin will exclude all files from `/tests` except those in `/tests/examples`.

## "`must be an instance of Exception, instance of Error given`"
The problem is with old version `phpunit` and `phpunit-selenium` extensions using by OctoberCMS. If you want to get nice error messages you have to update it in `composer.json` `require-dev` section. It can be the newest one, so you can just change the `~` with `>` in `phpunit` and `phpunit-selenium`.
