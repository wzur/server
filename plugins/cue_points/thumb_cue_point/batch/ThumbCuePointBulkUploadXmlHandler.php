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
	 * @var array
	 */
	protected $thumbResources;
	
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
		$thumbCuePoint = parent::addCuePoint($scene);
		/*@var $thumbCuePoint KalturaThumbCuePoint */
		
		$timedThumbResource = $this->xmlBulkUploadEngine->getResource($scene->slide, null);
		
		$this->thumbResources[$thumbCuePoint->startTime] = $timedThumbResource; 
		
		return $thumbCuePoint;
	}
	
	protected function updateCuePoint(SimpleXMLElement $scene)
	{
		if(!isset($scene->slide) || !isset($scene->slide->timedThumbAssetId))
			return parent::updateCuePoint($scene);
		
		$timedThumbResource = $this->xmlBulkUploadEngine->getResource($scene->slide, null);
		
		KBatchBase::$kClient->thumbAsset->setContent($scene->slide->timedThumbAssetId, $timedThumbResource);
		
		return parent::updateCuePoint($scene);
	}
	
	protected function handleResults(array $results, array $items)
	{
		/*
		KBatchBase::$kClient->startMultiRequest();
		
		foreach($results as $index => $cuePoint)
		{	
			if(isset($cuePoint->startTime) && isset($this->thumbResources[$cuePoint->startTime]) && $cuePoint instanceof KalturaThumbCuePoint)
				 KBatchBase::$kClient->thumbAsset->setContent($cuePoint->timedThumbAssetId, $this->thumbResources[$cuePoint->startTime]);
		}
		
		KBatchBase::$kClient->doMultiRequest();
		
		return parent::handleResults($results, $items);
		*/
		
		KBatchBase::$kClient->startMultiRequest();
		
		foreach($results as $index => $cuePoint)
		{	
			if($cuePoint instanceof KalturaThumbCuePoint)
			{
				$timedThumbResource = $this->xmlBulkUploadEngine->getResource($items[$index]->slide, null);
				KBatchBase::$kClient->thumbAsset->setContent($cuePoint->timedThumbAssetId, $timedThumbResource);
			}
				
		}
		
		KBatchBase::$kClient->doMultiRequest();
		
		return parent::handleResults($results, $items);
	}

}
