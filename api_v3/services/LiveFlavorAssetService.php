<?php

/**
 * Retrieve information and invoke actions on Flavor Asset
 *
 * @service flavorAsset
 * @package api
 * @subpackage services
 */
class LiveFlavorAssetService extends FlavorAssetService
{   
    /**
     * Update Live flavor asset status
     *
     * @action update
     * @param string $id
     * @param KalturaFlavorAsset $flavorAsset
     * @return KalturaFlavorAsset
     * @throws KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND
     * @validateUser asset::entry id edit
     */
    function updateLiveStatusAction($id, KalturaLiveAssetStatusOptions $liveAssetStatusOptions)
    {
   		$dbFlavorAsset = assetPeer::retrieveById($id);
   		if (!$dbFlavorAsset || !($dbFlavorAsset instanceof flavorAsset))
   			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $id);
    	
		$dbEntry = $dbFlavorAsset->getentry();
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $dbFlavorAsset->getEntryId());
			
		
		
    	$dbFlavorAsset = $flavorAsset->toUpdatableObject($dbFlavorAsset);
   		$dbFlavorAsset->save();
		
		$flavorAsset = KalturaFlavorAsset::getInstance($dbFlavorAsset, $this->getResponseProfile());
		return $flavorAsset;
    }
}