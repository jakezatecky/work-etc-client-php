# WORK[etc] PHP Client

[![Packagist](https://img.shields.io/packagist/v/jakezatecky/work-etc-client-php.svg)](https://packagist.org/packages/jakezatecky/work-etc-client-php)
[![Build Status](https://travis-ci.org/jakezatecky/work-etc-client-php.svg?branch=master)](https://travis-ci.org/jakezatecky/work-etc-client-php)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/jakezatecky/work-etc-client-php/master/LICENSE.txt)

**WorkEtcClient** is a thin HTTP client to the WORK[etc] SOAP API. It handles
login authentication and simple invocations of the WORK[etc] operations.

## Installation

This library is only available through a Composer package. Add the following to
your `composer.json`:

``` json
{
	"require": {
		"jakezatecky/work-etc-client-php": "~1.0.0"
	}
}
```

## Usage

Usage is simple. Invoke `WorkEtcClient::connect` and pass in your organization's
domain, your email, and your password. Then, call the `invoke` method and pass
in the relavent [operation][operation] and its required parameters as an array.

``` php
$domain   = 'yourcompany';
$email    = 'youremail@mail.com';
$password = 'yourpassword';

// Authenticate with WORK[etc] and get access to the API
$api = \WorkEtcClient\WorkEtcClient::connect($domain, $email, $password);

// Invoke an operation without any parameters
$stageGroups = $api->invoke('GetProjectStageGroups');

// Invoke an operation with parameters
$projects = $api->invoke('FindProjects', [
	'keywords' => 'Install',
]);
```

The example above is for the following WORK[etc] address. Replace `yourcompany`
with whatever sub-domain is associated with your organization:

```
https://yourcompany.worketc.com
```

## License

MIT license.

[operation]: http://admin.worketc.com/xml
