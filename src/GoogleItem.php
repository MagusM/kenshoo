<?php

namespace Kenshoo;

use Curl\Curl;

class GoogleItem {

    const URL = "https://www.googleapis.com/customsearch/v1";

    private $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function getFullQueryParams($q, $key = null, $cx = null) {
        return [
            "key" => $key == null ? "AIzaSyAC-JlJLyhoeQx-3Lzx1B1XZbkSaq9YWLw" : $key,
            "cx"  => $cx  == null ? "017576662512468239146:omuauf_lfve" : $cx,
            "q"   => $q
        ];
    }

    public function getGoogleResult($q, $resultLimit = 5) {
        $this->curl->get(self::URL, $this->getFullQueryParams($q));
        if ($this->curl->error) {
            throw new \Exception('Curl error: ' . $this->curl->error_code);
        }
        $body = $this->curl->response;
        // now, process the JSON string
        $json = json_decode($body, true);

        return array_slice($json['items'], 0, 5, true);
    }

}