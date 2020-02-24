<?php
namespace Tikivn\Pegasus\Catalog;

use Tikivn\Pegasus\HttpClient as PegasusClient;

class Repository
{
    private $pegasusClient;

    public function __construct(PegasusClient $pegasusClient) {
        $this->pegasusClient = $pegasusClient;
    }

    public function findByIds(array $ids) : Model\CollectionProduct
    {
        $response = $this->pegasusClient->get('/v1/products?'.\http_build_query(['ids' => implode(',',$ids)]));
        $rawData = json_decode($response->getBody()->getContents(), true);
        $productCollection = Model\CollectionProduct::hydrate($rawData);
        return $productCollection;
    }

    public function findBySkus(array $skus) : Model\CollectionProduct
    {
        $response = $this->pegasusClient->get('/v1/products?'.\http_build_query(['skus' => implode(',',$skus)]));
        $rawData = json_decode($response->getBody()->getContents(), true);
        $productCollection = Model\CollectionProduct::hydrate($rawData);
        return $productCollection;
    }
}