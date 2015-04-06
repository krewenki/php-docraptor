[![Build Status](https://travis-ci.org/expectedbehavior/php-docraptor.svg?branch=master)](https://travis-ci.org/expectedbehavior/php-docraptor)

#PHP-DocRaptor

PHP-DocRaptor is a simple API wrapper for [DocRaptor.com](https://docraptor.com/).
You will need a DocRaptor account before you can use this library, as it requires a valid API key.

##Dependencies
This wrapper requires PHP 5.4 or newer. PHP 5.4 support will be dropped when it reaches EOL, we strongly advice to migrate your projects to PHP 5.6. Other than that, only the PHP curl extention is needed.

At the current moment, this library also still works with PHP 5.3 but we don't guarantee that for any future releases.

##Installation

This library is PSR-4 autoloading compliant, you can install it via composer. Just require it in your `composer.json`.

    "require": {
        "expectedbehavior/php-docraptor": "1.0.0"
    }
    
Then run `composer update` resp. `composer install`.

##Usage
###Simple

    $docRaptor = new DocRaptor\ApiWrapper($api_key); // Or ommit the API key and pass it in via setter
    $docRaptor->setDocumentContent('<h1>Hello!</h1>')->setDocumentType('pdf')->setTest(true)->setName('output.pdf');
    $file = $docRaptor->fetchDocument();

Optionally, the fetchDocument() method takes a file path as an argument.  If you provide
a path, the class will attempt to write the returned value to the path you provided.

###Advanced
Since we're injecting a `HttpTransferInterface` interface into the `ApiWrapper` you can either inject the provided `HttpClient` or inject your own implementation of the interface.

    $httpClient = new DocRaptor\HttpClient();
    $docRaptor  = new DocRaptor\ApiWrapper($api_key, $httpClient);

The provided `HttpClient` is a very simple domain specific curl wrapper that extracts all curl functions from the `ApiWrapper` which makes it possible to inject a mock client for testing.

##Options

###HTTPS or HTTP
By default, PHP-DocRaptor submits requests over https.  You can choose to submit via http, if that's your preference, by passing an argument to the *setSecure()* method (true for https, false for http):

	$docRaptor->setSecure(false);
	
NB! It IS not secure, you're basically broadcasting your api key over the network.
