<?php

namespace WHMCS\Module\Addon\Ip;

/**
 * Class IpFilter
 * @package WHMCS\Module\Addon\Ip
 */
class IpFilter
{

    /**
     * Create form to filter in sidebar
     * @return string
     */
    public function createForm()
    {
        $header = '<span class="header">Фильтр IP</span>';
        $form = '<div class="smallfont"><form method="get" action="addonmodules.php">';
        $form .= '<input type="hidden" name="module" value="ip">';
        $form .= $this->createInputText([
            'label' => 'IP:',
            'name' => 'ip',
            'id' => 'ip-field',
            'placeholder' => 'IP',
        ]);
        $form .= $this->createInputText([
            'label' => 'Free ips in subnet:',
            'name' => 'fcount',
            'id' => 'fcount-field',
            'placeholder' => 'Free ips in subnet',
        ]);
        $form .= $this->createInputText([
            'label' => 'Server name:',
            'name' => 'sname',
            'id' => 'sname-field',
            'placeholder' => 'Server name',
        ]);
        $form .= $this->createDropdown(IpSearch::getDcidLabels(), [
            'label' => 'DC:',
            'name' => 'dc',
            'id' => 'dc-select'
        ]);
        $form .= $this->createDropdown(IpSearch::getStatusLabels(), [
            'label' => 'Status:',
            'name' => 'status',
            'id' => 'status-select
            '
        ]);
        $form .= $this->createDropdown(IpSearch::getOrderLabels(), [
            'label' => 'Order by:',
            'name' => 'order',
            'id' => 'order-select'
        ]);
        $form .= $this->createDropdown(IpSearch::getLimitLabels(), [
            'label' => 'Show:',
            'name' => 'limit',
            'id' => 'limit-select
            '
        ]);
        $form .= '<div class="input-group input-group-sm">
                        <div class="input-group-btn">
                            <input type="submit" value="Show" class="btn btn-success">
                        </div>
                    </div>';
        $form .= '</form></div><br>';
        $result = $header . $form;
        return $result;
    }

    /**
     * Create input text with options
     * @param array $options
     * @return string
     */
    private function createInputText($options = ['label' => '', 'name' => '', 'id' => '', 'placeholder' => ''])
    {
        $field = '<div class="form-group">';
        if (!empty($options['label'])) {
            $field .= '<label for="' . $options['id'] . '">' . $options['label'] . '</label>';
        }
        $field .= '<input type="text" name="' . $options['name'] . '" class="form-control" value="' . (isset($_GET[$options['name']]) && $_GET[$options['name']] != '' ? $_GET[$options['name']] : '') . '" placeholder="' . $options['placeholder'] . '" id="' . $options['id'] . '">';
        $field .= '</div>';
        return $field;
    }

    /**
     * Create dropdownlist with data and options
     * @param $data
     * @param array $options
     * @return string
     */
    private function createDropdown($data, $options = ['label' => '', 'name' => '', 'id' => ''])
    {
        $field = '<div class="form-group">';
        if (!empty($options['label'])) {
            $field .= '<label for="' . $options['id'] . '">' . $options['label'] . '</label>';
        }
        $field .= '<select name="' . $options['name'] . '" id="' . $options['id'] . '" class="form-control input-sm">';
        foreach ($data as $key => $value) {
            if (isset($_GET[$options['name']]) && $_GET[$options['name']] == $key) {
                $field .= '<option selected value="' . $key . '">' . $value . '</option>';
            } else {
                $field .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        $field .= '</select></div>';

        return $field;
    }
}