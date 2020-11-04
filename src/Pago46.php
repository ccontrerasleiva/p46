<?php

namespace quickex\Pago46;

use quickex\Pago46\Base;

class Pago46
{
    private $merchant = [];
    private $request = [];
    private $env = 'testing';
    private $header = [];
    public function __construct()
    {
        $this->merchant = [
            'key'       =>  getenv('MERCHANT_KEY'),
            'secret'    =>  getenv('MERCHANT_SECRET')
        ];
    }

    public function getOrders()
    {
        $this->request = ['method' => 'GET', 'path' => '%2Fmerchant%2Forders%2F'];
        $this->header = Base::setHeader($this->merchant, $this->request, false);
        $api = Base::call('merchant/orders/', $this->request['method'], false);
        return $api;
    }

    public function getOrderByID($id)
    {
        $this->request = ['method' => 'GET', 'path' => "%2Fmerchant%2Forder%2F{$id}"];
        $this->header = $this->setHeader(false);
        $api = $this->callApi("merchant/order/{$id}", $this->request['method'], false);
        return $api;
    }

    public function getOrderByNotificationID($id)
    {
        $this->request = ['method' => 'GET', 'path' => "%2Fmerchant%2Forder%2F{$id}"];
        $this->header = $this->setHeader(false);
        $api = $this->callApi("merchant/notification/{$id}", $this->request['method'], false);
        return $api;
    }

    public function newOrder($order)
    {
        $this->request = ['method' => 'POST', 'path' => '%2Fmerchant%2Forders%2F'];
        $concatenateParams = '';
        foreach ($order as $k => $v) {
            $value = urlencode($v);
            $concatenateParams .= "&{$k}={$value}";
        }
        $this->header = $this->setHeader($concatenateParams);
        $api = $this->callApi("merchant/orders/", $this->request['method'], $order);
        return $api;
    }
}
