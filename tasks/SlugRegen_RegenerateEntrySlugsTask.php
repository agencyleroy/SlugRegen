<?php
namespace Craft;

class SlugRegen_RegenerateEntrySlugsTask extends BaseTask
{
  private $entries;

  private $_totalSteps = null;

  public function getDescription()
  {
    return Craft::t('Regenerating entry slugs');
  }

  public function getTotalSteps()
  {
    if (is_int($this->_totalSteps)) {
      return $this->_totalSteps;
    }

    $settings = $this->model->getAttribute('settings');

    foreach ($settings['locales'] as $locale) {
      $this->entries[] = craft()->entries->getEntryById($settings['entryId'], $locale);
    }

    $this->_totalSteps = count($settings['locales']);
    return $this->_totalSteps;
  }

  public function runStep($step)
  {
    $entry = $this->entries[$step];
    $old = $entry->slug;

    switch ($old) {
      case '__home__':
        return true;
        break;
      default:
        $entry->slug = '';
        break;
    }

    craft()->entries->saveEntry($entry);

    unset($entry);

    if ($this->_totalSteps - 1 == $step) {
      unset($this->entries);
    }

    return true;
  }
}