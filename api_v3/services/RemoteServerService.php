<?php
/**
 * Edge Server service
 *
 * @service remoteServer
 * @package api
 * @subpackage services
 */
class RemoteServerService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$partnerId = $this->getPartnerId();
		if(!$this->getPartner()->getEnabledService(PermissionName::FEATURE_REMOTE_SERVER))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
			
		$this->applyPartnerFilterForClass('remoteServer');
	}
	
	/**
	 * Adds a remote server to the Kaltura DB.
	 *
	 * @action add
	 * @param KalturaRemoteServer $remoteServer
	 * @return KalturaRemoteServer
	 */
	function addAction(KalturaRemoteServer $remoteServer)
	{	
		if(!$remoteServer->status)
			$remoteServer->status = KalturaRemoteServerStatus::DISABLED; 
		
		$dbRemoteServer = $remoteServer->toInsertableObject();
		$dbRemoteServer->setPartnerId($this->getPartnerId());
		$dbRemoteServer->save();
		
		$remoteServer = KalturaRemoteServerFactory::getInstanceByType($dbRemoteServer->getType());
		$remoteServer->fromObject($dbRemoteServer, $this->getResponseProfile());
		return $remoteServer;
	}
	
	/**
	 * Get remote server by id
	 * 
	 * @action get
	 * @param int $remoteServerId
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @return KalturaRemoteServer
	 */
	function getAction($remoteServerId)
	{
		$dbRemoteServer = RemoteServerPeer::retrieveByPK($remoteServerId);
		if (!$dbRemoteServer)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $remoteServerId);
		
		$remoteServer = KalturaRemoteServerFactory::getInstanceByType($dbRemoteServer->getType());
		$remoteServer->fromObject($dbRemoteServer, $this->getResponseProfile());
		return $remoteServer;
	}
	
	/**
	 * Update remote server by id 
	 * 
	 * @action update
	 * @param int $remoteServerId
	 * @param KalturaRemoteServer $remoteServer
	 * @return KalturaRemoteServer
	 */
	function updateAction($remoteServerId, KalturaRemoteServer $remoteServer)
	{
		$dbRemoteServer = RemoteServerPeer::retrieveByPK($remoteServerId);
		if (!$dbRemoteServer)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $remoteServerId);
			
		$dbRemoteServer = $remoteServer->toUpdatableObject($dbRemoteServer);
		$dbRemoteServer->save();
		
		$remoteServer = KalturaRemoteServerFactory::getInstanceByType($dbRemoteServer->getType());
		$remoteServer->fromObject($dbRemoteServer, $this->getResponseProfile());
		return $remoteServer;
	}
	
	/**
	 * delete remote server by id
	 *
	 * @action delete
	 * @param string $remoteServerId
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	function deleteAction($remoteServerId)
	{
		$dbRemoteServer = RemoteServerPeer::retrieveByPK($remoteServerId);
		if(!$dbRemoteServer)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $remoteServerId);
	
		$dbRemoteServer->setStatus(RemoteServerStatus::DELETED);
		$dbRemoteServer->save();
	}
	
	/**	
	 * @action list
	 * @param KalturaRemoteServerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaRemoteServerListResponse
	 */
	public function listAction(KalturaRemoteServerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if(!$filter)
			$filter = new KalturaRemoteServerFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getTypeListResponse($pager, $this->getResponseProfile(), null);
	}
	
	/**
	 * Update remote server status
	 *
	 * @action reportStatus
	 * @param string $hostName
	 * @return KalturaRemoteServer
	 */
	function reportStatusAction($hostName)
	{
		$dbRemoteServer = RemoteServerPeer::retrieveByPartnerIdAndHostName($this->getPartnerId(), $hostName);
		if(!$dbRemoteServer)
			throw new KalturaAPIException(KalturaErrors::EDGE_SERVER_NOT_FOUND, $hostName);
	
		$dbRemoteServer->setHeartbeatTime(time());
		$dbRemoteServer->save();
	
		$remoteServer = KalturaRemoteServerFactory::getInstanceByType($dbRemoteServer->getType());
		$remoteServer->fromObject($dbRemoteServer, $this->getResponseProfile());
		return $remoteServer;
	}
}
