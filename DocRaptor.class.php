<?php

/**
 * DocRaptor
 *
 * @author Warren Krewenki
 **/
class DocRaptor {
	
	public $api_key;
	public $document_content;
	public $document_type;
	public $name;
	public $test;
	
	public function __construct($api_key=null){
		if(!is_null($api_key)){
			$this->api_key = $api_key;
		}
		$this->test = false;
		$this->setDocumentType();
		return true;
	}
	
	public function setAPIKey($api_key=null){
		if(!is_null($api_key)){
			$this->api_key = $api_key;
		}
		return true;
	}
	
	public function setDocumentContent($document_content=null){
		$this->document_content = $document_content;
		return true;
	}
	
	public function setDocumentType($document_type){
		$document_type = strtolower($document_type);
		$type = $document_type == 'pdf' || $document_type == 'xls' ? $document_type : 'pdf';
		return true;
	}
	
	public function setName($name){
		$this->name = $name;
		return true;
	}
	
	public function setTest($test=false){
		$this->test = (bool)$test;
		return true;
	}
	
	public function fetchDocument($filename = false){
		if($this->api_key != ''){
			$url = "https://docraptor.com/docs?user_credentials=".$this->api_key;
			$fields = array(
				'doc[document_content]'=>urlencode($this->document_content),
				'doc[document_type]'=>$this->document_type,
				'doc[name]'=>$this->name,
				'doc[test]'=>$this->test
			);
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
				echo 'error';
			}
			//close connection 
			curl_close($ch);
			return $filename ? true : $result;
		}

	}
}

?>