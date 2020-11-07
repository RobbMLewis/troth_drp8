<?php

namespace Drupal\troth_google\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * TrothGoogleGroup Type.
 *
 * @ConfigEntityType(
 *   id = "troth_google_type",
 *   label = @Translation("TrothGoogleGroup Type"),
 *   bundle_of = "troth_google",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_prefix = "type",
 *   config_export = {
 *     "id",
 *     "label",
 *     "group_id",
 *     "group_description",
 *     "group_required",
 *     "group_registration",
 *     "group_expired",
 *     "group_limit_role",
 *     "group_roles",
 *     "group_message",
 *   },
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\troth_google\Form\TrothGoogleGroupTypeEntityForm",
 *       "add" = "Drupal\troth_google\Form\TrothGoogleGroupTypeEntityForm",
 *       "edit" = "Drupal\troth_google\Form\TrothGoogleGroupTypeEntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\troth_google\TrothGoogleGroupTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer site configuration",
 *   links = {
 *     "canonical" = "/admin/config/troth/google/group/{troth_google_type}",
 *     "add-form" = "/admin/config/troth/google/group/add",
 *     "edit-form" = "/admin/config/troth/google/group/{troth_google_type}/edit",
 *     "delete-form" = "/admin/config/troth/google/group/{troth_google_type}/delete",
 *     "collection" = "/admin/config/troth/google/group",
 *   }
 * )
 */
class TrothGoogleGroupType extends ConfigEntityBundleBase {
  /**
   * The machine name of the practical type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the practical type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $group_id;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */

  protected $group_description;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $group_required;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $group_registration;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $group_expired;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $group_limit_role;

  /**
   * A brief description of the practical type.
   *
   * @var array
   */
  protected $group_roles;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $group_message;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($label) {
    $this->label = $label;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getGroupId() {
    return $this->group_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setGroupId($group_id) {
    $this->group_id = $group_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->group_description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($group_description) {
    $this->group_description = $group_description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequired() {
    return $this->group_required;
  }

  /**
   * {@inheritdoc}
   */
  public function setRequired($group_required) {
    $this->group_required = $group_required;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegistration() {
    return $this->group_registration;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegistration($group_registration) {
    $this->group_registration = $group_registration;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getExpired() {
    return $this->group_expired;
  }

  /**
   * {@inheritdoc}
   */
  public function setExpired($group_expired) {
    $this->group_expired = $group_expired;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLimitRole() {
    return $this->group_limit_role;
  }

  /**
   * {@inheritdoc}
   */
  public function setLimitRole($group_limit_role) {
    $this->group_limit_role = $group_limit_role;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRoles() {
    return $this->group_roles;
  }

  /**
   * {@inheritdoc}
   */
  public function setRoles($group_roles) {
    $this->group_roles = $group_roles;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->group_message;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessage($group_message) {
    $this->group_message = $group_message;
    return $this;
  }

}
