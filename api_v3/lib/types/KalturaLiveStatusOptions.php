<?php
/**
 * A representation of a live stream recording entry configuration
 * 
 * @package api
 * @subpackage objects
 */
class KalturaLiveStatusOptions extends KalturaObject
{
	/**
	 * @var string
	 */
	public $hostname;
	
	/**
	 * @var KalturaMediaServerIndex
	 */
	public $mediaServerIndex;
	
	/**
	 * @var string
	 */
	public $applicationName;
	
	/**
	 * @var int
	 */
	public $status;
	
	private static $mapBetweenObjects = array
	(
		"hostname",
		"mediaServerIndex",
		"applicationName",
		"status"
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kLiveStatusOptions();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}