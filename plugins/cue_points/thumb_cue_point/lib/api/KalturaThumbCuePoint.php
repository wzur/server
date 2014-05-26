<?php
/**
 * @package plugins.thumbCuePoint
 * @subpackage api.objects
 */
class KalturaThumbCuePoint extends KalturaCuePoint
{
	/**
	 * @var string
	 */
	public $timedThumbAssetId ;

	public function __construct()
	{
		$this->cuePointType = ThumbCuePointPlugin::getApiValue(thumbCuePointType::THUMB);
	}
	
	private static $map_between_objects = array
	(
		"timedThumbAssetId",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new ThumbCuePoint();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		
		//TBD Maybe add new validation for asset id is not null
		//$this->validatePropertyNotNull("timedThumbAssetId");
	}
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		//TBD Maybe add new validation that the thumb was not mannually deleted
		//$this->validatePropertyNotNull("timedThumbAssetId");
		//if(!is_null($this->endTime))
		//	$this->validateEndTime($sourceObject->getId());
			
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
