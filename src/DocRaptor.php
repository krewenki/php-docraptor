<?php namespace DocRaptor;

use Exception;

/**
 * Class ApiWrapper
 * @package DocRaptor
 */
class ApiWrapper
{

    protected $api_key;
    protected $api_url          = 'docraptor.com/docs';
    protected $name;
    protected $test             = false;
    protected $strict           = 'none';
    protected $help             = false;
    protected $url_protocol     = 'https';
    protected $base_url;
    protected $document_content;
    protected $document_url;
    protected $document_type    = 'pdf';


    /**
     * @param string|null $api_key
     *
     * @todo the null check does not really seem needed here, the object will be constructed once per request cycle
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
    public function setAPIKey($api_key)
    {
        $this->api_key = $api_key;
        return $this;
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
        $allowedValues = array('xls', 'xlsx', 'pdf');

        if (! in_array(strtolower(trim($document_type)), $allowedValues)) {
            throw new Exception('Value not accepted by method.');
        }

        $document_type = strtolower($document_type);
        return $this;
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
     * @param bool $test
     * @return $this
     */
    public function setTest($test = false)
    {
        $this->test = (bool)$test;
        return $this;
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
            throw new Exception('Value not accepted by method.');
        }

        $this->strict = $strict;
        return $this;
    }

    /**
     * @param bool $help
     * @return $this
     */
    public function setHelp($help = false)
    {
        $this->help = (bool)$help;
        return $this;
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
     * @param string $base_url
     * @return $this
     */
    public function setBaseUrl($base_url)
    {
        $this->base_url = $base_url;
        return $this;
    }

    /**
     * Main method that makes the actual API call
     *
     * @param bool|string $filename
     * @return bool|mixed
     * @throws Exception
     */
    public function fetchDocument($filename = false)
    {
        if ($this->api_key != '') {
            $url = $this->url_protocol . '://'. $this->api_url . '?user_credentials=' . $this->api_key;
            $fields = array(
                'doc[document_type]' => $this->document_type,
                'doc[name]'          => $this->name,
                'doc[help]'          => $this->help,
                'doc[test]'          => $this->test,
                'doc[strict]'        => $this->strict
            );

            if (!empty($this->base_url)) {
                $fields['doc[prince_options][base_url]'] = $this->base_url;
            }

            if (!empty($this->document_content)) {
                $fields['doc[document_content]'] = urlencode($this->document_content);
            } else {
                $fields['doc[document_url]'] = urlencode($this->document_url);
            }

            $fields_string = http_build_query($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            if ($result) {
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($http_code != 200) {
                    //@todo silently erroring out is probably not the best way here
                    return false;
                }

                if ($filename) {
                    file_put_contents($filename, $result);
                }

            } else {
                throw new Exception('Curl error: ' . curl_error($ch));
            }

            curl_close($ch);

            return $filename ? true : $result;
        }

        return false;
    }
}
