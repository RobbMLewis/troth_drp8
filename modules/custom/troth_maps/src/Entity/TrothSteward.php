<?php

namespace Drupal\troth_maps\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Defines the troth_maps entity.
 *
 * @ContentEntityType(
 *   id = "troth_steward",
 *   label = @Translation("Steward Entity"),
 *   base_table = "troth_steward",
 *   entity_keys = {
 *     "id" = "id",
 *     "region_id" = "region_id",
 *     "uid" = "uid",
 *     "startdate" = "startdate",
 *     "enddate" = "enddate",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   fieldable = FALSE,
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\troth_maps\TrothStewardListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\troth_maps\Form\TrothStewardEntityForm",
 *       "add" = "Drupal\troth_maps\Form\TrothStewardEntityForm",
 *       "edit" = "Drupal\troth_maps\Form\TrothStewardEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   links = {
 *     "canonical" = "/admin/config/troth/maps/steward/{troth_steward}",
 *     "add-form" = "/admin/config/troth/maps/steward/add",
 *     "edit-form" = "/admin/config/troth/maps/steward/{troth_steward}/edit",
 *     "delete-form" = "/admin/config/troth/maps/steward/{troth_steward}/delete",
 *     "collection" = "/admin/config/troth/maps/steward",
 *   },
 *   admin_permission = "administer site configuration",
 * )
 */
class TrothSteward extends ContentEntityBase implements TrothStewardEntityInterface {

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
  public function getRegionId() {
    return $this->get('region_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setRegionId($region_id) {
    $this->set('region_id', $region_id);
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

    $fields['region_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Region'))
      ->setSetting('target_type', 'troth_maps_regions')
      ->setSetting('handler', 'default:troth_maps_regions')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'region_name',
          'placeholder' => '',
        ],
      ])
      ->setDescription(t('The Region ID of Officer.'));

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Officer'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default:troth_user')
      ->setRequired(TRUE)
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
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'date',
      ])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'medium',
        ],
        'weight' => 4,
      ])
      ->setDescription(t("The date the person's term starts."));

    $fields['enddate'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End Date'))
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'date',
      ])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'medium',
        ],
        'weight' => 4,
      ])
      ->setDescription(t("The date the person's term ends."));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'date',
        'weight' => 4,
      ])
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'date',
      ])
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
