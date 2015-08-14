# WorkEtcClient

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
$stageGroups = $we->invoke('GetProjectStageGroups');

// Invoke an operation with parameters
$projects = $we->invoke('FindProjects', [
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
