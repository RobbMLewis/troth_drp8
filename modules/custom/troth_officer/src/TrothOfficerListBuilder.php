<?php

namespace Drupal\troth_officer;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\troth_officer\Entity\TrothOfficer;

/**
 * Class TrothOfficerTypeListBuilder.
 */
class TrothOfficerListBuilder extends EntityListBuilder {

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
   * Constructs a new TrothOfficerListBuilder object.
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
    $pagetype = \Drupal::routeMatch()->getParameter('pagetype');
    $today = new DrupalDateTime();
    $query = $this->getStorage()->getQuery();
    if ($pagetype == 'archive') {
      $query->condition('enddate', $today->getTimestamp(), '<');
      $query->sort('enddate', 'desc');
      return $query->execute();
    }
    else {
      $query->condition('enddate', $today->getTimestamp(), '>=');

      $query->sort('enddate', 'ASC');
      $results = $query->execute();
      $office = [];
      foreach ($results as $id) {
        $entity = TrothOfficer::load($id);
        $office_name = $entity->getOffice()->getName();
        $group = $entity->getOffice()->getOfficeType();
        if (!isset($office[$group])) {
          $office[$group] = [];
        }
        if (!isset($office[$group][$office_name])) {
          $office[$group][$office_name] = [];
        }
        $office[$group][$office_name][] = $id;
      }
      ksort($office);
      $ret = [];
      foreach ($office as $group => $data) {
        ksort($data);
        foreach ($data as $name => $ids) {
          foreach ($ids as $id) {
            $ret[$id] = $id;
          }
        }
      }
      return $ret;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Entry');
    $header['name'] = $this->t('Office');
    $header['user'] = $this->t('User');
    $header['email'] = $this->t('Office Email');
    $header['start'] = $this->t('Term Start');
    $header['end'] = $this->t('Term End');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\practical\Entity\TrothOfficerEntityInterface $entity */
    $account = $entity->getOfficer();
                $fname = $account->field_profile_first_name->value;
            $lname = $account->field_profile_last_name->value;
    $row['id'] = $entity->toLink($entity->id());
    $row['list'] = $entity->getOffice()->getName();
    $row['name'] = $entity->getOfficer()->toLink("$fname $lname");
    $row['email'] = $entity->getOffice()->getEmail();
    $row['start'] = $this->dateFormatter->format($entity->getStartTimestamp(), 'troth_date');
    $row['end'] = $this->dateFormatter->format($entity->getEndTimestamp(), 'troth_date');
    return $row + parent::buildRow($entity);
  }

}
