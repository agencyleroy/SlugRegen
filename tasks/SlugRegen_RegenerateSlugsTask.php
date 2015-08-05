<?php
namespace Craft;

class SlugRegen_RegenerateSlugsTask extends BaseTask
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

    ini_set('memory_limit', '2048M');

    $criteria = craft()->elements->getCriteria(ElementType::Entry);
    $criteria->limit = 10000;
    $this->entries = $criteria->find();
    $this->_totalSteps = count($this->entries);
    return $this->_totalSteps;
  }

  public function runStep($step) {
    return $this->runSubTask('SlugRegen_RegenerateEntrySlugs', $this->entries[$step]->title, $this->entries[$step]);
  }
}
