<?php
namespace Craft;

class SlugRegen_RegenerateEntrySlugsTask extends BaseTask
{
  private $settings;

  private $entries;

  private $_totalSteps = null;

  public function getTotalSteps()
  {
    if (is_int($this->_totalSteps)) {
      return $this->_totalSteps;
    }

    $this->settings = $this->model->getAttribute('settings');

    foreach ($this->settings['locales'] as $locale) {
      $this->entries[] = craft()->entries->getEntryById($this->settings['entryId'], $locale);
    }

    $this->_totalSteps = count($this->settings['locales']);
    return $this->_totalSteps;
  }

  public function runStep($step)
  {
    $entry = $this->entries[$step];
    $oldSlug = $entry->slug;
    $oldUri = $entry->uri;

    switch ($oldSlug) {
      case '__home__':
        return true;
        break;
      default:
        $entry->slug = '';
        break;
    }

    craft()->entries->saveEntry($entry);

    if ($this->settings['generateCsv'] && trim($oldUri) != trim($entry->uri)) {
      $comparison = '"' . $oldUri . '";"' . $entry->uri . '"' . "\n";
      file_put_contents($this->settings['fileName'], $comparison, FILE_APPEND);
    }

    unset($entry);

    if ($this->_totalSteps - 1 == $step) {
      unset($this->entries);
    }

    return true;
  }
}