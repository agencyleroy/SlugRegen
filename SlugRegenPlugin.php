<?php namespace Craft;

class SlugRegenPlugin extends BasePlugin
{
  public function getName()
  {
    return 'Slug Regen';
  }

  public function getVersion()
  {
    return '1.0.0';
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
    if($settings['regenerateSlugs']) {
      $locales = array();

      foreach ($settings['locales'] as $locale => $enabled) {
        if ($enabled) {
          $locales = array_merge($locales, array($locale));
        }
      }

      craft()->slugRegen_slugRegeneration->regenerateSlugs(array(
          'locales'     => $locales,
          'generateCsv' => $settings['generateCsv'],
          'skipAscii'   => $settings['skipAscii']
        )
      );
    }
  }
}
