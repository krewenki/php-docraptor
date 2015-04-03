#PHP-DocRaptor

PHP-DocRaptor is a simple API wrapper for [DocRaptor.com](https://docraptor.com/).
You will need a DocRaptor account before you can use this library, as it requires a valid API key.

##Installation

This library is PSR-4 autoloading compliant, you can install it via composer. Just require it in your `composer.json`.

    "require": {
        "expectedbehavior/php-docraptor": "dev-master"
    }
    
Then run `composer update` resp. `composer install`.

##Usage
Import the namespace.

    use DocRaptor\ApiWrapper;

Use the wrapper.

    $docRaptor = new ApiWrapper($api_key);
    $docRaptor->setDocumentContent('<h1>Hello!</h1>')->setDocumentType('pdf')->setTest(true)->setName('output.pdf');
    $file = $docRaptor->fetchDocument();

Optionally, the fetchDocument() method takes a filename as an argument.  If you provide
a filename, the class will attempt to write the returned value to the file you provided.

##Options

###HTTPS or HTTP
By default, PHP-DocRaptor submits requests over https.  You can choose to submit via http, if that's your preference, by passing an argument to the *setSecure()* method (true for https, false for http):

	$docRaptor->setSecure(false);
	
NB! It IS not secure, you're basically broadcasting your api key over the network.
