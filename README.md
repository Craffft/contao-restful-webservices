RESTful Webservices for Contao
==================================

The "RESTful Webservices" extension is a helper library for developers to realize RESTful webservices in your own extension

License
-------

This Contao extension is licensed under the terms of the LGPLv3.
http://www.gnu.org/licenses/lgpl-3.0.html

Dependencies
------------

- Haste https://github.com/codefog/contao-haste

Links
-----

https://contao.org/en/extension-list/view/restful-webservices.html

Documentation
-------------

Define the webservice "categories"

```php
// systems/modules/mymodule/config/config.php

$GLOBALS['RESTFUL_WEBSERVICE']['ROUTING']['categories'] = array
(
    // Define the webservice location (required definition)
    // Callable via http://localhost/mycontao/interface/categories/12/my_token
    'pattern' => '/categories/{id}/{token}',

    // Restrict methods (optional definition)
    // You can use GET, PUT, POST and DELETE
    'methods' => array('GET', 'POST'),

    // Set requirements for the pattern values (optional definition)
    'requirements' => array
    (
        'id' => '\d+',
    ),

    // Restrict access by tokens (optional definition)
    'tokens' => array
    (
        'my_token',
    ),

    // Restrict access by ip addresses (optional definition)
    'ips' => array
    (
        '127.0.0.1',
    ),

    // Restrict CORS access by ip addresses (optional definition)
    'cors' => array
    (
        '192.168.1.180',
    )
);
```

Declare the webservice class "WebserviceCategories"

```php
// systems/modules/mymodule/webservices/WebserviceCategories.php

namespace MyAppNamespace;

use \Haste\Http\Response\JsonResponse;

class WebserviceCategories extends \RESTfulWebservices\Controller
{
    public function get()
    {
        $arrData = array();

        // Add "Hello World!" to the json output
        $arrData['status'] = 'Hello World!';

        // Send response
        $objResponse = new JsonResponse();
        $objResponse->setContent($arrData, JSON_PRETTY_PRINT);
        $objResponse->send();
    }

    public function put()
    {
        // Code for PUT requests
    }

    public function post()
    {
        // Code for POST requests
    }

    public function delete()
    {
        // Code for DELETE requests
    }
}
```
