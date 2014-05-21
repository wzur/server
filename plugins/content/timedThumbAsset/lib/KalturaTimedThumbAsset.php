<?php
/**
 * @package plugins.timedThumbAsset
 * @subpackage api.objects
 */
class KalturaTimedThumbAsset extends KalturaThumbAsset  
{
	/**
	 * The time offset of the thumbnail relative to video
	 * @var float
	 */
	public $offset;

	
	private static $map_between_objects = array
	(
		"offset",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new timedThumbAsset();
		
		return parent::toInsertableObject ($object_to_fill, $props_to_skip);
	}


	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		//ToDo
	}
}
