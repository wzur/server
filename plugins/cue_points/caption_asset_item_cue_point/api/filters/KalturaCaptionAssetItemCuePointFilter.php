<?php
/**
 * @package plugins.captionAssetItemCuePoint
 * @subpackage api.filters
 */
class KalturaCaptionAssetItemCuePointFilter extends KalturaCaptionAssetItemCuePointBaseFilter
{
	static private $map_between_objects = array
	(
		"contentLike" => "_like_text",
		"contentMultiLikeOr" => "_mlikeor_text",
		"contentMultiLikeAnd" => "_mlikeand_text",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
