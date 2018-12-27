<?php

namespace WHMCS\Module\Addon\Ip;

use WHMCS\Database\Capsule;

/**
 * Class IpGrid
 * @package WHMCS\Module\Addon\Ip
 */
class IpGrid
{
    const SEARCH_LIMIT = 50;
    const ORDER_BY = 'ip';

    /**
     * Array with fields of table.
     * In fields can set label for column,
     * url (if need a create link in cell),
     * prefix to text in cell
     *
     * For example:
     * 'ip' => [
     *      'label' => 'IP'
     * ],
     * 'vlan' => [
     *      'label' => 'VLAN',
     *      'url' => 'addonmodules.php?module=ip&action=show'
     * ],
     * 'used_by' => [
     *      'label' => 'Used by',
     *      'prefix' => 'DS'
     * ]
     *
     * @var array
     */
    public $fields = [];

    protected $search;
    protected $data;

    /**
     * IpGrid constructor.
     * @param \WHMCS\Module\Addon\Ip\IpSearch $search
     */
    function __construct(IpSearch $search)
    {
        $this->search = $search;
        $this->data = $this->search->createGridQuery();
    }


    /**
     * Composition parts of table
     * @return string
     */
    private function prepareTableFrame()
    {
        $table = '<table class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3"><tbody>';
        $table .= $this->prepareTableHead();
        $table .= $this->prepareTableBody();
        $table .= '</tbody></table>';
        $table .= $this->createPager();
        return $table;
    }

    /**
     * Create table head and add labels to columns
     * @return string
     */
    private function prepareTableHead()
    {
        $result = '<tr>';
        foreach ($this->fields as $key => $value) {
            if (isset($value['label'])) {
                $result .= '<th>' . $value['label'] . '</th>';
            } else {
                $result .= '<th>' . $key . '</th>';
            }
        }
        $result .= '</tr>';
        return $result;
    }

    /**
     * Create body of table and put data in it
     * @return string
     */
    private function prepareTableBody()
    {
        $result = '';
        foreach ($this->data['data'] as $key => $value) {
            $result .= '<tr>';
            foreach ($this->fields as $field => $options) {
                $cellData = isset($value->$field) ? $value->$field : '';
                $cellData = isset($options['prefix']) && !empty($cellData) ? $options['prefix'] . $cellData : $cellData;
                $cellData = isset($options['url']) && !empty($cellData) ? $this->prepareUrl($field, $cellData,
                    $options['url']) : $cellData;
                $result .= '<td>' . $cellData . '</td>';
            }
            $result .= '</tr>';
        }
        return $result;
    }

    /**
     * Prepare VLAN-url for grid
     * @param $field
     * @param $data
     * @param $url
     * @return string
     */
    private function prepareUrl($field, $data, $url)
    {
        $url = $url . '&' . $field . '=' . $data;
        return '<a href="' . $url . '">' . $data . '</a>';
    }

    /**
     * Create pager for table
     * @return string
     */
    private function createPager()
    {

        $pages = ceil($this->data['count'] / $this->search->limit);
        $pager = '<ul class="pager">';
        if (!isset($_GET['page']) || (isset($_GET['page']) && $_GET['page'] == 1)) {
            $pager .= '<li class="previous disabled"><a href="javascript:void(0)">« « Предыдущая страница</a></li>';
        } else {
            $pager .= '<li class="previous"><a href="'.$this->createPagerUrl($_GET['page'] - 1).'">« « Предыдущая страница</a></li>';
        }
        for ($i = 1; $i <= $pages; $i++) {
            if ((isset($_GET['page']) && $_GET['page'] == $i) || (!isset($_GET['page']) && $i == 1)) {
                $pager .= '<li><span style="background: #ddd">'.$i.'</span></li>';
            } else {
                $pager .= '<li><a href="'.$this->createPagerUrl($i).'">'.$i.'</a></li>';
            }
        }
        if ((isset($_GET['page']) && $_GET['page'] == $pages) || $pages == 1) {
            $pager .= '<li class="next disabled"><a href="javascript:void(0)">Следующая страница » »</a></li>';
        } else {
            if (!isset($_GET['page'])) {
                $pager .= '<li class="next"><a href="'.$this->createPagerUrl(2).'">Следующая страница » »</a></li>';
            } else {
                $pager .= '<li class="next"><a href="'.$this->createPagerUrl($_GET['page'] + 1).'">Следующая страница » »</a></li>';
            }
        }
        $pager .= '</ul>';
        return $pager;

    }

    /**
     * Create url for pager
     * @param $page
     * @return string
     */
    private function createPagerUrl($page)
    {
        $data = $_REQUEST;
        $data['page'] = $page;
        $dataUrl = http_build_query($data);
        return 'addonmodules.php?'.$dataUrl;
    }

    /**
     * Create table with data and pagination
     * @return string
     */
    public function create()
    {
        $table = $this->prepareTableFrame();
        return $table;
    }
}