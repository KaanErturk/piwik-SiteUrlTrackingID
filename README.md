# Piwik SiteUrlTrackingID Plugin

## Description

If you have many websites that serve the very same web page with a single tracking code, and you just want to use the site URL automatically, then this plugin is for you.

Once the plugin is activated Piwik will start to accept site URL via `idsite` parameter as part of the tracking requests. You may still use the numeric site ID since that functionality will continue to work. New tracking code generated on Piwik will include the main site URL instead of the numeric site ID. You may as well use one of the other URLs of a website as the tracking ID.

This plugin is for a very simple use case. I'm not planning to add any more functionality. However please do feel free to contact me if there are any issues.

**Note: There may be issues if multiple tracking ID plugins (ones that modify the site ID in the tracking code) are enabled. Only one of those plugins should be activated.**

## Installation

Install it via Piwik Marketplace

## License

GPL v3 or later
