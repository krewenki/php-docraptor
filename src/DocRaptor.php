<?php namespace DocRaptor;

use DocRaptor\Exception\MissingAPIKeyException;
use Exception;
use DocRaptor\Exception\BadRequestException;
use DocRaptor\Exception\ForbiddenException;
use DocRaptor\Exception\UnauthorizedException;
use DocRaptor\Exception\UnexpectedValueException;
use DocRaptor\Exception\UnprocessableEntityException;
use InvalidArgumentException;

/**
 * Class ApiWrapper
 * @package DocRaptor
 */
class ApiWrapper
{
    // Service and HTTP
    protected $api_key;
    protected $url_protocol     = 'https';
    protected $api_url          = 'docraptor.com/docs';

    // Output document related
    protected $document_type    = 'pdf';
    protected $document_content;
    protected $document_url;

    // Document creation
    protected $strict           = 'none';
    protected $javascript       = false;

    // Meta Settings
    protected $name             = 'default';
    protected $tag;
    protected $test             = false;
    protected $help             = false;

    // Prince Options
    protected $baseurl;



    /**
     * @param string|null $api_key
     */
    public function __construct($api_key = null)
    {
        if (!is_null($api_key)) {
            $this->api_key = $api_key;
        }
    }

    /**
     * @param string $api_key
     * @return $this
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @param string|null $document_content
     * @return $this
     */
    public function setDocumentContent($document_content = null)
    {
        $this->document_content = $document_content;
        return $this;
    }

    /**
     * @param string $document_url
     * @return $this
     */
    public function setDocumentUrl($document_url)
    {
        $this->document_url = $document_url;
        return $this;
    }

    /**
     * @param string $document_type
     * @return $this
     * @throws Exception
     */
    public function setDocumentType($document_type)
    {
        $filtered = strtolower(trim($document_type));
        $allowedValues = array('xls', 'xlsx', 'pdf');

        if (! in_array($filtered, $allowedValues)) {
            throw new InvalidArgumentException(sprintf('Document type must be in %s, %s given.', implode('|', $allowedValues), $filtered));
        }

        $this->document_type = $filtered;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentType()
    {
        return $this->document_type;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @param bool $test
     * @return $this
     */
    public function setTest($test = false)
    {
        // Strict type check, needs to be conservative
        $flag = (false === $test) ? true : false;
        $this->test = $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Toggle validation of HTML by DocRaptor
     *
     * @todo should probably just be a bool flag
     *
     * @param string $strict none: no validation, html: errors out on non-parsable markup
     * @return $this
     * @throws Exception
     */
    public function setStrict($strict = 'none')
    {
        $allowedValues = array('none', 'html');

        if (! in_array(strtolower(trim($strict)), $allowedValues)) {
            throw new InvalidArgumentException(sprintf('Validation type must be in %s, %s given.', implode('|', $allowedValues), $strict));
        }

        $this->strict = $strict;
        return $this;
    }

    /**
     * @param boolean $javascript
     * @return $this
     */
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;
        return $this;
    }

    /**
     * @param bool $help
     * @return $this
     */
    public function setHelp($help = false)
    {
        // Strict type check, needs to be conservative
        $flag = (false === $help) ? true : false;
        $this->help = $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @param bool $secure_url
     * @return $this
     */
    public function setSecure($secure_url = false)
    {
        $this->url_protocol = $secure_url ? 'https' : 'http';
        return $this;
    }

    /**
     * Prince option, sets base url for assets
     *
     * @param string $baseurl
     * @return $this
     */
    public function setBaseurl($baseurl)
    {
        $this->baseurl = $baseurl;
        return $this;
    }

    /**
     * Main method that makes the actual API call
     *
     * @param bool|string $filename
     * @return bool|mixed
     * @throws Exception
     * @throws MissingAPIKeyException
     */
    public function fetchDocument($filename = false)
    {
        if (!$this->api_key) {
            throw new MissingAPIKeyException();
        }

        $uri = sprintf('%s://%s', $this->url_protocol, $this->api_url);

        $fields = array(
            'user_credentials'   => $this->api_key,
            'doc[document_type]' => $this->document_type,
            'doc[name]'          => $this->name,
            'doc[tag]'           => $this->tag,
            'doc[help]'          => $this->help,
            'doc[test]'          => $this->test,
            'doc[strict]'        => $this->strict,
            'doc[javascript]'    => $this->javascript,
        );

        if (!empty($this->baseurl)) {
            $fields['doc[prince_options][baseurl]'] = $this->baseurl;
        }

        if (!empty($this->document_content)) {
            $fields['doc[document_content]'] = $this->document_content;
        } else {
            $fields['doc[document_url]'] = $this->document_url;
        }

        $queryString = http_build_query($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if (!$result) {
            throw new Exception(sprintf('Curl error: %s', curl_error($ch)));
        }

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpStatusCode != 200) {
            switch ($httpStatusCode) {
                case 400:
                    throw new BadRequestException();
                case 401:
                    throw new UnauthorizedException();
                case 403:
                    throw new ForbiddenException();
                case 422:
                    throw new UnprocessableEntityException();
                default:
                    throw new UnexpectedValueException($httpStatusCode);
            }
        }

        if ($filename) {
            file_put_contents($filename, $result);
        }

        curl_close($ch);

        return $filename ? true : $result;
    }
}
