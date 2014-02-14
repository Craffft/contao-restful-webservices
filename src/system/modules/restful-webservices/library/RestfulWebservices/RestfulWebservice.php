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
 * Class RESTfulWebservice
 *
 * @copyright  Daniel Kiesel 2014
 * @author     Daniel Kiesel <https://github.com/icodr8>
 */
class RESTfulWebservice extends \Contao\Controller
{
    /**
     * instance
     *
     * @var object
     * @access protected
     * @static
     */
    protected static $instance = null;

    /**
     * response
     *
     * @var object
     * @access protected
     */
    protected $response = null;

    /**
     * __construct function.
     *
     * @access protected
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();

        // Load language file
        \System::loadLanguageFile('default');

        // Set static urls
        $this->setStaticUrls();

        // Init response
        $this->response = new JsonResponse();
    }

    /**
     * Gets an instance from itself.
     *
     * @access public
     * @static
     * @return object
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new RESTfulWebservice();
        }

        return self::$instance;
    }

    /**
     * Initializes the webservice.
     *
     * @access public
     * @return void
     */
    public function run()
    {
        $strPath = trim(preg_replace('#' . TL_PATH . '/interface#', '', \Environment::get('requestUri'), 1), '/');

        if (!is_array($GLOBALS['RESTFUL_WEBSERVICES']['ROUTING']) || count($GLOBALS['RESTFUL_WEBSERVICES']['ROUTING']) < 1) {
            $this->response->sendError(404);
        }

        // Go through each defined routing item
        foreach ($GLOBALS['RESTFUL_WEBSERVICES']['ROUTING'] as $k => $v) {
            // Handle params
            if (!$this->handleParams($strPath, $v['pattern'], $v['requirements'])) {
                continue;
            }

            // Check methods
            if (!$this->checkMethods($v['methods'])) {
                continue;
            }

            // Check tokens
            if (!$this->checkTokens($v['tokens'])) {
                continue;
            }

            // Check allowed ip addresses
            if (!$this->checkIps($v['ips'])) {
                continue;
            }

            // Check allowed CORS hosts
            $this->checkCors($v['cors']);

            // Call webservice class method
            $this->callClassMethod(
                $this->getClass($k),
                $this->getMethod()
            );

            // Send response
            $this->response->send();
            exit;
        }

        // Throw error
        $this->response->sendError(404);
    }

    /**
     * Gets the parameters from the url and saves it in the GET variables.
     *
     * @access protected
     * @param  string $strPath
     * @param  string $strPattern
     * @param  array  $arrRequirements (default: null)
     * @return bool
     */
    protected function handleParams($strPath, $strPattern, $arrRequirements = null)
    {
        \Input::initialize();

        // Get parameter keys
        preg_match_all('#{([^}]*)}#', $strPattern, $arrKeys);
        $arrKeys = (is_array($arrKeys[1])) ? $arrKeys[1] : null;

        // Check routing and get parameter values
        $strPattern = preg_replace('#{[^}]*}#', '([^/]*)', $strPattern);
        $blnRouting = preg_match(('#^' . $strPattern . '$#'), ('/' . $strPath), $arrValues);

        // If route not found
        if (!$blnRouting) {
            return false;
        }

        // Build parameter array
        if (is_array($arrKeys) && count($arrKeys) > 0 && is_array($arrValues) && count($arrValues) > 1) {
            $blnContinue = false;

            foreach ($arrKeys as $kk => $vv) {
                if (!isset($arrKeys[$kk]) || !isset($arrValues[$kk+1])) {
                    $blnContinue = true;
                    break;
                }

                // Check requirements
                if (isset($arrRequirements[$arrKeys[$kk]])) {
                    if (!preg_match('#^(' . $arrRequirements[$arrKeys[$kk]] . ')$#', $arrValues[$kk+1])) {
                        $blnContinue = true;
                        break;
                    }
                }

                \Input::setGet($arrKeys[$kk], $arrValues[$kk+1]);
            }

            // If continue
            if ($blnContinue) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check the allowed request methods.
     *
     * @access protected
     * @param  string $strMethod
     * @return bool
     */
    protected function checkMethods($strMethod)
    {
        if (isset($strMethod)) {
            if (!is_array($strMethod) || !in_array(\Environment::get('requestMethod'), $strMethod)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Compare given token with allowed tokens.
     *
     * @access protected
     * @param  array $arrTokens
     * @return bool
     */
    protected function checkTokens($arrTokens)
    {
        if (isset($arrTokens)) {
            if (!in_array(\Input::get('token'), $arrTokens) && !in_array(\Input::post('token'), $arrTokens)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check the allowed ip addresses.
     *
     * @access protected
     * @param  array $arrIps
     * @return bool
     */
    protected function checkIps($arrIps)
    {
        if (isset($arrIps)) {
            if (!in_array(\Environment::get('remoteAddr'), $arrIps)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check the allowed cors hosts.
     *
     * @access protected
     * @param  array $arrCors
     * @return bool
     */
    protected function checkCors($arrCors)
    {
        if (!isset($arrCors) || (isset($arrCors) && in_array(\Environment::get('httpOrigin'), $arrCors))) {
            // Set CORS
            $this->response->setHeader("Access-Control-Allow-Origin:", \Environment::get('httpOrigin'));

            return true;
        }

        return false;
    }

    /**
     * Checks the parameters of the given method and calls it.
     *
     * @access protected
     * @param  string $strClass
     * @param  string $strMethod
     * @return string
     */
    protected function callClassMethod($strClass, $strMethod)
    {
        if (!method_exists($strClass, $strMethod)) {
            // Throw error
            $this->response->sendError(500, sprintf('The class method %s does not exist!', $strClass . '::' . $strMethod . '()'));
        }

        // Check if class name begins with "Webservice"
        if (substr((explode('\\', $strClass)[count(explode('\\', $strClass))-1]), 0, 10) != 'Webservice') {
            // Throw error
            $this->response->sendError(500, sprintf('The class name of %s has to begin with "Webservice"', $strClass . '::' . $strMethod . '()'));
        }

        // Get params
        $arrParams = (\Environment::get('requestMethod') == 'POST') ? $_POST : $_GET;

        // Get reflection object
        $objReflection = new \ReflectionMethod($strClass, $strMethod);
        $arrRealParams = array();

        foreach ($objReflection->getParameters() as $strParam) {
            $strName = $strParam->getName();

            // Check if param exists
            if (array_key_exists($strName, $arrParams)) {
                $arrRealParams[] = $arrParams[$strName];
            } else {
                // Throw error
                $this->response->sendError(500, sprintf('%s is using the unknown parameter "$%s"', $strClass . '::' . $strMethod . '()', $strName));
            }
        }

        // Init class and call method
        $objClass = new $strClass();

        return call_user_func_array(array($objClass, $strMethod), $arrRealParams);
    }

    /**
     * Returns the webservice class.
     *
     * @access protected
     * @param  string $strName
     * @return string
     */
    protected function getClass($strName)
    {
        return '\Webservice' . implode('', array_map('ucfirst', explode('_', $strName)));
    }

    /**
     * Returns the method.
     *
     * @access protected
     * @return string
     */
    protected function getMethod()
    {
        $strMethod = 'get';

        switch (\Environment::get('requestMethod')) {
            case 'GET':
            case 'PUT':
            case 'POST':
            case 'DELETE':
                $strMethod = strtolower(\Environment::get('requestMethod'));
                break;
        }

        return $strMethod;
    }
}
