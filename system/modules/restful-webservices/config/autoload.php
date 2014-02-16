<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (c) 2014 Daniel Kiesel
 *
 * @package RESTfulWebservices
 * @link    https://github.com/icodr8/contao-restful-webservices
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
    'RESTfulWebservices\Controller'        => 'system/modules/restful-webservices/library/RESTfulWebservices/Controller.php',
    'RESTfulWebservices\RESTfulWebservice' => 'system/modules/restful-webservices/library/RESTfulWebservices/RESTfulWebservice.php',
    'RESTfulWebservices\JsonResponse'      => 'system/modules/restful-webservices/library/RESTfulWebservices/JsonResponse.php',
));
