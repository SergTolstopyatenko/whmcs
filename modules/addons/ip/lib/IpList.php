<?php

namespace WHMCS\Module\Addon\Ip;

use WHMCS\Database\Capsule;

/**
 * Class IpList
 * @package WHMCS\Module\Addon\Ip
 */
class IpList
{

    protected $search;
    protected $data;

    /**
     * IpList constructor.
     * @param IpSearch $search
     */
    function __construct(IpSearch $search)
    {
        $this->search = $search;
        $this->data = $this->search->createListQuery();
    }

    /**
     * Create frame of list
     * @return string
     */
    private function prepareListFrame()
    {
        $list = '<div class="row"><div class="col-lg-12"><div class="clientssummarybox">';
        $list .= '<div class="title">Список сетей</div>';
        $list .= '<table class="clientssummarystats" cellspacing="0" cellpadding="2"><tbody>';
        $list .= $this->prepareListBody($this->data['result']);
        $list .= '</tbody></table>';
        $list .= '<ul><li>Всего IPов - ' . $this->data['data']['total'] . '</li>';
        $list .= '<li>Свободных IPов - ' . $this->data['data']['free'] . '</li>';
        $list .= '<li>Неиспользуемые сети - ' . $this->data['data']['unused'] . '</li></ul>';
        $list .= '</div></div></div>';

        return $list;

    }

    /**
     *  Create body of list with data and urls
     * @param $data
     * @return string
     */
    private function prepareListBody($data)
    {
        $result = '';
        foreach ($data as $key => $value) {
            $url = 'addonmodules.php?'. http_build_query([
                    'module' => IpController::MODULE_NAME,
                    'key' => $key,
                    'free' => true,
                ]);
            $result .= '<tr class="altrow"><td>'.$key.'/'.$value['total'].' (<a href="'.$url.'">'.$value['free'].' free ips</a>)</td>';
            $result .= '<td>VLAN: '.$this->vlan.'</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        }
        return $result;
    }


    /**
     * Create list with data
     * @return string
     */
    public function createList()
    {
       $list = $this->prepareListFrame();
       return $list;
    }
}