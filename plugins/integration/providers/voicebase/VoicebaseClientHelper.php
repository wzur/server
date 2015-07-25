<?php
/**
 * @package plugins.voicebase
 */

class VoicebaseClientHelper
{
	const VOICEBASE_FAILURE_MEESAGE = "FAILURE";
	const VOICEBASE_MACHINE_COMPLETE_MEESAGE = "MACHINECOMPLETE";
	
    private $supportedLanguages = array();
    private $baseEndpointUrl = null;

    public function __construct($apiKey, $apiPassword)
    {
    	$voicebaseParamsMap = kConf::getMap('integration');
        $this->supportedLanguages = $voicebaseParamsMap['voicebaselanguages'];
    	$version = $voicebaseParamsMap['voicebase_version'];

    	$url = $voicebaseParamsMap['voicebase_url'];
    	$url .= "=$version";
    	$url .= "&apikey=$apiKey";
    	$url .= "&password=$apiPassword";
    	
    	$this->baseEndpointUrl = $url;
    }
    
    public function checkExitingExternalContent($entryId)
    {
    	$exitingEntryQueryUrl = $this->baseEndpointUrl;
    	$exitingEntryQueryUrl .= "&action=getFileStatus";
    	$exitingEntryQueryUrl .= "&externalID=$entryId";
    
    	$curlResult = self::sendAPICall($exitingEntryQueryUrl);
    	if($curlResult)
    	{
    		if ($curlResult->requestStatus == self::VOICEBASE_FAILURE_MEESAGE)
    			return false;
    		if (!$curlResult->fileStatus == self::VOICEBASE_MACHINE_COMPLETE_MEESAGE)
    			return false;
    		return true;
    	}
    	return false;
    }
    
    public function uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage)
    {
        $spokenLanguage = $this->supportedLanguages[$spokenLanguage];
    	$uploadAPIUrl = $this->baseEndpointUrl;
    	$uploadAPIUrl .= "&action=uploadMedia";
    	$uploadAPIUrl .= "&externalID=$entryId";
    	$uploadAPIUrl .= "&transcriptType=machine";
    	$uploadAPIUrl .= "&machineReadyCallBack=$callBackUrl";
        if($spokenLanguage)
            $uploadAPIUrl .= "&lang=$spokenLanguage";
    
    	$params = array("mediaURL" => $flavorUrl);
    
    	$curlResult = self::sendAPICall($uploadAPIUrl, $params);
    }
    
    private function sendAPICall($url, $params = null, $noDecoding = false)
    {
    	KalturaLog::debug("sending API call - $url");
    
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	if ($params)
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    	try
    	{
    		$result = curl_exec($ch);
    	}
    	catch(Exception $e)
    	{
    		KalturaLog::err('problem with curl - ' . $e->getMessage);
    		curl_close($ch);
    		throw $e;
    	}
    	if(!$noDecoding)
    	{
    		try
    		{
    			$result = json_decode($result);
    		}
    		catch(Exception $e)
    		{
    			KalturaLog::err("bad response from service provider");
    			curl_close($ch);
    			throw $e;
    		}
    	}
    	KalturaLog::debug('result is - ' . var_dump($result));
    	curl_close($ch);
    	return $result;
    }
    
    public function getTranscriptContent($url)
    {    
    	$result = self::sendAPICall($url,null,true);
    	return $result;
    }
    
    public function updateRemoteTranscript($entryId, $transcriptContent)
    {
    	$updateTranscriptUrl = $this->baseEndpointUrl;
    	$updateTranscriptUrl .= "&action=updateTranscript";
    	$updateTranscriptUrl .= "&externalID=$entryId";
    	$params = array("transcript" => $transcriptContent);
    
    	self::sendAPICall($updateTranscriptUrl, $params);
    }
    
    public function getRemoteTranscripts($entryId, array $formats)
    {
    	$getTranscriptUrl = $this->baseEndpointUrl;
    	$getTranscriptUrl .= "&action=getTranscript";
    	$getTranscriptUrl .= "&externalID=$entryId";
    	
    	$results = array();
    	foreach($formats as $format)
    	{
    		$url = $getTranscriptUrl . "&format=$format";
    		$result = self::sendAPICall($url);
    		$results[$format] = $result->transcript;
    	}
    	return $results;
    }
    
    public function deleteRemoteFile($entryId)
    {
    	$deleteUrl = $this->baseEndpointUrl;
    	$deleteUrl .= "&action=deleteFile";
    	$deleteUrl .= "&externalID=$entryId";
    
    	$curlResult = self::sendAPICall($deleteUrl);
    }
}
