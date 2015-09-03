<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaServer extends KalturaEdgeServer
{	
	/**
	 * Media server app prefix
	 *
	 * @var string
	 */
	public $appPrefix;
	
	/**
	 * Media server app prefix
	 *
	 * @var KalturaKeyValueArray
	 */
	public $protocolPort;
	
	private static $mapBetweenObjects = array
	(
		'appPrefix',
		'protocolPort',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new MediaServer();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
}