<?php

namespace quickex\Pago46;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class Base
{

    protected static $hmacAlgo = 'sha256';
    protected static $enpoint = '';
    public function __construct()
    {
        // constructor body
    }

    public static function getEnv()
    {
        return static::$env;
    }

    public function getClientHttp()
    {
        return $this->clientHttp;
    }

    public function setClientHttp()
    {
        $this->clientHttp = new \GuzzleHttp\Client();
    }

    public static function setEnv($env)
    {
        $permitted = ['production', 'sandbox'];

        if (!in_array($env, $permitted)) {
            static::$env = false;
            return false;
        }
        $env = getenv('URL');
        static::$env = $env;
    }

    public static function setHeader($merchant, $request, $concatenateParams = false)
    {
        $unixTimestamp = date_timestamp_get(date_create());

        $encryptBase = "{$merchant['key']}&{$unixTimestamp}&{$request['method']}&{$request['path']}";

        if ($concatenateParams) {
            $encryptBase = "{$encryptBase}{$concatenateParams}";
        }

        $hmac = hash_hmac(static::$hmacAlgo, $encryptBase, $merchant['secret']);

        return [
            'Content-Type: application/json',
            "merchant-key: {$merchant['key']}",
            "message-hash: {$hmac}",
            "message-date: {$unixTimestamp}"
        ];
    }

    public static function call($url, $method = 'GET', $data = false)
    {
        $url = (substr(static::$endpoint, -1) == '/') ? static::$endpoint . $url : static::$enpoint . '/' . $url;
        $data = json_encode($data);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        switch (strtolower($method)) {
            case 'post':
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Hack Non-SSL
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // Hack Non-SSL
        curl_setopt($curl, CURLOPT_HTTPHEADER, static::$header);

        $jsonResult = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($jsonResult);

        return $result;
    }
}