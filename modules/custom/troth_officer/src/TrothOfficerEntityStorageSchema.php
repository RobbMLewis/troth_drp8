<?php

namespace Drupal\troth_officer;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;

/**
 * Defines the troth maps schema handler.
 */
class TrothOfficerEntityStorageSchema extends SqlContentEntityStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {
    $schema = parent::getEntitySchema($entity_type, $reset);

    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_office_office_email']['fields']['state'])) {
      $schema['troth_office_office_email']['fields']['state']['not null'] = FALSE;
    }
    return $schema;
  }

}
