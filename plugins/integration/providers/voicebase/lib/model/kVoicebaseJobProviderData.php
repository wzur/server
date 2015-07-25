<?php
/**
 * @package plugins.voicebase
 * @subpackage model.data
 */
class kVoicebaseJobProviderData extends kIntegrationJobProviderData
{
	const CUSTOM_DATA_SPOKEN_LANGUAGE = "spokenLanguage";
	
	/**
	 * @var string
	 */
	private $entryId;
	
	/**
	 * @var string
	 */
	private $flavorAssetId;

	/**
	 * @var string
	 */
	private $inputTranscriptId;
    
	/**
	 * @var string
	 */
	private $captionAssetFormats;
	
	/**
	 * @var string
	 */
	private $apiKey;
	
	/**
	 * @var string
	 */
	private $apiPassword;
	
	/**
	 * @var string
	 */
	private $spokenLanguage;

	/**
	 * @var string
	 */
	private $partnerId;
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}

	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
	}
	
	/**
	 * @return string
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}
	
	/**
	 * @param string $flavorAssetId
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}
	
	/**
	 * @return string
	 */
	public function getInputTranscriptId()
	{
		return $this->inputTranscriptId;
	}
    
	/**
	 * @param string inputTranscriptId
	 */
	public function setInputTranscriptId($inputTranscriptId)
	{
		$this->inputTranscriptId = $inputTranscriptId;
	}
    
	/**
	 * @return string
	 */
	public function getCaptionAssetFormats()
	{
		return $this->captionAssetFormats;
	}
	
	/**
	 * @param string $captionAssetFormats
	 */
	public function setCaptionAssetFormats($captionAssetFormats)
	{
		$this->captionAssetFormats = $captionAssetFormats;
	}
	
	/**
	 * @return string
	 */
	public function getApiKey()
	{
		return $this->apiKey;
	}
	
	/**
	 * @param string $apiKey
	 */
	public function setApiKey($apiKey)
	{
		$this->apiKey = $apiKey;
	}
	
	/**
	 * @return string
	 */
	public function getApiPassword()
	{
		return $this->apiPassword;
	}
	
	/**
	 * @param string $apiPassword
	 */
	public function setApiPassword($apiPassword)
	{
		$this->apiPassword = $apiPassword;
	}
	
	/**
	 * @return string
	 */
	public function getSpokenLanguage()
	{
		return $this->spokenLanguage;
	}
	
	/**
	 * @param string $spokenLanguage
	 */
	public function setSpokenLanguage($spokenLanguage)
	{
		$this->spokenLanguage = $spokenLanguage;
	}

	/**
	 * @return string
	 */
	public function getPartnerId()
	{
		return $this->partnerId;
	}

	/**
	 * @param string $partnerId
	 */
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}
}
