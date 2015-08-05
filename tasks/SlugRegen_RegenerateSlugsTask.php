<?php
namespace Craft;

class SlugRegen_RegenerateSlugsTask extends BaseTask
{
  private $entries;

  private $locales;

  private $_totalSteps = null;

  private $fileName;

  private $generateCsv;

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
    $this->fileName = craft()->config->get('environmentVariables')['basePath'] . 'slugregen_' . date('YmdHis') . '.csv';
  }

  public function getTotalSteps()
  {
    if (is_int($this->_totalSteps)) {
      return $this->_totalSteps;
    }

    $settings = $this->model->getAttribute('settings');
    $this->locales = $settings['locales'];

    if ($settings['generateCsv']) {
      $this->generateCsv = true;
      file_put_contents($this->fileName, '"old uri";"new uri"' . "\n");
    }

    $this->_totalSteps = count($this->entries);
    return $this->_totalSteps;
  }

  public function runStep($step)
  {
    $result = $this->runSubTask('SlugRegen_RegenerateEntrySlugs', $this->entries[$step]->title, array(
        'entryId'     => $this->entries[$step]->id,
        'locales'     => $this->locales,
        'fileName'    => $this->fileName,
        'generateCsv' => $this->generateCsv
      )
    );

    unset($this->entries[$step]);

    if ($this->_totalSteps - 1 == $step) {
      unset($this->entries);
    }

    return $result;
  }
}
