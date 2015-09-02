<?php
/**
 * @package api
 * @subpackage objects.factory
 */
class KalturaRemoteServerFactory
{
	/**
	 * @param int $type
	 * @return KalturaRemoteServer
	 */
	static function getInstanceByType ($type)
	{
		switch ($type) 
		{
			case KalturaRemoteServerType::NODE:
				$obj = new KalturaEdgeServer();
				break;
				
			case KalturaRemoteServerType::MEDIA_SERVER:
				$obj = new KalturaMediaServer();
				break;
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaRemoteServer', $type);
				
				if(!$obj)
					$obj = new KalturaRemoteServer();
					
				break;
		}
		
		return $obj;
	}
}