<?php

namespace Drupal\troth_migrate\Commands;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class DrushBatchProcessingCommands extends DrushCommands {

  /**
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;

  /**
   * Constructs a new DrushBatchProcessingCommands object.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   */
  public function __construct(LoggerChannelFactoryInterface $loggerChannelFactory) {
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  /**
   * Migrate.
   *
   * @command troth-migrate:migrate
   * @aliases troth-migrate-migrate
   */
  public function migrate() {
    // 1. Log the start of the script.
    $this->loggerChannelFactory->get('troth_migrate')->info('Update maps batch operations start');

    $batch = [
      'title' => t('Migrating Data'),
      'init_message' => t('The old website and users are being migrated.'),
      'operations' => [],
      'file' => drupal_get_path('module', 'troth_migrate') . '/troth_migrate.batch.inc',
      'finished' => 'batch_finish_callback',
    ];
    $batch['operations'][] = ['migrate_roles', []];
    $batch['operations'][] = ['migrate_users', []];
    //$batch['operations'][] = ['migrate_nodes', []];
    //$batch['operations'][] = ['menu_hierarchy', []];
    $batch['operations'][] = ['migrate_order', []];
    // 5. Add batch operations as new batch sets.
    batch_set($batch);
    // 6. Process the batch sets.
    drush_backend_batch_process();
    // 6. Show some information.
    $this->logger()->notice("Batch operations end.");
    // 7. Log some information.
    $this->loggerChannelFactory->get('troth_migrate')->info('Update batch operations end.');
  }

}
