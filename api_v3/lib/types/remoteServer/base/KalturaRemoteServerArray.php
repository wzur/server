<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRemoteServerArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaRemoteServerArray();
		foreach($arr as $obj)
		{
		    /* @var $obj StorageProfile */
			$nObj = KalturaRemoteServerFactory::getInstanceByType($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaRemoteServer" );
	}
}