<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlChainAccessControlProfilesAction extends KalturaRuleAction
{
	/**
	 * Comma separated list of access control profile ids 
	 * 
	 * @var string
	 */
	public $accessControlProfileIds;
	
	private static $mapBetweenObjects = array
	(
		'accessControlProfileIds',
	);

	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = RuleActionType::CHAIN_ACCESS_CONTROL_PROFILES;
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kAccessControlChainAccessControlAction();
			
		return parent::toObject($dbObject, $skip);
	}
}