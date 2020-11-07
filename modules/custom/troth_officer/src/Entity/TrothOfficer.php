<?php

namespace Drupal\troth_officer\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Defines the troth_officer entity.
 *
 * @ContentEntityType(
 *   id = "troth_officer",
 *   label = @Translation("Officer Entry"),
 *   base_table = "troth_officer",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "bundle",
 *     "uid" = "uid",
 *     "startdate" = "startdate",
 *     "enddate" = "enddate",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   fieldable = FALSE,
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\troth_officer\TrothOfficerListBuilder",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\troth_officer\Form\TrothOfficerEntityForm",
 *       "add" = "Drupal\troth_officer\Form\TrothOfficerEntityForm",
 *       "edit" = "Drupal\troth_officer\Form\TrothOfficerEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   links = {
 *     "canonical" = "/admin/config/troth/officer/officer/{troth_officer}",
 *     "add-page" = "/admin/config/troth/officer/officer/add",
 *     "add-form" = "/admin/config/troth/officer/officer/add/{troth_officer_type}",
 *     "edit-form" = "/admin/config/troth/officer/officer/{troth_officer}/edit",
 *     "delete-form" = "/admin/config/troth/officer/officer/{troth_officer}/delete",
 *     "collection" = "/admin/config/troth/officer/officer",
 *   },
 *   admin_permission = "administer site configuration",
 *   bundle_entity_type = "troth_officer_type",
 *   field_ui_base_route = "entity.troth_officer_type.edit_form",
 * )
 */
class TrothOfficer extends ContentEntityBase implements TrothOfficerEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getOfficer() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOfficer(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOfficerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOfficerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStartDate() {
    return $this->get('startdate')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getStartTimestamp() {
    $date = new DrupalDateTime($this->get('startdate')->value);
    return $date->getTimestamp();
  }

  /**
   * {@inheritdoc}
   */
  public function setStartDate($date) {
    $this->set('startdate', $date);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndDate() {
    return $this->get('enddate')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndTimestamp() {
    $date = new DrupalDateTime($this->get('enddate')->value);
    return $date->getTimestamp();
  }

  /**
   * {@inheritdoc}
   */
  public function setEndDate($date) {
    $this->set('enddate', $date);
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

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Officer'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'string',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDescription(t('The user ID of Officer.'));

    $fields['startdate'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Start Date'))
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'date',
      ])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'troth_date',
        ],
        'weight' => 4,
      ])
      ->setDescription(t("The date the person's term starts."));

    $fields['enddate'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End Date'))
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'date',
      ])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'troth_date',
        ],
        'weight' => 4,
      ])
      ->setDescription(t("The date the person's term ends."));

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
