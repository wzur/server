<?php
/**
 * @package plugins.thumbCuePoint
 * @subpackage lib.enum
 */
class thumbCuePointType implements IKalturaPluginEnum, CuePointType
{
	const THUMB = 'Thumb';
	
	public static function getAdditionalValues()
	{
		return array(
			'THUMB' => self::THUMB,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
