<?php
/**
 * Will transform metadata XML based on XSL and will update the metadata object with the new version 
 *
 * @package plugins.metadata
 * @subpackage Scheduler.Transform
 */
class KAsyncTransformMetadata extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::METADATA_TRANSFORM;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->upgrade($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}
	
	private function transform(KalturaBatchJob $job, KalturaTransformMetadataJobData $data)
	{
		$pager = new KalturaFilterPager();
		$pager->maxPageSize = 40;
		if($this->taskConfig->params && $this->taskConfig->params->maxObjectsEachRun)
			$pager->maxPageSize = $this->taskConfig->params->maxObjectsEachRun;
		
		$transformList = $this->kClient->metadataBatch->getTransformMetadataObjects(
			$data->metadataProfileId,
			$data->srcVersion,
			$data->destVersion,
			$pager
			);
			
		if(!$transformList->totalCount) // if no metadata objects returned
		{
			if(!$transformList->lowerVersionCount) // if no metadata objects of lower version exist
			{
				$this->closeJob($job, null, null, 'All metadata transformed', KalturaBatchJobStatus::FINISHED);
				return $job;
			}
			
			$this->closeJob($job, null, null, "Waiting for metadata objects [$transformList->lowerVersionCount] of lower versions", KalturaBatchJobStatus::RETRY);
			return $job;
		}
		
		if($transformList->lowerVersionCount || $transformList->totalCount) // another retry will be needed later
		{
			$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
		}
			
		foreach($transformList->objects as $object)
		{
			$xml = kXsd::transformXmlData($object->xml, $data->destXsdPath, $data->srcXslPath);
			
			if($xml)
			{
				$this->kClient->metadata->update($object->id, $xml);
			}
			else 
			{
				$this->kClient->metadata->invalidate($object->id);				
			}
		}
		
		$this->closeJob($job, null, null, "Metadata objects [" . count($transformList->objects) . "] transformed", KalturaBatchJobStatus::RETRY);
		
		return $job;
	}
	
	private function increaseVersion(KalturaBatchJob $job, KalturaTransformMetadataJobData $data)
	{
		$transformList = $this->kClient->metadataBatch->upgradeMetadataObjects(
			$data->metadataProfileId,
			$data->srcVersion,
			$data->destVersion
			);
			
		if(!$transformList->totalCount) // if no metadata objects returned
		{
			if(!$transformList->lowerVersionCount) // if no metadata objects of lower version exist
			{
				$this->closeJob($job, null, null, 'All metadata upgraded', KalturaBatchJobStatus::FINISHED);
				return $job;
			}
			
			$this->closeJob($job, null, null, "Waiting for metadata objects [$transformList->lowerVersionCount] of lower versions", KalturaBatchJobStatus::RETRY);
			return $job;
		}
		
		if($transformList->lowerVersionCount) // another retry will be needed later
		{
			$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
		}
		
		$this->closeJob($job, null, null, "Metadata objects [$transformList->totalCount] upgraded", KalturaBatchJobStatus::RETRY);
		return $job;
	}
	
	private function upgrade(KalturaBatchJob $job, KalturaTransformMetadataJobData $data)
	{
		KalturaLog::debug("transform($job->id)");
		
		try
		{
			if($data->srcXslPath)
			{
				KalturaLog::debug("source XSL [$data->srcXslPath]");
				return $this->transform($job, $data);
			}
			else 
			{
				KalturaLog::debug("No XSL supplied, just updating the profile version");
				return $this->increaseVersion($job, $data);
			}
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		return $job;
	}
}
