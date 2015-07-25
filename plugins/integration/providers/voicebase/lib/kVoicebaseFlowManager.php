<?php
class kVoicebaseFlowManager implements kBatchJobStatusEventConsumer, kObjectReplacedEventConsumer 
{
    const BATCH_PARTNER_ID = -1;

    private $baseEndpointUrl = null;

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
        if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED && $dbBatchJob->getJobType() == IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION))
		{
            $data = $dbBatchJob->getData();
            $providerType = $data->getProviderType();
            if ($providerType == VoicebasePlugin::getProviderTypeCoreValue(VoicebaseProvider::VOICEBASE))
                return true;
		}
        return false;
	}

    /* (non-PHPdoc)
     * @see kObjectReplacedEventConsumer::shouldConsumeReplacedEvent()
     */
    public function shouldConsumeReplacedEvent(BaseObject $object)
    {
        if($object instanceof entry)
        {
            $partner = $object->getPartner();
            $params = $partner->getVoicebaseParams();
            if(is_array($params) && count($params))
                return true;
        }
        return false;
    }
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{	
		$data = $dbBatchJob->getData();
        $providerData = $data->getProviderData();
        $entryId = $providerData->getEntryId();
        $partnerId = $dbBatchJob->getPartnerId() !=  self::BATCH_PARTNER_ID ? $dbBatchJob->getPartnerId() : $providerData->partnerId;
        $spokenLanguage = $providerData->getSpokenLanguage();
        $formatsString = $providerData->getCaptionAssetFormats();

        $serviceProviderParams = array('apiKey' => $providerData->getApiKey() , 'apiPassword' => $providerData->getApiPassword());
        $clientHelper = VoicebasePlugin::getClientHelper($serviceProviderParams);
        
        $externalEntryExists = $clientHelper->checkExitingExternalContent($entryId);
        if (!$externalEntryExists)
        {
        	KalturaLog::err('remote content does not exist');
        	return true;     	
        }
        $formatsArray = explode(',',$formatsString);
        $contentsArray = $clientHelper->getRemoteTranscripts($entryId, $formatsArray);
        KalturaLog::debug('contents are - ' . print_r($contentsArray, true));
        $transcripts = self::getObjects($entryId, $spokenLanguage, array(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT)));
        $captions = self::getObjects($entryId, $spokenLanguage, array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)));
        
        foreach ($contentsArray as $format => $content)
        {
            $transcriptFormatConst = constant("KalturaTranscriptType::" . $format);
            if($transcriptFormatConst && isset($transcripts[$transcriptFormatConst]))
            {
                $transcript = $transcripts[$transcriptFormatConst];
                self::setObjectContent($transcript, $content, $format);
            }
            
            $captionFormatConst = constant("KalturaCaptionType::" . $format);
            KalturaLog::debug('XXXXXXXXXXXX - format - ' . $format . ' caption format ' . $captionFormatConst);
            if($captionFormatConst)
            {
                if(isset($captions[$captionFormatConst]))
                    $caption = $captions[$captionFormatConst];
                else
                {
                    KalturaLog::debug('AAAAAAAAAAAAAAAAAA - 1');
                    $caption = new CaptionAsset();
                    KalturaLog::debug('AAAAAAAAAAAAAAAAAA - 2');
                    $caption->setEntryId($entryId);
                    KalturaLog::debug('AAAAAAAAAAAAAAAAAA - 3');
                    $caption->setPartnerId($partnerId);
                    KalturaLog::debug('AAAAAAAAAAAAAAAAAA - 4');
                    $caption->setContainerFormat($captionFormatConst);
                    KalturaLog::debug('AAAAAAAAAAAAAAAAAA - 5');
                    $caption->setStatus(CaptionAsset::ASSET_STATUS_QUEUED);
                    KalturaLog::debug('XXXXXXXXXXXX - caption - ' . print_r($caption, true));
                    $caption->save();
                }
                KalturaLog::debug('AAAAAAAAAAAAAAAAAA- out');
                self::setObjectContent($caption, $content, $format);
            }
        }

		return true;					
	}

    /* (non-PHPdoc)
     * @see kObjectReplacedEventConsumer::objectReplaced()
     */
    public function objectReplaced(BaseObject $object, BaseObject $replacingObject, BatchJob $raisedJob = null)
    {
        $jobData = new kIntegrationJobData();
        $jobData->setTriggerData(new kBpmEventNotificationIntegrationJobTriggerData());

        $triggerType = BpmEventNotificationIntegrationPlugin::getIntegrationTriggerCoreValue(BpmEventNotificationIntegrationTrigger::BPM_EVENT_NOTIFICATION);
        $jobData->setTriggerType($triggerType);
        $providerType = VoicebasePlugin::getIntegrationProviderCoreValue(VoicebaseProvider::VOICEBASE);
        $jobData->setProviderType($providerType);

        $partner = $object->getPartner();
        $voiceBaseParams = $partner->getVoicebaseParams();

        $providerData = new kVoicebaseJobProviderData();
        $providerData->setEntryId($object->getId());
        $providerData->setApiKey($voiceBaseParams['apiKey']);
        $providerData->setApiPassword($voiceBaseParams['apiPassword']);
        $providerData->setPartnerId($partner->getId());

        $jobData->setProviderData($providerData);
        
        $coreObjectType = kPluginableEnumsManager::apiToCore('BatchJobObjectType', BatchJobObjectType::ENTRY);
        $job = kIntegrationFlowManager::addintegrationJob($coreObjectType, null , $jobData);
        kJobsManager::updateBatchJob($job, BatchJob::BATCHJOB_STATUS_PENDING);
        return $jod->getId();
    }

    function getObjects($entryId, $spokenLanguage, array $assetTypes)
    {
        $objects = array();
        $resultArray = assetPeer::retrieveByEntryId($entryId, $assetTypes);

        foreach($resultArray as $object)
        {
            if($object->getLanguage() == $spokenLanguage)
                $objects[$object->getContainerFormat()] = $object;
        }

        return $objects;
    }

    private function setObjectContent($assetObject, $content, $format)
    {
        file_put_contents($fname = tempnam(myContentStorage::getFSUploadsPath(), "VBF"), $content); //prefix for - voicebase file

        $assetObject->incrementVersion();
        $ext = "txt";
        if ($foramt == "DFXP")
            $ext = "xml";
        if ($format == "SRT")
            $ext = "srt";
        if ($format == "JSON")
            $ext = "json";

        $assetObject->setFileExt($ext);
        $assetObject->setSize(kFile::fileSize($fname));
        $assetObject->save();

        $syncKey = $assetObject->getSyncKey(AttachmentAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);

        try 
        {
            kFileSyncUtils::moveFromFile($fname, $syncKey, true, false);
        }
        catch (Exception $e)
        {     
            if($attachmentAsset->getStatus() == AttachmentAsset::ASSET_STATUS_QUEUED || $attachmentAsset->getStatus() == AttachmentAsset::ASSET_STATUS_NOT_APPLICABLE)
            {
                $assetObject->setDescription($e->getMessage());
                $assetObject->setStatus(AttachmentAsset::ASSET_STATUS_ERROR);
                $assetObject->save();
            }                                               
            throw $e;
        }

        $finalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
        list($width, $height, $type, $attr) = getimagesize($finalPath);

        $assetObject->setWidth($width);
        $assetObject->setHeight($height);
        $assetObject->setSize(kFile::fileSize($finalPath));

        $assetObject->setStatus(AttachmentAsset::ASSET_STATUS_READY);
        $assetObject->save();
    } 

}
