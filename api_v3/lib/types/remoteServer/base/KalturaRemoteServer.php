<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaRemoteServer extends KalturaObject implements IRelatedFilterable, IApiObjectFactory
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $heartbeatTime;
	
	/**
	 * remoteServer name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $name;
	
	/**
	 * remoteServer uniqe system name
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * remoteServer description
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * remoteServer host name
	 *
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $hostName;
	
	/**
	 * @var KalturaRemoteServerStatus
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var KalturaRemoteServerType
	 * @filter eq,in
	 */
	public $type;
	
	/**
	 * remoteServer tags
	 *
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $tags;
	
	/**
	 * DC where the remote server is located
	 *
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $dc;
	
	/**
	 * Id of the parent remote server
	 *
	 * @var int
	 * @filter eq,in
	 */
	public $parentId;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"createdAt",
		"updatedAt",
		"heartbeatTime",
		"name",
		"systemName",
		"description",
		"hostName",
		"status",
		"type",
		"tags",
		"dc",
		"parentId"
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validateMandatoryAttributes(true);
		$this->validateDuplications();
	
		return parent::validateForInsert($propertiesToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateMandatoryAttributes();
		$this->validateDuplications($sourceObject->getId());
				
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	public function validateMandatoryAttributes($isInsert = false)
	{
		$this->validatePropertyMinLength("hostName", 1, !$isInsert);
		
		$this->validatePropertyMinLength("name", 1, !$isInsert);
	}
	
	public function validateDuplications($edgeId = null)
	{
		if($this->hostName)		
			$this->validateHostNameDuplication($edgeId);
		
		if($this->systemName)
			$this->validateSystemNameDuplication($edgeId);
	}
	
	public function validateHostNameDuplication($edgeId = null)
	{
		$c = KalturaCriteria::create(EdgeServerPeer::OM_CLASS);
		
		if($edgeId)
			$c->add(EdgeServerPeer::ID, $edgeId, Criteria::NOT_EQUAL);
		
		$c->add(EdgeServerPeer::HOST_NAME, $this->hostName);
		$c->add(EdgeServerPeer::STATUS, array(EdgeServerStatus::ACTIVE, EdgeServerStatus::DISABLED), Criteria::IN);
		
		if(EdgeServerPeer::doCount($c))
			throw new KalturaAPIException(KalturaErrors::HOST_NAME_ALREADY_EXISTS, $this->hostName);
	}
	
	public function validateSystemNameDuplication($edgeId = null)
	{
		$c = KalturaCriteria::create(EdgeServerPeer::OM_CLASS);
	
		if($edgeId)
			$c->add(EdgeServerPeer::ID, $edgeId, Criteria::NOT_EQUAL);
	
		$c->add(EdgeServerPeer::SYSTEM_NAME, $this->systemName);
		$c->add(EdgeServerPeer::STATUS, array(EdgeServerStatus::ACTIVE, EdgeServerStatus::DISABLED), Criteria::IN);
	
		if(EdgeServerPeer::doCount($c))
			throw new KalturaAPIException(KalturaErrors::SYSTEM_NAME_ALREADY_EXISTS, $this->systemName);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $source_object RemoteServer */
		parent::doFromObject($source_object, $responseProfile);
		
		if($source_object->getHeartbeatTime() < (time() - 90))
			$this->status = EdgeServerStatus::NOT_REGISTERED;
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
	
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = KalturaRemoteServerFactory::getInstanceByType($sourceObject->getType());
		if (!$object)
			return null;
		 
		$object->fromObject($sourceObject, $responseProfile);
		return $object;
	}
}
