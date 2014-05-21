<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.enum
 */
class CuePointFileAssetObjectType implements IKalturaPluginEnum, FileAssetObjectType
{
	const CUE_POINT_FILE_ASSET = 'CuePointFileAsset';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues() 
	{
		return array
		(
			'CUE_POINT_FILE_ASSET' => self::CUE_POINT_FILE_ASSET,
		);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() {
		return array();
		
	}
}