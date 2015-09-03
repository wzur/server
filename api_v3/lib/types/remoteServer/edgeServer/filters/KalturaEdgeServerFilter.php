<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaEdgeServerFilter extends KalturaEdgeServerBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = remoteServerType::NODE;
		
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
