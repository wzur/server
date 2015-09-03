<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaRemoteServerFilter extends KalturaRemoteServerBaseFilter
{
		/* (non-PHPdoc)
		 * @see KalturaFilter::getCoreFilter()
		 */
		protected function getCoreFilter()
		{
			return new RemoteServerFilter();
		}
		
		public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
		{
			list($list, $totalCount) = $this->doGetListResponse($pager, $type);
			$response = new KalturaRemoteServerListResponse();
			$response->objects = KalturaCuePointArray::fromDbArray($list, $responseProfile);
			$response->totalCount = $totalCount;
		
			return $response;
		}
		
		protected function doGetListResponse(KalturaFilterPager $pager, $type = null)
		{
			$c = KalturaCriteria::create(RemoteServerPeer::OM_CLASS);
			
			if($type)
				$c->add(RemoteServerPeer::TYPE, $type);
			
			$remoteServerFilter = $this->toObject();
			$remoteServerFilter->attachToCriteria($c);
			$pager->attachToCriteria($c);
			
			$list = RemoteServerPeer::doSelect($c);
		
			return array($list, $c->getRecordsCount());
		}

		/* (non-PHPdoc)
		 * @see KalturaRelatedFilter::getListResponse()
		 */
		public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
		{
			return $this->getTypeListResponse($pager, $responseProfile);
		}
}
