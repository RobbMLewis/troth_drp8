<?php

/**
 * @file
 * Batch processing.
 */

use Drupal\troth_elections\Entity\TrothElectionsEmails;
use Drupal\troth_elections\Entity\TrothElectionsPropositionVote;
use Drupal\troth_elections\Entity\TrothElectionsNominationVote;
use Drupal\troth_elections\Entity\TrothElectionsNominationVoter;
use Drupal\troth_elections\Entity\TrothElectionsNominationBios;
use Drupal\troth_elections\Entity\TrothElectionsNomination;

/**
 * Runs through entity archives data, and deletes entries.
 */
function empty_entity($year, $entityType, &$context) {
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $context['sandbox']['ptrothress'] = 0;
    $context['sandbox']['current_id'] = 0;
    $context['sandbox']['max'] = \Drupal::entityQuery($entityType)->count()->execute();
  }

  \Drupal::logger('troth_elections')->notice($entityType);
  // Set the limit.
  $limit = 100;

  // Get the entids.
  $query = \Drupal::entityQuery($entityType)
    ->range(0, $limit);
  $entids = $query->execute();
  ksort($entids);
  $rows = [];

  switch ($entityType) {
    case 'troth_elections_emails':
      // Load the entities.
      $entities = TrothElectionsEmails::loadMultiple($entids);
      $header = 'id,bundle,uid,email,sent,created,changed';
      // Go through the entities, get data, and add to rows.
      foreach ($entities as $entity) {
        $id = $entity->id();
        $bundle = $entity->bundle();
        $uid = $entity->getOwnerId();
        $email = $entity->getEmail();
        $dateSent = $entity->getDateSent();
        $created = $entity->getCreatedTime();
        $changed = $entity->getChangedTime();
        $rows[] = "$id,$bundle,$uid,$email,$dateSent,$created,$changed";
        // Advance the counter.
        $context['sandbox']['ptrothress']++;
        $context['sandbox']['current_id'] = $id;
        // Delete the entity.
        $entity->delete();
      }

      break;

    case 'troth_elections_proposition_vote':
      // Load the entities.
      $entities = TrothElectionsPropositionVote::loadMultiple($entids);
      $header = 'id,bundle,uidhash,proposition_id,vote,created,changed';
      // Go through the entities, get data, and add to rows.
      foreach ($entities as $entity) {
        $id = $entity->id();
        $bundle = $entity->bundle();
        $memhash = $entity->getMemHash();
        $propid = $entity->getPropositionId();
        $vote = rtrim($entity->getVote());
        $created = $entity->getCreatedTime();
        $changed = $entity->getChangedTime();
        $rows[] = "$id,$bundle,$memhash,$propid,$vote,$created,$changed";
        // Advance the counter.
        $context['sandbox']['ptrothress']++;
        $context['sandbox']['current_id'] = $id;
        // Delete the entity.
        $entity->delete();
      }

      break;

    case 'troth_elections_nomination_vote':
      // Load the entities.
      $entities = TrothElectionsNominationVote::loadMultiple($entids);
      $header = 'id,bundle,uidhash,office_id,candidate,vote,created,changed';
      // Go through the entities, get data, and add to rows.
      foreach ($entities as $entity) {
        $id = $entity->id();
        $bundle = $entity->bundle();
        $memhash = $entity->getMemHash();
        $officeid = $entity->getOfficeId();
        $candidate = $entity->getCandidate();
        $vote = $entity->getVote();
        $created = $entity->getCreatedTime();
        $changed = $entity->getChangedTime();
        $rows[] = "$id,$bundle,$memhash,$officeid,$candidate,$vote,$created,$changed";
        // Advance the counter.
        $context['sandbox']['ptrothress']++;
        $context['sandbox']['current_id'] = $id;
        // Delete the entity.
        $entity->delete();
      }

      break;

    case 'troth_elections_nomination_voter':
      // Load the entities.
      $entities = TrothElectionsNominationVoter::loadMultiple($entids);
      $header = 'id,bundle,uid,signature,proxy,ip,created,changed';
      // Go through the entities, get data, and add to rows.
      foreach ($entities as $entity) {
        $id = $entity->id();
        $bundle = $entity->bundle();
        $uid = $entity->getOwnerId();
        $signature = $entity->getSignature();
        $proxy = $entity->getProxy();
        $ip = $entity->getIp();
        $created = $entity->getCreatedTime();
        $changed = $entity->getChangedTime();
        $rows[] = "$id,$bundle,$uid,$signature,$proxy,$ip,$created,$changed";
        // Advance the counter.
        $context['sandbox']['ptrothress']++;
        $context['sandbox']['current_id'] = $id;
        // Delete the entity.
        $entity->delete();
      }

      break;

    case 'troth_elections_nomination_bios':
      // Load the entities.
      $entities = TrothElectionsNominationBios::loadMultiple($entids);
      $header = 'id,bundle,uid,bio,created,changed';
      // Go through the entities, get data, and add to rows.
      foreach ($entities as $entity) {
        $id = $entity->id();
        $bundle = $entity->bundle();
        $uid = $entity->getOwnerId();
        $bio = $entity->getBio()->value;
        $created = $entity->getCreatedTime();
        $changed = $entity->getChangedTime();
        $rows[] = "$id,$bundle,$uid,\"$bio\",$created,$changed";
        // Advance the counter.
        $context['sandbox']['ptrothress']++;
        $context['sandbox']['current_id'] = $id;
        // Delete the entity.
        $entity->delete();
      }

      break;

    case 'troth_elections_nomination':
      // Load the entities.
      $entities = TrothElectionsNomination::loadMultiple($entids);
      $header = 'id,label,uid,office_id,created,changed';
      // Go through the entities, get data, and add to rows.
      foreach ($entities as $entity) {
        $id = $entity->id();
        $bundle = $entity->bundle();
        $uid = $entity->getOwnerId();
        $office = $entity->getOfficeId();
        $created = $entity->getCreatedTime();
        $changed = $entity->getChangedTime();
        $rows[] = "$id,$bundle,$uid,$office,$created,$changed";
        // Advance the counter.
        $context['sandbox']['ptrothress']++;
        $context['sandbox']['current_id'] = $id;
        // Delete the entity.
        $entity->delete();
      }

      break;

    default:
      $context['sandbox']['ptrothress'] = $context['sandbox']['max'];

  }
  if (!isset($context['sandbox']['filename'])) {
    // We haven't created the file, create it and enter headers.
    $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_path');
    $file = $path . '/' . $year . '_' . $entityType . '.csv';
    $context['sandbox']['filename'] = $file;
    // Save the header.
    $fh = fopen($file, 'w');
    fwrite($fh, $header . "\n");
    fclose($fh);
  }

  // Save the files.
  $fh = fopen($context['sandbox']['filename'], 'a');
  foreach ($rows as $row) {
    fwrite($fh, $row . "\n");
  }
  fclose($fh);

  if ($context['sandbox']['ptrothress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['ptrothress'] / $context['sandbox']['max'];
  }
}

/**
 * Runs through entity archives data, and deletes entries.
 */
function empty_entity_type($year, $entityType, &$context) {
  // Get proposition data.
  $propstorage = \Drupal::entityTypeManager()
    ->getStorage($entityType);
  $results = \Drupal::entityQuery($entityType)
    ->execute();
  $entities = $propstorage->loadMultiple($results);
  $rows = [];
  switch ($entityType) {
    case 'troth_elections_nomination_type':
      $header = 'id,label,office_id,uid,nominated,accepted,declined,ineligible,numnoms';
      // Go through the entities, get data, and add to rows.
      foreach ($entities as $entity) {
        $id = $entity->id();
        $label = $entity->getName();
        $office_id = $entity->getOffice();
        $uid = $entity->getNominee();
        $nominated = $entity->getNominated();
        $accepted = $entity->getAccepted();
        $declined = $entity->getDeclined();
        $ineligible = $entity->getIneligible();
        $numnoms = $entity->getNumNoms();
        $rows[] = "$id,$label,$office_id,$uid,$nominated,$accepted,$declined,$ineligible,$numnoms";
        // Advance the counter.
        $context['sandbox']['ptrothress']++;
        $context['sandbox']['current_id'] = $id;
        // Delete the entity.
        $entity->delete();
      }
      break;

    case 'troth_elections_proposition_type':
      $header = 'id,label,text,options';
      // Go through the entities, get data, and add to rows.
      foreach ($entities as $entity) {
        $id = $entity->id();
        $label = $entity->getName();
        $text = $entity->getText()['value'];
        $options = $entity->getOptions();
        $rows[] = "$id,$label,\"$text\",\"$options\"";
        // Advance the counter.
        $context['sandbox']['ptrothress']++;
        $context['sandbox']['current_id'] = $id;
        // Delete the entity.
        $entity->delete();
      }
      break;
  }
  // We haven't created the file, create it and enter headers.
  $path = \Drupal::config('troth_elections.adminsettings')->get('troth_elections_path');
  $file = $path . '/' . $year . '_' . $entityType . '.csv';
  // Save the header.
  $fh = fopen($file, 'w');
  fwrite($fh, $header . "\n");
  foreach ($rows as $row) {
    fwrite($fh, $row . "\n");
  }
  fclose($fh);
}

/**
 * Finishes the batch processing.
 */
function batch_finish_callback($success, $results, $operations) {
  // The 'success' parameter means no fatal PHP errors were detected. All
  // other error management should be handled using 'results'.
  if ($success) {
    $message = \Drupal::translation()
      ->formatPlural(count($results), 'One post processed.', '@count posts processed.');
    $message = implode(',', $results);
  }
  else {
    $message = t('Finished with an error.');
  }
  drupal_set_message($message);
}
