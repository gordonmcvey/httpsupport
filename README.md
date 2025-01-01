# HTTP Support Library

This is a simple library to provide the following:

* A HTTP Request and Response implementation that you can re-use across projects
* Enums for the standard set of HTTP verbs and status codes
* Modern PHP feature usage (validating with strict types, enums, etc)
* Some utility classes for handling some grunt work like formatting header keys
* A thorough test suite
* Adherence to what is currently considered best practice

## Installation

1. Add this repository to your `composer.json` file

```json
{
    "repositories": [
        {
            "type": "github",
            "url": "git@github.com:gordonmcvey/httpsupport.git"
        }
    ]
}
```

2. Require the `httpsupport` library from the command line

```bash
composer require gordonmcvey/httpsupport
```

The library will then be available from within your project in the `gordonmcvey\httpsupport` namespace

## Notes

### Known issues and limitations

Currently this is not an implementation of [PSR HTTP message standards](https://www.php-fig.org/psr/psr-7/) for a couple of reasons:

* This library only implements the features I currently need for my [PHP-JAPI modernisation project](https://github.com/gordonmcvey/php-japi) and is missing some of the features specified in the PSR such as multiple-value headers
* PSR-7 was written with older versions of PHP in mind and currently an implementation that uses more modern PHP features like enums would be incompatible without a set of wrapper classes

I may address these issues in the future to make this library more general-purpose but for now it provides all the functionality needed for its current use case.  
