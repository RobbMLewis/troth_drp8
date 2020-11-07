<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the troth_elections_nomination_voter entity.
 *
 * @ContentEntityType(
 *   id = "troth_elections_nomination_voter",
 *   label = @Translation("Elections Emails"),
 *   base_table = "troth_elections_nomination_voter",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "bundle",
 *     "uid" = "uid",
 *     "signature" = "signature",
 *     "proxy" = "proxy",
 *     "ip" = "ip",
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
class TrothElectionsNominationVoter extends ContentEntityBase implements TrothElectionsNominationVoterEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSignature() {
    return $this->get('signature')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSignature($signature) {
    $this->set('signature', $signature);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getProxy() {
    return $this->get('proxy')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setProxy($proxy) {
    $this->set('proxy', $proxy);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIp() {
    return $this->get('ip')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setIp($ip) {
    $this->set('ip', $ip);
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
      ->setLabel(t('Owner'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDescription(t('The user ID of user.'));

    $fields['signature'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Signature'))
      ->setDescription(t('Signature of the voter'));

    $fields['proxy'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Proxy'))
      ->setDescription(t('Proxy of the voter.'));

    $fields['ip'] = BaseFieldDefinition::create('string')
      ->setLabel(t('IP Address'))
      ->setDescription(t('IP Address of the voter.'));

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
