<?php

namespace Cloudflare\API\Adapter;

use simpleHTTP as SHTTP;
use Cloudflare\API\Auth\Auth;
use Psr\Http\Message\ResponseInterface;

class simpleHTTPAdapter implements Adapter
{
    private string $url;
    private array $headers;
    private SHTTP $http;

    /**
     * @inheritDoc
     */
    public function __construct(Auth $auth, string $baseURI = null)
    {
        if ($baseURI === null) {
            $baseURI = 'https://api.cloudflare.com/client/v4/';
        }

        $headers = $auth->getHeaders();
        $this->headers=[];
        foreach($headers as $key=>$val) {
          $this->headers[]=$key.': '.$val;
        }

        $this->url=$baseURI;
        $this->http=new SHTTP(1,true);
        $this->http->setExtraHeaders($this->headers);
    }

    private function buildheaders(array $headers):array {
        $tmp=[];
        foreach($headers as $key=>$val) {
          $tmp[]=$key.': '.$val;
        }
        return $tmp;
    }

    private function request(string $method, string $uri, array $data=[], array $headers=[]): ResponseInterface {
        if(count($headers)>0) {
          $headers=$this->buildheaders($headers);
        }
        $url=$this->url.$uri;
        switch($method) {
          case 'get':
            $this->http->get($url,$headers);
            break;
          case 'post':
            $this->http->postJSON($url,$data,$headers);
            break;
          case 'put':
            $this->http->putJSON($url,$data,$headers);
            break;
          case 'patch':
            $this->http->patchJSON($url,$data,$headers);
            break;
          case 'delete':
            $this->http->delete($url,$headers);
            break;
        }
        return $this->http->PSRResponse();
    }

    /**
     * @inheritDoc
     */
    public function get(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        $pars=http_build_query($data);
        return $this->request('get', $uri.'?'.$pars, [], $headers);
    }

    /**
     * @inheritDoc
     */
    public function post(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        return $this->request('post', $uri, $data, $headers);
    }

    /**
     * @inheritDoc
     */
    public function put(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        return $this->request('put', $uri, $data, $headers);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        return $this->request('patch', $uri, $data, $headers);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $uri, array $data = [], array $headers = []): ResponseInterface
    {
        return $this->request('delete', $uri, $data, $headers);
    }


}
