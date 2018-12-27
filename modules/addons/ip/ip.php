<?php

use WHMCS\Module\Addon\Ip\IpDispatcher;
use WHMCS\Module\Addon\Ip\IpFilter;

/**
 * @return array
 */
function ip_config() {
    return array(
        "name" => "IP module",
        "description" => "This is config for test ip module",
        "version" => "1.0",
        "author" => "Serg Tolstopyatenko");
}

/**
 * @param $vars
 */
function ip_output($vars) {
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $dispatcher = new IpDispatcher();
    $response = $dispatcher->dispatch($action, $vars);
    echo $response;
}

/**
 * @return string
 */
function ip_sidebar()
{
    $form = new IpFilter();
    $sidebar = $form->createForm();
    return $sidebar;
}
