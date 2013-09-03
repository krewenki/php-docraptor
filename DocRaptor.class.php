<?php

/**
 * DocRaptor
 *
 *
 * @author Warren Krewenki
 **/
class DocRaptor {

  protected $api_key;
  protected $document_content;
  protected $document_url;
  protected $document_type;
  protected $name;
  protected $test;
  protected $strict;
  protected $url_protocol;
  protected $base_url;

  public function __construct($api_key=null){
    if(!is_null($api_key)){
      $this->api_key = $api_key;
    }
    $this->test = false;
    $this->setDocumentType('pdf');
    $this->setSecure(true);
    $this->setStrict('html');
    return true;
  }

  public function setAPIKey($api_key=null){
    if(!is_null($api_key)){
      $this->api_key = $api_key;
    }
    return $this;
  }

  public function setDocumentContent($document_content=null){
    $this->document_content = $document_content;
    return $this;
  }

  public function setDocumentUrl($document_url){
    $this->document_url = $document_url;
    return $this;
  }

  public function setDocumentType($document_type){
    $document_type = strtolower($document_type);
    $this->type = $document_type == 'pdf' || $document_type == 'xls' ? $document_type : 'pdf';
    return $this;
  }

  public function setName($name){
    $this->name = $name;
    return $this;
  }

  public function setTest($test=false){
    $this->test = (bool)$test;
    return $this;
  }

  public function setStrict($strict){
    $this->strict = $strict;
    return $this;
  }

  public function setSecure($secure_url=false){
    $this->url_protocol = $secure_url ? 'https' : 'http';
    return $this;
  }

  public function setBaseUrl($base_url){
    $this->base_url = $base_url;
    return $this;
  }

  public function fetchDocument($filename = false){
    if($this->api_key != ''){
      $url = $this->url_protocol . '://docraptor.com/docs?user_credentials=' . $this->api_key;
      $fields = array(
        'doc[document_type]'=>$this->type,
        'doc[name]'=>$this->name,
        'doc[test]'=>$this->test,
        'doc[strict]'=>$this->strict
      );
      if ( !empty($this->base_url)){
        $fields['doc[prince_options][base_url]'] = $this->base_url;
      }
      if ( !empty($this->document_content) ){
        $fields['doc[document_content]'] = urlencode($this->document_content);
      } else {
        $fields['doc[document_url]'] = urlencode($this->document_url);
      }

      foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
      rtrim($fields_string,'&');
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_POST,count($fields));
      curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      if($result = curl_exec($ch)) {
        if($filename){
          file_put_contents($filename,$result);
        }
      } else {
        echo ('Error: ' . curl_error($ch));
      }
      //close connection
      curl_close($ch);
      return $filename ? true : $result;
    }
  }
}

?>
