<?php

namespace WHMCS\Module\Addon\Ip;

use WHMCS\Database\Capsule;

/**
 * Class IpSearch
 * @package WHMCS\Module\Addon\Ip
 */
class IpSearch
{
    const SEARCH_LIMIT = 50;
    const ORDER_BY = 'ip';

    const STATUS_ALL = 0;
    const STATUS_USED = 1;
    const STATUS_UNUSED = 2;

    const LIMIT_50 = 50;
    const LIMIT_100 = 100;
    const LIMIT_200 = 200;

    const ORDER_IP = 'ip';
    const ORDER_VLAN = 'vlan';
    const ORDER_PTR = 'ptr';
    const ORDER_USED = 'used_by';

    const DCID_ALL = 0;
    const DCID_VXCHANGE = 1;
    const DCID_SERVERIUS = 2;
    const DCID_DATALINE = 3;
    const DCID_WEBAIR = 5;

    public $limit = self::SEARCH_LIMIT;
    public $vlan;

    /**
     * @return array
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_ALL => 'All',
            self::STATUS_USED => 'Used',
            self::STATUS_UNUSED => 'Unused',
        ];
    }

    /**
     * @return array
     */
    public static function getDcidIndex()
    {
        return [
            self::DCID_VXCHANGE => [79],
            self::DCID_SERVERIUS => [82, 86],
            self::DCID_DATALINE => [84],
            self::DCID_WEBAIR => [85],
        ];
    }

    /**
     * @return array
     */
    public static function getDcidLabels()
    {
        return [
            self::DCID_ALL => 'All',
            self::DCID_VXCHANGE => 'Vxchange',
            self::DCID_SERVERIUS => 'Serverius',
            self::DCID_DATALINE => 'Dataline',
            self::DCID_WEBAIR => 'Webair',
        ];
    }


    /**
     * @return array
     */
    public static function getLimitLabels()
    {
        return [
            self::LIMIT_50 => '50 per page',
            self::LIMIT_100 => '100 per page',
            self::LIMIT_200 => '200 per page',
        ];
    }

    /**
     * @return array
     */
    public static function getOrderLabels()
    {
        return [
            self::ORDER_IP => 'IP',
            self::ORDER_VLAN => 'VLAN',
            self::ORDER_PTR => 'PTR',
            self::ORDER_USED => 'Used by',
        ];
    }

    /**
     * Create query to grid show and check $_GET params fo filter
     * @return mixed
     */
    public function createGridQuery()
    {
        $query = Capsule::table('ip');

        if (isset($_GET['order']) && $_GET['order'] != '') {
            $order = $_GET['order'];
        } else {
            $order = self::ORDER_BY;
        }
        /**
         * fix order by ip string to integer
         */
        if ($order == self::ORDER_BY) {
            $order = Capsule::raw('cast(' . $order . ' as unsigned)');
        }

        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $this->limit = $_GET['limit'];
        }

        if (isset($_GET['dc']) && $_GET['dc'] != '' && isset(self::getDcidIndex()[$_GET['dc']])) {
            $query->whereIn('dc_id', self::getDcidIndex()[$_GET['dc']]);
        }

        if (isset($_GET['status']) && $_GET['status'] != '' && $_GET['status'] != 0) {
            $query->where('status', $_GET['status']);
        }

        if (isset($_GET['key']) && isset($_GET['free'])) {
            $key = substr($_GET['key'], 0, strrpos($_GET['key'], '.')) . '%';
            $query->whereNull('used_by')
                ->where('ip', 'like', $key);
        }

        if (isset($_GET['ip']) && $_GET['ip'] != '') {
            $key = $_GET['ip'] . '%';
            $query->where('ip', 'like', $key);
        }

        if (isset($_GET['sname']) && $_GET['sname'] != '') {
            $key = $_GET['sname'] . '%';
            $query->where('ptr', 'like', $key);
        }

        if (isset($_GET['fcount']) && is_numeric($_GET['fcount'])) {
            $free = Capsule::table('ip')
                ->select(Capsule::raw('ip, count(ip) as free'))
                ->whereNull('used_by')
                ->groupBy(Capsule::raw('SUBSTRING_INDEX(ip,".",3)'))
                ->having('free', '=', $_GET['fcount'])
                ->get();
            /**
             * If not found free subnets return empty result
             */
            if (empty($free)) {
                $query->where('ip', false);
            } else {
                $query->where(function ($subQuery) use ($free) {
                    foreach ($free as $subnet) {
                        $key = substr($subnet->ip, 0, strrpos($subnet->ip, '.')) . '%';
                        $subQuery->orWhere('ip', 'like', $key);
                    }
                });
            }
        }
        $query->take($this->limit)
            ->orderBy($order, 'asc');

        $data['count'] = $query->count();
        $data['data'] = $query->get();

        return $data;
    }

    /**
     * Create query to list show
     * @return array
     */
    public function createListQuery()
    {
        $result = [];
        $data = ['total' => 0, 'free' => 0, 'unused' => 0];
        $query = Capsule::table('ip')
            ->select(Capsule::raw('ip, count(ip) as count'))
            ->where('vlan', (integer)$this->vlan)
            ->groupBy(Capsule::raw('SUBSTRING_INDEX(ip,".",3)'));

        $total = $query->get();
        foreach ($total as $value) {
            $result[$value->ip]['total'] = $value->count;
            $result[$value->ip]['free'] = 0;
            $data['total'] += $value->count;
        }
        $free = $query->whereNull('used_by')->get();
        foreach ($free as $value) {
            $result[$value->ip]['free'] = $value->count;
            $data['free'] += $value->count;
            if ($result[$value->ip]['free'] == $result[$value->ip]['total']) {
                $data['unused']++;
            }
        }
        return ['result' => $result, 'data' => $data];
    }
}