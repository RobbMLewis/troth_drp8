<?php

namespace Drupal\troth_officer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * TrothOfficer Type.
 *
 * @ConfigEntityType(
 *   id = "troth_officer_type",
 *   label = @Translation("TrothOfficer Type"),
 *   bundle_of = "troth_officer",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_prefix = "type",
 *   config_export = {
 *     "id",
 *     "label",
 *     "office_id",
 *     "office_name",
 *     "office_type",
 *     "office_email",
 *     "office_description",
 *     "office_term",
 *     "office_open",
 *     "office_number_open",
 *     "office_roles",
 *   },
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\troth_officer\Form\TrothOfficerTypeEntityForm",
 *       "add" = "Drupal\troth_officer\Form\TrothOfficerTypeEntityForm",
 *       "edit" = "Drupal\troth_officer\Form\TrothOfficerTypeEntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\troth_officer\TrothOfficerTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer site configuration",
 *   links = {
 *     "canonical" = "/admin/config/troth/officer/office/{troth_officer_type}",
 *     "add-form" = "/admin/config/troth/officer/office/add",
 *     "edit-form" = "/admin/config/troth/officer/office/{troth_officer_type}/edit",
 *     "delete-form" = "/admin/config/troth/officer/office/{troth_officer_type}/delete",
 *     "collection" = "/admin/config/troth/officer/office",
 *   }
 * )
 */
class TrothOfficerType extends ConfigEntityBundleBase {
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
  protected $office_id;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $office_name;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $office_type;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $office_description;

  /**
   * A brief description of the practical type.
   *
   * @var int
   */
  protected $office_term;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $office_open;

  /**
   * A brief description of the practical type.
   *
   * @var int
   */
  protected $office_number_open;

  /**
   * A brief description of the practical type.
   *
   * @var string
   */
  protected $office_email;

  /**
   * A brief description of the practical type.
   *
   * @var array
   */
  protected $office_roles;

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
  public function getOfficeId() {
    return $this->office_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOfficeId($office_id) {
    $this->office_id = $office_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOfficeType() {
    return $this->office_type;
  }

  /**
   * {@inheritdoc}
   */
  public function setOfficeType($office_type) {
    $this->office_type = $office_type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->office_description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($office_description) {
    $this->office_description = $office_description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTerm() {
    return $this->office_term;
  }

  /**
   * {@inheritdoc}
   */
  public function setTerm($office_term) {
    $this->office_term = $office_term;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOpen() {
    return $this->office_open;
  }

  /**
   * {@inheritdoc}
   */
  public function setOpen($office_open) {
    $this->office_open = $office_open;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumOpen() {
    return $this->office_number_open;
  }

  /**
   * {@inheritdoc}
   */
  public function setNumOpen($office_number_open) {
    $this->office_number_open = $office_number_open;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEmail() {
    return $this->office_email;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail($office_email) {
    $this->office_email = $office_email;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRoles() {
    return $this->office_roles;
  }

  /**
   * {@inheritdoc}
   */
  public function setRoles($office_roles) {
    $this->office_roles = $office_roles;
    return $this;
  }

}
