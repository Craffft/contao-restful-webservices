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
 * Initialize the system
 */
define('TL_MODE', 'RESTFUL_WEBSERVICES');
define('BYPASS_TOKEN_CHECK', true);
require '../system/initialize.php';


/**
 * Instantiate the RESTfulWebservice
 */
\RESTfulWebservices\RESTfulWebservice::getInstance()->run();
