<?php namespace DocRaptor;

/**
 * Class HttpClientMock
 * @package DocRaptor
 */
class HttpClientMock implements HttpTransferInterface
{
    /**
     * @param $uri
     * @param array $postFields
     * @return string
     */
    public function doPost($uri, array $postFields)
    {
        return 'success';
    }
}