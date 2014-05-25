<?php
/**
 * Handles thumb cue point ingestion from XML bulk upload
 * @package plugins.thumbCuePoint
 * @subpackage batch
 */
class ThumbCuePointBulkUploadXmlHandler extends CuePointBulkUploadXmlHandler
{
	/**
	 * @var ThumbCuePointBulkUploadXmlHandler
	 */
	protected static $instance;
	
	/**
	 * @return ThumbCuePointBulkUploadXmlHandler
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new ThumbCuePointBulkUploadXmlHandler();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see CuePointBulkUploadXmlHandler::getNewInstance()
	 */
	protected function getNewInstance()
	{
		return new KalturaThumbCuePoint();
	}
	
	/* (non-PHPdoc)
	 * @see CuePointBulkUploadXmlHandler::parseCuePoint()
	 */
	protected function parseCuePoint(SimpleXMLElement $scene)
	{
		if($scene->getName() != 'scene-thumb-cue-point')
			return null;
			
		$cuePoint = parent::parseCuePoint($scene);
		if(!($cuePoint instanceof KalturaThumbCuePoint))
			return null;
			
		//If timedThumbAssetId is present in the XML assume an existing one is beeing updated (Action = Update)
		if(isset($scene->timedThumbAssetId))
			$cuePoint->timedThumbAssetId  = $scene->timedThumbAssetId;
			
		return $cuePoint;
	}
	
	protected function addCuePoint(SimpleXMLElement $scene)
	{
		if(!isset($scene->slide))
			return parent::addCuePoint($scene);
		
		$timedThumbAsset = $this->getTimedThumbAsset($scene->slide);
		$timedThumbResource = $this->xmlBulkUploadEngine->getResource($scene->slide, null);
		
		$thumb = KBatchBase::$kClient->thumbAsset->add($this->entryId, $timedThumbAsset);
		KBatchBase::$kClient->thumbAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $timedThumbResource);
		
		return parent::addCuePoint($scene);
	}
	
	protected function updateCuePoint(SimpleXMLElement $scene)
	{
		if(!isset($scene->slide) || !isset($scene->slide->timedThumbAssetId))
			return parent::updateCuePoint($scene);
		
		$timedThumbResource = $this->xmlBulkUploadEngine->getResource($scene->slide, null);
		
		KBatchBase::$kClient->thumbAsset->setContent($scene->slide->timedThumbAssetId, $timedThumbResource);
		
		return parent::updateCuePoint($scene);
	}
	
	protected function getTimedThumbAsset(SimpleXMLElement $slide)
	{
		$timedThumbAsset = new KalturaTimedThumbAsset();
		
		$timedThumbAsset->offset = kXml::integerToTime($slide->offset);
	}
	
}
