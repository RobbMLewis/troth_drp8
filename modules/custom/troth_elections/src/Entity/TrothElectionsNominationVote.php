<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the troth_elections_nomination_vote entity.
 *
 * @ContentEntityType(
 *   id = "troth_elections_nomination_vote",
 *   label = @Translation("Elections Emails"),
 *   base_table = "troth_elections_nomination_vote",
 *   revision_table = "troth_elections_nomination_vote_revision",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "bundle",
 *     "revision" = "revision_id",
 *     "uidhash" = "uidhash",
 *     "office_id" = "office_id",
 *     "candidate" = "candidate",
 *     "vote" = "vote",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   fieldable = FALSE,
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer site configuration",
 *   bundle_entity_type = "troth_elections_nomination_type",
 * )
 */
class TrothElectionsNominationVote extends ContentEntityBase implements TrothElectionsNominationVoteEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getMemHash() {
    return $this->get('uidhash')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setMemHash($uidhash) {
    $this->set('uidhash', $uidhash);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setMemHashUid($uid) {
    $this->set('uidhash', md5($uid));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOfficeId() {
    return $this->get('office_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setOfficeId($office_id) {
    $this->set('office_id', $office_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCandidate() {
    return $this->get('candidate')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCandidate($candidate) {
    $this->set('candidate', $candidate);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getVote() {
    return $this->get('vote')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setVote($vote) {
    $this->set('vote', $vote);
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

    $fields['uidhash'] = BaseFieldDefinition::create('string')
      ->setLabel(t('MemHash'))
      ->setDescription(t('Unique hash of member uid.'));

    $fields['office_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Office'))
      ->setDescription(t('Office'));

    $fields['candidate'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Candidate'))
      ->setDescription(t('Candidate'));

    $fields['vote'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Vote'))
      ->setRevisionable(TRUE)
      ->setDescription(t('Vote of the member'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'date',
        'weight' => 4,
      ])
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'date',
      ])
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
