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

    $entry = $this->getSettings();
    $this->entries[] = craft()->entries->getEntryById($entry->id, 'sv_ax');
    $this->entries[] = craft()->entries->getEntryById($entry->id, 'sv_fi');
    $this->entries[] = craft()->entries->getEntryById($entry->id, 'sv_se');
    $this->entries[] = craft()->entries->getEntryById($entry->id, 'fi_fi');
    $this->entries[] = craft()->entries->getEntryById($entry->id, 'en_gb');
    $this->_totalSteps = 5; // five languages @TODO make this dynamic
    return $this->_totalSteps;
  }

  public function runStep($step) {
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

    if ($this->_totalSteps - 1 == $step) {
      unset($this->entries);
    }

    return true;
  }
}