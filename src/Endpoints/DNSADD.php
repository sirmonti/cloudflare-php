<?php

namespace Cloudflare\API\Endpoints;

use stdClass;
use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Traits\BodyAccessorTrait;

/**
 * Add DNSSEC enable/disable to Cloudflare SDK API
 *
 * Surprisingly, Cloudflare SDK does not provide functions to manage
 * DNSSEC support, although the REST API does provide the webservice.
 */
class DNSADD extends DNS implements API
{
    public function addA(string $zoneID, string $hostname, string $IP, int $ttl = 0, bool $proxied = false): bool
    {
        return $this->addRecord($zoneID,'A',$hostname,$IP,$ttl,$proxied);
    }

    public function addAAAA(string $zoneID, string $hostname, string $IP, int $ttl = 0, bool $proxied = false): bool
    {
        return $this->addRecord($zoneID,'AAAA',$hostname,$IP,$ttl,$proxied);
    }

    public function addCNAME(string $zoneID, string $hostname, string $IP, int $ttl = 0, bool $proxied = false): bool
    {
        return $this->addRecord($zoneID,'CNAME',$hostname,$IP,$ttl,$proxied);
    }

    public function addTXT(string $zoneID, string $hostname, string $data, int $ttl = 0): bool
    {
        return $this->addRecord($zoneID,'TXT',$hostname,$data,$ttl,false);
    }

    public function addMX(string $zoneID, string $hostname, string $mx, string $priority, int $ttl = 0): bool
    {
        return $this->addRecord($zoneID,'MX',$hostname,$IP,$ttl,false,$priority);
    }

    public function addNS(string $zoneID, string $hostname, string $NS, int $ttl = 0): bool
    {
        return $this->addRecord($zoneID,'NS',$hostname,$NS,$ttl,false);
    }

    public function addCAA(string $zoneID, string $hostname, bool $wild, string $ca, int $ttl = 0): bool
    {
        $data=[
            'flags'=>0,
            'tag'=> $wild ? 'issuewild':'issue',
            'value'=>$ca
        ];
        return $this->addRecord($zoneID,'CAA',$hostname,$NS,$ttl,false,'',$data);
    }

    public function addSSHFP(string $zoneID, string $hostname, int $algorithm, int $type, string $hash, int $ttl = 0): bool
    {
        $data=[
            'algorithm'=>$algorithm,
            'type'=>$type,
            'fingerprint'=>$hash
        ];
        return $this->addRecord($zoneID,'SSHFP',$hostname,'',$ttl,false,'',$data);
    }
}
