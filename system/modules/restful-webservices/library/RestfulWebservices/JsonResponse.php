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
 * Namespace
 */
namespace RESTfulWebservices;

/**
 * Class JsonResponse
 *
 * @copyright  Daniel Kiesel 2014
 * @author     Daniel Kiesel <https://github.com/icodr8>
 */
class JsonResponse extends \Haste\Http\Response\JsonResponse
{
    /**
     * Sends an error response.
     *
     * @access public
     * @static
     * @return void
     */
    public function sendError($intStatus, $strText = '')
    {
        // Prepare content
        $arrContent = array();
        $arrContent['status'] = sprintf('%s %s', $intStatus, static::$arrStatuses[$intStatus]);

        // Add text as info to the json string
        if (strlen($strText))
        {
            $arrContent['info'] = $strText;
        }

        // Send json response
        $this->setHeader("Access-Control-Allow-Origin:", \Environment::get('httpOrigin'));
        $this->setStatusCode($intStatus);
        $this->setContent($arrContent, JSON_PRETTY_PRINT);
        $this->send();
    }
}
