<?php
namespace Craft;

class SlugRegen_RegenerateSlugsTask extends BaseTask
{
  private $settings;

  private $entries;

  private $_totalSteps = null;

  public function getDescription()
  {
    return Craft::t('Regenerating entry slugs');
  }

  public function init()
  {
    parent::init();
    ini_set('memory_limit', '768M');

    $criteria = craft()->elements->getCriteria(ElementType::Entry);
    $criteria->limit = 1000;
    $this->entries = $criteria->find();
  }

  public function getTotalSteps()
  {
    if (is_int($this->_totalSteps)) {
      return $this->_totalSteps;
    }

    $this->settings = $this->model->getAttribute('settings');

    if ($this->settings['generateCsv']) {
      $this->settings['fileName'] = craft()->config->get('environmentVariables')['basePath'] . 'slugregen_' . date('YmdHis') . '.csv';
      file_put_contents($this->settings['fileName'], '"old uri";"new uri"' . "\n");
    }

    $this->_totalSteps = count($this->entries);
    return $this->_totalSteps;
  }

  public function runStep($step)
  {
    $result = $this->runSubTask('SlugRegen_RegenerateEntrySlugs', $this->entries[$step]->title, array(
        'entryId'     => $this->entries[$step]->id,
        'locales'     => $this->settings['locales'],
        'fileName'    => $this->settings['fileName'],
        'generateCsv' => $this->settings['generateCsv']
      )
    );

    unset($this->entries[$step]);

    if ($this->_totalSteps - 1 == $step) {
      unset($this->entries);
    }

    return $result;
  }
}
