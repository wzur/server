<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlChainAccessControlAction extends kRuleAction 
{
	/**
	 * @var array
	 */
	protected $accessControlProfileIds = array();
	
	public function __construct() 
	{
		parent::__construct(RuleActionType::CHAIN_ACCESS_CONTROL_PROFILES);
	}
	
	/**
	 * @return string
	 */
	public function getAccessControlProfileIds() 
	{
		return implode(',', $this->accessControlProfileIds);
	}

	/**
	 * @param string $accessControlProfileIds
	 */
	public function setAccessControlProfileIds($accessControlProfileIds) 
	{
		$this->accessControlProfileIds = explode(',', $accessControlProfileIds);
	}
	
	public function applyDeliveryProfileDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes)
	{
		$deliveryAttributes->setDeliveryProfileIds($this->deliveryProfileIds, $this->isBlockedList);
		return true;
	}
	
}
