<?php

namespace WHMCS\Module\Addon\Ip;


/**
 * Class IpController
 * @package WHMCS\Module\Addon\Ip
 */
class IpController
{
    const MODULE_NAME = 'ip';

    /**
     * Index action
     * @return string
     */
    public function index()
    {
        $search = new IpSearch();
        $grid = new IpGrid($search);
        $url = 'addonmodules.php?' . http_build_query([
                'module' => self::MODULE_NAME,
                'action' => 'show'
            ]);
        $grid->fields = [
            'ip' => [
                'label' => 'IP'
            ],
            'vlan' => [
                'label' => 'VLAN',
                'url' => $url
            ],
            'ptr' => [
                'label' => 'PTR'
            ],
            'used_by' => [
                'label' => 'Used by',
                'prefix' => 'DS'
            ]
        ];

        return $grid->create();
    }

    /**
     * Show action.
     * @return string
     */
    public function show()
    {
        if (isset($_GET['vlan'])) {
            $search = new IpSearch();
            $search->vlan = $_GET['vlan'];
            $list = new IpList($search);
            return $list->createList();
        }

        echo '<p>VLAN is required for view</p>';
    }
}
