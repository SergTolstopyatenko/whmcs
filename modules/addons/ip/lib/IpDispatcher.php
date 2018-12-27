<?php

namespace WHMCS\Module\Addon\Ip;

/**
 * Class IpDispatcher
 * @package WHMCS\Module\Addon\Ip
 */
class IpDispatcher {

    /**
     * Dispatch request.
     *
     * @param string $action
     * @param array $parameters
     *
     * @return string
     */
    public function dispatch($action, $parameters)
    {
        if (!$action) {
            // Default to index if no action specified
            $action = 'index';
        }

        $controller = new IpController();

        // Verify requested action is valid and callable
        if (is_callable(array($controller, $action))) {
            return $controller->$action($parameters);
        }

        return '<p>Invalid action requested. Please go back and try again.</p>';
    }
}
