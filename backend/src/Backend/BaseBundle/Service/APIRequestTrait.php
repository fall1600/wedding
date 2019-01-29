<?php
namespace Backend\BaseBundle\Service;

use GuzzleHttp\Client;

trait APIRequestTrait
{
    protected function doPost($url, $param = array())
    {
        $client = new Client();
        $response = $client->request('POST', $url, array(
            'form_params' => $param,
        ));
        return $response->getBody();
    }

    protected function doGet($url, $param = array())
    {
        $queryString = http_build_query($param);
        if($queryString !== ''){
            if (strpos($url, '?') === false) {
                $getUrl = "$url?$queryString";
            } else {
                $getUrl = "$url&$queryString";
            }
        }
        else{
            $getUrl = $url;
        }
        return file_get_contents($getUrl);
    }
}