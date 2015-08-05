<?php namespace Craft;

class SlugRegenPlugin extends BasePlugin
{
  public function getName()
  {
    return 'Slug Regen';
  }

  public function getVersion()
  {
    return '0.0.1';
  }

  public function getDeveloper()
  {
    return 'Agency Leroy';
  }

  public function getDeveloperUrl()
  {
    return 'http://agencyleroy.com';
  }

  public function getSettingsHtml()
  {
    return craft()->templates->render('slugregen/settings');
  }

  public function prepSettings($settings)
  {
    if($settings["regenerateSlugs"]) {
      craft()->slugRegen_slugRegeneration->regenerateSlugs();
    }
  }
}
