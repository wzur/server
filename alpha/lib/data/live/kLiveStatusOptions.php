<?php

/**
 * Live stream recording entry configuration object 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveStatusOptions
{
	/**
	 * @var string
	 */
	protected $hostname;
	
	/**
	 * @var KalturaMediaServerIndex
	 */
	protected $mediaServerIndex;
	
	/**
	 * @var string
	 */
	protected $applicationName;
	
	/**
	 * @var int
	 */
	protected $status;
	
	/**
	 * @param string $hostName
	 */
	public function setHostName($hostName)
	{
		$this->hostname = $hostName;
	}
	
	/**
	 * @return string
	 */
	public function getHostName()
	{
		return $this->hostname;
	}
	/**
	 * @param KalturaMediaServerIndex $mediaServerIndex
	 */
	public function setMediaServerIndex($mediaServerIndex)
	{
		$this->mediaServerIndex = $mediaServerIndex;
	}
	
	/**
	 * @return KalturaMediaServerIndex
	 */
	public function getMediaServerIndex()
	{
		return $this->mediaServerIndex;
	}
	
	/**
	 * @param string $applicationName
	 */
	public function setApplicationName($applicationName)
	{
		$this->applicationName = $applicationName;
	}
	
	/**
	 * @return string
	 */
	public function getApplicationName()
	{
		return $this->applicationName;
	}
	
	/**
	 * @param int $shouldCopyEntitlement
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}
}