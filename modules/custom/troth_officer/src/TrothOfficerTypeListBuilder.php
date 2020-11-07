<?php

namespace Drupal\troth_officer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Class SimpleTypeListBuilder.
 */
class TrothOfficerTypeListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Office Name');
    $header['office_type'] = $this->t('Office Group');
    $header['id'] = $this->t('Machine Name');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $groups = troth_officer_office_groups();
    $row['label'] = $entity->getName();
    $row['office_type'] = $groups[$entity->getOfficeType()];
    $row['id'] = $entity->id();

    return $row + parent::buildRow($entity);
  }

}
