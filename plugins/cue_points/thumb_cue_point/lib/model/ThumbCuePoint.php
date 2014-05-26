<?php


/**
 * @package plugins.thumbCuePoint
 * @subpackage model
 */
class ThumbCuePoint extends CuePoint implements IMetadataObject
{
	const CUSTOM_DATA_FIELD_THUMB_ASSET_ID = 'thumbAssetId';
	
	public function __construct() 
	{
		parent::__construct();
		$this->applyDefaultValues();
	}
	
	/* (non-PHPdoc)
	 * @see CuePoint::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$timedThumbAsset = new timedThumbasset();
		$timedThumbAsset->setOffset($this->getStartTime());
		$timedThumbAsset->setStatus(thumbAsset::ASSET_STATUS_QUEUED);
		$timedThumbAsset->save();
		
		$this->setTimedThumbAssetId($timedThumbAsset->getId());
		$this->setCustomDataObj();
		
		return parent::preInsert($con);
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setType(ThumbCuePointPlugin::getCuePointTypeCoreValue(thumbCuePointType::THUMB));
	}
	
	public function setTimedThumbAssetId($v)		{return $this->putInCustomData(self::CUSTOM_DATA_FIELD_THUMB_ASSET_ID, (string)$v);}
	public function getTimedThumbAssetId()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_THUMB_ASSET_ID);}
	
	/* (non-PHPdoc)
	 * @see IMetadataObject::getMetadataObjectType()
	 */
	public function getMetadataObjectType()
	{
		return ThumbCuePointMetadataPlugin::getMetadataObjectTypeCoreValue(ThumbCuePointMetadataObjectType::THUMB_CUE_POINT);
	}
}
