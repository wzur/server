<?php
/**
 * @package plugins.voicebase
 * @subpackage api.objects
 */
class KalturaVoicebaseJobProviderData extends KalturaIntegrationJobProviderData
{
	/**
	 * Entry ID
	 * @var string
	 */
	public $entryId;

	/**
	 * Flavor ID
	 * @var string
	 */
	public $flavorAssetId;

	/**
	 * input Transcript-asset ID
	 * @var string
	 */
	public $transcriptId;
 	
	/**
	 * output Transcript-asset IDs
	 * @var string
	 * @readonly
	 */
	public $outputTranscriptIds;
	    
	/**
	 * Caption formats
	 * @var string
	 */
	public $captionAssetFormats;
	    
	/**
	 * Api key for service provider
	 * @var string
	 * @readonly
	 */
	public $apiKey;
	    
	/**
	 * Api key for service provider
	 * @var string
	 * @readonly
	 */
	public $apiPassword;
	    
	/**
	 * Transcript content language
	 * @var KalturaLanguage
	 */
	public $spokenLanguage;

	/**
	 * Partner id
	 * @var string
	 * @readonly
	 */
	public $partnerId;
    
	private static $map_between_objects = array
	(
		"entryId",
		"flavorAssetId",
		"transcriptId" => "inputTranscriptId",
		"outputTranscriptIds",
		"captionAssetFormats",
		"apiKey",
		"apiPassword",
		"spokenLanguage",
		"partnerId",
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
