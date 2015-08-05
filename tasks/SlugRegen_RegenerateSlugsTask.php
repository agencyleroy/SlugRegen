<?php
namespace Craft;

class SlugRegen_RegenerateSlugsTask extends BaseTask
{
  private $entries;

  private $locales;

  private $_totalSteps = null;

  public function getDescription()
  {
    return Craft::t('Regenerating entry slugs');
  }

  public function init()
  {
    parent::init();
    ini_set('memory_limit', '2048M');

    $criteria = craft()->elements->getCriteria(ElementType::Entry);
    $criteria->limit = 1000;
    $this->entries = $criteria->find();
    $this->locales = craft()->i18n->getSiteLocaleIds();
  }

  public function getTotalSteps()
  {
    if (is_int($this->_totalSteps)) {
      return $this->_totalSteps;
    }

    $this->_totalSteps = count($this->entries);
    return $this->_totalSteps;
  }

  public function runStep($step)
  {
    return $this->runSubTask('SlugRegen_RegenerateEntrySlugs', $this->entries[$step]->title, array(
        'entryId' => $this->entries[$step]->id,
        'locales' => $this->locales
      )
    );
  }
}
