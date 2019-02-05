<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SiteUrlTrackingID;

use Piwik\Common;
use Piwik\Plugins\SitesManager\Model as SiteModel;
use Piwik\Plugins\SitesManager\SiteUrls;
use Piwik\Tracker\Cache as TrackerCache;

class SiteUrlTrackingID extends \Piwik\Plugin
{
	const MAPPING_CACHE_KEY = 'SiteUrlTrackingID.Mapping';

    /**
     * Register event observers
     *
     * @return array
     */
    public function registerEvents()
    {
        return [
            'Piwik.getJavascriptCode' => 'getSiteURLjs',
            'SitesManager.getImageTrackingCode' => 'getSiteURLimg',
            'Tracker.Request.getIdSite' => 'getSiteID',
			'Tracker.setTrackerCacheGeneral'    => 'setTrackerCacheGeneral',
        ];
    }
	
	/**
	 * Run upon activation of plugin. Clears Tracker Cache so that this plugins setTrackerCacheGeneral method will be called and added to cache
	 * 
	 * @return void
	 */
	public function activate()
	{
		TrackerCache::clearCacheGeneral();
	}
	
	/**
	 * Run upon deactivation of plugin. Clears this plugins data from the Tracker Cache
	 * 
	 * @return void
	 */
	public function deactivate()
	{
		TrackerCache::clearCacheGeneral();
	}
		
	/**
	 * Cache all possible website urls (including aliases) for later lookup
	 * 
	 * @param array &$cacheContent
	 * @return void
	 */
	public function setTrackerCacheGeneral(&$cacheContent)
	{
		$siteUrls = new SiteUrls();
		 
		$urls = $siteUrls->getAllCachedSiteUrls();
		
		$map = [];
		
		foreach($urls as $id=>$aliases)
		{
			foreach($aliases as $alias)
			{
				$siteKey = preg_replace('/^.+?\:\/\//', '', strtolower($alias));
				$siteKey = sha1($siteKey);
				$map[$siteKey] = $id;
			}
		}
		
		$cacheContent[self::MAPPING_CACHE_KEY] = $map;
	}

    /**
     * Get main site URL from the site ID
     *
     * @param int $idSite
     * @return string|int
     */
    public function getSiteURL($idSite)
    {
		$siteModel = new SiteModel();
		
		$site = $siteModel->getSiteFromId($idSite);
		        
        $siteURL = $site["main_url"];
        
        return (!empty($siteURL) ? preg_replace('/^.+?\:\/\//i', '', $siteURL) : $idSite);
    }
	
    /**
     * Get site ID from the site URL
     *
     * @param int &$idSite
     * @param array $params
     * @return void
     */
	public function getSiteID(&$idSite, $params)
    {
        if (!(is_int($idSite) && $idSite > 0))
        {
			$siteURLfull = preg_replace(['/^.+?\:\/\//','/\/\s*$/'], '', strtolower($params['idsite']));
			
			$siteKey = sha1($siteURLfull);
			
			$trackerCache = TrackerCache::getCacheGeneral();
			
			$idMapping = $trackerCache[self::MAPPING_CACHE_KEY];
			
           if(!empty($idMapping[$siteKey]))
		   {
				$idSite = $idMapping[$siteKey];
           }
        }
    }

    /**
     * Get site URL for the JavaScript Tracking Code
     *
     * @param array &$codeImpl
     * @param array $parameters
     * @return void
     */
    public function getSiteURLjs(&$codeImpl, $parameters)
    {
		$settings = new SystemSettings();
		
		if($settings->useUrlInTrackingCode->getValue())
		{
			$codeImpl['idSite'] = $this->getSiteURL($codeImpl['idSite']);
		}
    }

    /**
     * Get site URL for the Image Tracking Link
     *
     * @param array &$piwikUrl
     * @param array &$urlParams
     * @return void
     */
    public function getSiteURLimg(&$piwikUrl, &$urlParams)
    {
		$settings = new SystemSettings();
		
		if($settings->useUrlInTrackingCode->getValue())
		{
			$urlParams['idsite'] = $this->getSiteURL($urlParams['idsite']);
		}
    }
    
}