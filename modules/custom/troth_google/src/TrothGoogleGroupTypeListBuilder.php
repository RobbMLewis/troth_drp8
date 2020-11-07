<?php

namespace Drupal\troth_google;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Class SimpleTypeListBuilder.
 */
class TrothGoogleGroupTypeListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Group Name');
    $header['id'] = $this->t('Machine Name');
    $header['group_id'] = $this->t('Group ID');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['group_id'] = $entity->getGroupId();

    return $row + parent::buildRow($entity);
  }

}
