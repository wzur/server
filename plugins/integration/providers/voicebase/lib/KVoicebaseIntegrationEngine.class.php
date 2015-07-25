<?php
/**
 * @package plugins.voicebase
 * @subpackage Scheduler
 */
class KVoicebaseIntegrationEngine extends KExampleIntegrationEngine
{
    const BATCH_PARTNER_ID = -1;

    private $baseEndpointUrl = null;
    private $clientHelper = null;

	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::dispatch()
	 */
	public function dispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		return $this->doDispatch($job, $data, $data->providerData);
	}

	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::close()
	 */
	public function close(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		return $this->doClose($job, $data, $data->providerData);
	}
	
	protected function doDispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVoicebaseJobProviderData $providerData)
	{
        $partnerId = $job->partnerId !=  self::BATCH_PARTNER_ID ? $job->partnerId : $providerData->partnerId;

        KBatchBase::impersonate($partnerId);
        $entryId = $providerData->entryId;
        $flavorAssetId = $providerData->flavorAssetId;
        $transcriptId = $providerData->transcriptId;
        $spokenLanguage = $providerData->spokenLanguage;
        $foramtsString = $providerData->captionAssetFormats;
        $formatsArray = explode(',', $foramtsString);

        $callBackUrl = $data->callbackNotificationBaseUrl;
        $callBackUrl .= "/id/" . $job->id;

        $serviceProviderParams = array('apiKey' => $providerData->apiKey , 'apiPassword' => $providerData->apiPassword);
        $this->clientHelper = VoicebasePlugin::getClientHelper($serviceProviderParams);
        
        self::validateTranscripts($entryId, $formatsArray, $spokenLanguage);
        
        $transcriptContent = null;
        if($transcriptId)
        {
        	$attachmentAssetPlugin = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient);
        	try
        	{
        		$url = $attachmentAssetPlugin->attachmentAsset->getUrl($transcriptId);
        	}
        	catch(Exception $e)
        	{
        		KalturaLog::debug("unable to serve transcript - error - " . $e->getMessage());
        		throw $e;
        	}
        	$transcriptContent = $this->clientHelper->getTranscriptContent($url);
        }

        $flavorAssetId = self::validateFlavorAssetId($entryId, $flavorAssetId);

        try
        {
        	$result = KBatchBase::$kClient->flavorAsset->getUrl($flavorAssetId);
        }
        catch(KalturaAPIException $e)
        {
        	KalturaLog::err('unable to get flavor url with API error - ' . $e->getMessage());
        	throw $e;
        }   
        $flavorUrl = urlencode($result);
        
        $externalEntryExists = $this->clientHelper->checkExitingExternalContent($entryId);
        if (!$externalEntryExists)
        {
            $this->clientHelper->uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage);
        }
        elseif($transcriptContent)
        {
            $this->clientHelper->updateRemoteTranscript($entryId, $transcriptContent);
        }
        else
        {
            $this->clientHelper->deleteRemoteFile($entryId);
            $this->clientHelper->uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage);
        }
        KBatchBase::unimpersonate();
		return false;
	}
	
	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVoicebaseJobProviderData $providerData)
	{
		$serviceProviderParams = array('apiKey' => $providerData->apiKey , 'apiPassword' => $providerData->apiPassword);
		$this->clientHelper = VoicebasePlugin::getClientHelper($serviceProviderParams);
		
        $entryId = $providerData->entryId;
		KalturaLog::debug("closing - entry is [$entryId]");

        $externalEntryExists = $this->clientHelper->checkExitingExternalContent($entryId);
        if($externalEntryExists)
            return true;

		return false;
	}

    private function validateTranscripts($entryId, array $formats, $spokenLanguage)
    {   
        $filter = new KalturaAssetFilter();
        $filter->entryIdEqual = $entryId;
        $pager = null;
        $attachmentPlugin = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient);
        try
        {
            $result = $attachmentPlugin->attachmentAsset->listAction($filter, $pager);
        }
        catch(Exception $e)
        {
            KalturaLog::debug('unable to list transcripts - error - ' . $e->getMessage());
            return;
        }

        $adjustedFormats = array();
        foreach ($formats as $format)
        {
            $adjustedFormat = constant ("KalturaTranscriptType::" . $format);  
            if ($adjustedFormat)
            $adjustedFormats[] = $adjustedFormat; 
        }

        foreach($result->objects as $attachment)
        {
            if ($attachment instanceof KalturaTranscriptAsset)
            {
                $index = array_search($attachment->format, $adjustedFormats);
                 if($index !== false)
                 {
                    if($attachment->language == $spokenLanguage)
                    unset($adjustedFormats[$index]);
                 }
            }
        }

        foreach($adjustedFormats as $format)
        {
            $transcript = new KalturaTranscriptAsset();
            $transcript->language = $spokenLanguage;
            $transcript->format = $format;
            try
            {
                $result = $attachmentPlugin->attachmentAsset->add($entryId, $transcript);
            }
            catch(Exception $e)
            {
                KalturaLog::debug('unable to add caption asset - error - ' . $e->getMessage());    
            }
        }
    }

    private function validateFlavorAssetId($entryId, $flavorAssetId = null)
    {
        $sourceAssetId = null;

        $filter = new KalturaAssetFilter();
        $filter->entryIdEqual = $entryId;
        $pager = null;
        try
        {
            $result = KBatchBase::$kClient->flavorAsset->listAction($filter, $pager);
        }
        catch(Exception $e)
        {
            KalturaLog::debug("unable to list assets - error - " . $e->getMessage());
            throw $e;
        }

        foreach($result->objects as $entryAsset)
        {
            if($flavorAssetId && $entryAsset->id == $flavorAssetId)
                return $flavorAssetId;

            if($entryAsset->isOriginal == 1)
                $sourceAssetId = $entryAsset->id;
        }

        return $sourceAssetId;
    }
}
