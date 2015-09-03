<?php


/**
 * Skeleton subclass for representing a row from the 'remote_server' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class RemoteServer extends BaseRemoteServer {

	public function getPlaybackHostName()
	{
		$playbackHostName = $this->playback_host_name;
	
		if(!$playbackHostName)
			$playbackHostName = $this->host_name;
	
		return $playbackHostName;
	}

} // RemoteServer
