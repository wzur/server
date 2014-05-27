<?php
/**
 * Subclass for representing a row from the 'asset' table, used for timed_thumb_assets
 *
 * @package plugins.thumbCuePoint
 * @subpackage model
 */ 
class timedThumbAsset extends thumbAsset
{
	const CUSTOM_DATA_FIELD_OFFSET = "offest";

	/* (non-PHPdoc)
	 * @see Baseasset::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ThumbCuePointPlugin::getAssetTypeCoreValue(timedThumbAssetType::TIMED_THUMB_ASSET));
	}

	public function getOffset()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_OFFSET);}
	public function setOffset($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_OFFSET, $v);}

}