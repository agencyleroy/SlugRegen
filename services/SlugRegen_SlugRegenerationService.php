<?php
namespace Craft;

class SlugRegen_SlugRegenerationService extends BaseApplicationComponent
{
  public function regenerateSlugs($locales)
  {
    $task = craft()->tasks->getNextPendingTask('SlugRegen_RegenerateSlugs');
    if ($task) {
      craft()->tasks->saveTask($task, false);
    } else {
      craft()->tasks->createTask('SlugRegen_RegenerateSlugs', null, array('locales' => $locales));
    }
  }
}
