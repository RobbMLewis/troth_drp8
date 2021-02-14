<?php

namespace Drupal\troth_officer;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;

/**
 * Defines the troth officer schema handler.
 */
class TrothOfficerEntityStorageSchema extends SqlContentEntityStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {
    $schema = parent::getEntitySchema($entity_type, $reset);

    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_office']['fields']['office_email'])) {
      $schema['troth_office']['fields']['office_email']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_office']['fields']['office_description__value'])) {
      $schema['troth_office']['fields']['office_description__value']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_office']['fields']['office_description__format'])) {
      $schema['troth_office']['fields']['office_description__format']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_office']['fields']['office_term'])) {
      $schema['troth_office']['fields']['office_term']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_office']['fields']['office_open'])) {
      $schema['troth_office']['fields']['office_open']['not null'] = FALSE;
    }
    // Then target your annoying field and set the 'not null' key to FALSE!
    if (!empty($schema['troth_office']['fields']['office_roles'])) {
      $schema['troth_office']['fields']['office_roles']['not null'] = FALSE;
    }
    return $schema;
  }

}
