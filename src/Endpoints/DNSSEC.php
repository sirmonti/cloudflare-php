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
class DNSSEC implements API
{
    use BodyAccessorTrait;

    private $adapter;

    private function patch(string $zoneID, string $status): stdClass
    {
        $response = $this->adapter->patch('zones/' . $zoneID . '/dnssec', ['status' => $status]);
        $this->body = json_decode($response->getBody());
        return $this->body->result;
    }

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function get(string $zoneID): stdClass
    {
        $response = $this->adapter->get('zones/' . $zoneID . '/dnssec');
        $this->body = json_decode($response->getBody());
        return $this->body->result;
    }

    /**
     * Enable DNSSEC for this domain
     *
     * @param string $zoneID Zone identifier of the domain
     * @return stdClass DNSSEC data for the domain
     */
    public function enable(string $zoneID): stdClass
    {
        return $this->patch($zoneID, 'active');
    }

    /**
     * Disable DNSSEC for this domain
     *
     * @param string $zoneID Zone identifier of the domain
     * @return stdClass DNSSEC data for the domain
     */
    public function disable(string $zoneID): stdClass
    {
        return $this->patch($zoneID, 'disabled');
    }

    /**
     * Get the DNSSEC status for a domain
     *
     * @param string $zoneID ID of the domain
     * @return string Status code
     */
    public function getStatus(string $zoneID):string
    {
        return $this->get($zoneID)->status;
    }

    /**
     * Get the DS DNS record content
     *
     * This method returns the content for the DS that
     * must be created on domain registry in order to
     * enable DNSSEC support
     *
     * @return string Content for DS DNS record
     */
    public function getDS(string $zoneID):string
    {
        $sec=$this->get($zoneID);
        if ((string)@$sec->digest=='') {
            return '';
        }
        return sprintf('%d %d %d %s', $sec->key_tag, $sec->algorithm, $sec->digest_type, $sec->digest);
    }
}
