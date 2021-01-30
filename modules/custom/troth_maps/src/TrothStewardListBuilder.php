<?php

namespace Drupal\troth_maps;

use Drupal\user\Entity\User;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\troth_maps\Entity\TrothMapsRegions;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class TrothStewardTypeListBuilder.
 */
class TrothStewardListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;
  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The Bundle Names.
   *
   * @var array
   */
  protected $names;

  /**
   * Constructs a new TrothStewardListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatter $date_formatter, RendererInterface $renderer) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $stewardtype = \Drupal::routeMatch()->getParameter('stewardtype');
    $today = new DrupalDateTime();
    $query = $this->getStorage()->getQuery();
    if ($stewardtype == 'archive') {
      $query->condition('enddate', $today->getTimestamp(), '<');
    }
    else {
      $query->condition('enddate', $today->getTimestamp(), '>=');
    }
    $query->sort('enddate', 'DESC');
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Office');
    $header['user'] = $this->t('User');
    $header['start'] = $this->t('Term Start');
    $header['end'] = $this->t('Term End');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\troth_maps\Entity\TrothStewardEntityInterface $entity */
    $region = TrothMapsRegions::load($entity->getRegionId());
    $user = User::load($entity->getOfficerId());
    $name = $user->field_profile_first_name->value . " " . $user->field_profile_last_name->value;
    if ($user->field_profile_troth_name->value != '') {
      $name .= " (" . $user->field_profile_troth_name->value . ")";
    }
    $row['list'] = $region->getRegionName();
    $row['name'] = $entity->getOfficer()->toLink($name);
    $row['start'] = $this->dateFormatter->format($entity->getStartTimestamp(), 'troth_date');
    $row['end'] = $this->dateFormatter->format($entity->getEndTimestamp(), 'troth_date');
    return $row + parent::buildRow($entity);
  }

}
