<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaRemoteServerType extends KalturaDynamicEnum implements remoteServerType
{
	public static function getEnumClass()
	{
		return 'remoteServerType';
	}
}