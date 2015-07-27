<?php
/**
 * A representation of a live stream recording entry configuration
 * 
 * @package api
 * @subpackage objects
 */
class KalturaLiveEntryStatusOptions extends KalturaLiveStatusOptions
{
	private static $mapBetweenObjects = array
	(
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
			$dbObject = new kLiveEntryStatusOptions();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
}