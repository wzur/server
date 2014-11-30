<?php
/**
 * @package plugins.captionAssetItemCuePoint
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaCaptionAssetItemCuePointBaseFilter extends KalturaCuePointFilter
{
	static private $map_between_objects = array
	(
		"contentLike" => "_like_content",
		"contentMultiLikeOr" => "_mlikeor_content",
		"contentMultiLikeAnd" => "_mlikeand_content",
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
	);

	static private $order_by_map = array
	(
		"+endTime" => "+end_time",
		"-endTime" => "-end_time",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/**
	 * @var string
	 */
	public $contentLike;

	/**
	 * @var string
	 */
	public $contentMultiLikeOr;

	/**
	 * @var string
	 */
	public $contentMultiLikeAnd;

	/**
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $endTimeLessThanOrEqual;
}
