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

    // there is probably a much faster way to do this, using a manual query. all we really need is ids and titles.
    $criteria = craft()->elements->getCriteria(ElementType::Entry);
    $criteria->limit = 1000;
    $this->entries = $criteria->find();
  }

  public function getTotalSteps()
  {
    if (is_int($this->_totalSteps)) {
      return $this->_totalSteps;
    }

    // the correct place to do this would be init(), but settings aren't available yet then.
    $this->settings = $this->model->getAttribute('settings');

    if ($this->settings['generateCsv']) {
      // the file gets put into the basePath, which is the root directory of your Craft project
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

    // the amount of memory that these calls to unset() free is... minimal
    unset($this->entries[$step]);

    // last step
    if ($this->_totalSteps - 1 == $step) {
      unset($this->entries);
    }

    return $result;
  }
}
