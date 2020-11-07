<?php

namespace Drupal\troth_google;

use Drupal\troth_google\Entity\TrothGoogleGroupType;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class PracticalPermissionsGenerator.
 */
class TrothGooglePermissionsGenerator {
  use StringTranslationTrait;

  /**
   * Loop through all TrothGoogleTypeEntity and build an array of permissions.
   *
   * @return array
   *   Array of permissions
   */
  public function trothGoogleGroupTypePermissions() {
    $perms = [];
    foreach (TrothGoogleGroupType::loadMultiple() as $entity_type) {
      $perms += $this->buildPermissions($entity_type);
    }
    return $perms;
  }

  /**
   * Create the permissions desired for an individual entity type.
   *
   * @param \Drupal\troth_google\Entity\TrothGoogleGroupType $entity_type
   *   The entity type definition.
   *
   * @return array
   *   Array of permissions
   */
  protected function buildPermissions(TrothGoogleGroupType $entity_type) {
    $type_id = $entity_type->id();
    $bundle_of = $entity_type->getEntityType()->getBundleOf();
    $type_params = [
      '%type_name' => $entity_type->label(),
      '%bundle_of' => $type_id,
    ];
    return [
      "create $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Create new %bundle_of', $type_params),
      ],
      "view any $bundle_of $type_id" => [
        'title' => $this->t('%type_name: View any %bundle_of', $type_params),
      ],
      "view own $bundle_of $type_id" => [
        'title' => $this->t('%type_name: View own %bundle_of', $type_params),
      ],
      "edit any $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Edit any %bundle_of', $type_params),
      ],
      "edit own $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Edit own %bundle_of', $type_params),
      ],
      "delete any $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Delete any %bundle_of', $type_params),
      ],
      "delete own $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Delete own %bundle_of', $type_params),
      ],
    ];
  }

}
