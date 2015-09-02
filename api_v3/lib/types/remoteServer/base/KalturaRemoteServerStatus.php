<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaRemoteServerStatus extends KalturaEnum implements RemoteServerStatus
{
	public static function getEnumClass()
	{
		return 'RemoteServerStatus';
	}
}
