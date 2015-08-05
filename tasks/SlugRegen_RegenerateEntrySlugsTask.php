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

    // usually you would use the getSettings() function, but that doesn't work for some reason
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

    // @TODO: this could be optimized by avoiding entries with ascii-only slugs

    switch ($oldSlug) {
      case '__home__': // avoid regenerating the home page slug, this will break it
      // room for more exclusions as well
        return true;
        break;
      default:
        // empty the slug, so that craft regenerates the slug for us
        $entry->slug = '';
        break;
    }

    // the magical call that regenerates slugs
    craft()->entries->saveEntry($entry);

    // sometimes uris are almost empty for some reason, hence trim()
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
