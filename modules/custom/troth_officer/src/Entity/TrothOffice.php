<?php

namespace Drupal\troth_officer\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the troth_office entity.
 *
 * @ContentEntityType(
 *   id = "troth_office",
 *   label = @Translation("Office Entry"),
 *   base_table = "troth_office",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "office_name",
 *     "office_type",
 *     "office_email",
 *     "office_description",
 *     "office_term",
 *     "office_roles",
 *     "office_number_open",
 *
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   fieldable = FALSE,
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\troth_officer\TrothOfficeListBuilder",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\troth_officer\Form\TrothOfficeEntityForm",
 *       "add" = "Drupal\troth_officer\Form\TrothOfficeEntityForm",
 *       "edit" = "Drupal\troth_officer\Form\TrothOfficeEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "storage_schema" = "Drupal\troth_officer\TrothOfficerEntityStorageSchema",
 *   },
 *   links = {
 *     "canonical" = "/admin/config/troth/officer/office/{troth_office}",
 *     "edit-form" = "/admin/config/troth/officer/office/{troth_office}/edit",
 *     "delete-form" = "/admin/config/troth/officer/office/{troth_office}/delete",
 *     "collection" = "/admin/config/troth/officer/office",
 *   },
 *   admin_permission = "administer site configuration",
 * )
 */
class TrothOffice extends ContentEntityBase implements TrothOfficeEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('office_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($office_name) {
    $this->set('office_name', $office_name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOfficeType() {
    return $this->get('office_type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setOfficeType($office_type) {
    $this->set('office_type', $office_type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEmail() {
    return $this->get('office_email')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail($office_email) {
    $this->set('office_email', $office_email);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $desc['value'] = $this->get('office_description')->value;
    $desc['format'] = $this->get('office_description')->format;
    return $desc;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($office_description) {
    $this->set('office_description', $office_description);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTerm() {
    return $this->get('office_term')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTerm($office_term) {
    $this->set('office_term', $office_term);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOpen() {
    return $this->get('office_open')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setOpen($office_open) {
    $this->set('office_open', $office_open);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNumOpen() {
    return $this->get('office_number_open')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setNumOpen($office_number_open) {
    $this->set('office_number_open', $office_number_open);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRoles() {
    $values = $this->get('office_roles')->value;
    return unserialize($values);
  }

  /**
   * {@inheritdoc}
   */
  public function setRoles($office_roles) {
    $this->set('office_roles', serialize($office_roles));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * Determines the schema for the base_table property defined above.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields['office_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Office Name'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDescription(t("The name of the office."));

    $fields['office_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Office Type'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDescription(t("The tyoe of the office."));

    $fields['office_email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Office Email'))
      ->setDescription(t('Email Address of the office.'));

    $fields['office_description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDescription(t('Short blurb on the description of the office.  This will appear on ballots.'));

    $fields['office_term'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Term Length'))
      ->setSetting('unsigned', FALSE)
      ->setDescription(t('Length of the term.'));

    $fields['office_open'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Open?'))
      ->setDescription(t('Is the office currently open for election'));

    $fields['office_number_open'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Number Open'))
      ->setSetting('unsigned', FALSE)
      ->setDescription(t('Number of office open'));

    $fields['office_roles'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Office Roles'))
      ->setDescription(t("The roles for the office."));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'datetime_default',
        'weight' => 4,
        'settings' => [
          'format_type' => 'troth_date_time',
        ],
      ])
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'troth_date_time',
        ],
      ])
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
