<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (c) 2014 Daniel Kiesel
 *
 * @package RESTfulWebservices
 * @link    https://github.com/craffft/contao-restful-webservices
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'RESTfulWebservices',
));

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Library
    'RESTfulWebservices\JsonResponse'      => 'system/modules/restful-webservices/library/RESTfulWebservices/JsonResponse.php',
    'RESTfulWebservices\RESTfulController' => 'system/modules/restful-webservices/library/RESTfulWebservices/RESTfulController.php',
    'RESTfulWebservices\RESTfulWebservice' => 'system/modules/restful-webservices/library/RESTfulWebservices/RESTfulWebservice.php',
));
