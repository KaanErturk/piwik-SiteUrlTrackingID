<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SiteUrlTrackingID;

use Piwik\Settings\Setting;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;

class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var Setting */
    public $useUrlInTrackingCode;

    protected function init()
    {
        $this->useUrlInTrackingCode = $this->createUseUrlInTrackingCodeSetting();
    }

	/**
	 * Create ModifyTrackingCode Setting checkbox
	 * 
	 * @todo Desirable to this option displayed on Admin -> Websites -> Tracking Code page but I currently have no idea how to go about it. An admin option for now
	 * 
	 * @return SystemSetting
	 */
    private function createUseUrlInTrackingCodeSetting()
    {
        return $this->makeSetting('useUrlInTrackingCode', $default = false, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = 'Use URL for idSite in tracking code';
			$field->introduction = 'Support for tracking by site URL is currently ENABLED. The following option is available to provide customization of the tracking code if desired.';
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = 'If enabled, the first URL of the site (as shown on the Websites > Manage screen) will be used instead of the numeric assigned id. Note: Features which rely on the tracking code, like the page overlay may fail to function.';
        });
    }
}
