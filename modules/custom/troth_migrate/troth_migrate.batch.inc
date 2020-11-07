<?php

/**
 * @file
 * Batch processing.
 */

use Drupal\Core\Database\Database;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\taxonomy\Entity\Term;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\profile\Entity\Profile;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_price\Price;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_payment\Entity\Payment;

/**
 * Function to migrate the roles.
 */
function migrate_roles(&$context) {
  $context['message'] = t('Migrating User Roles');
  $roles = get_roles();

  foreach ($roles as $rid => $name) {
    $mname = get_machine_name($name);
    $role = Role::load($mname);
    if ($role == '') {
      // We don't have a role, we create it.
      $data = ['id' => $mname, 'label' => $name];
      $role = Role::create($data);
      $role->save();
    }
  }
  $context['finished'] = 1;
}

/**
 * Function to migrate the users.
 */
function migrate_users(&$context) {
  $context['message'] = t('Migrating Users');
  $limit = 20;
  $con = Database::getConnection('default', 'migrate');
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $query = $con->select('users', 'u')
      ->fields('u', ['uid'])
      ->condition('u.uid', '10', '>=')
      ->orderBy('uid');
    $count = $query->countQuery()->execute()->fetchField();
    $context['sandbox']['max'] = $count + 1;
    $context['sandbox']['current'] = 2;
  }

  // We need to build the query to get all the user info we need.
  $current = $context['sandbox']['current'] + 1;
  $roles = get_roles();
  $query = $con->select('users', 'users')
    ->fields('users');
  $query->leftjoin('field_data_field_profile_first_name', 'firstname', 'firstname.entity_type =:user and firstname.bundle= :user and firstname.entity_id = users.uid', [':user' => 'user']);
  $query->fields('firstname', ['field_profile_first_name_value']);
  $query->leftjoin('field_data_field_profile_last_name', 'lastname', 'lastname.entity_type =:user and lastname.bundle= :user and lastname.entity_id = users.uid', [':user' => 'user']);
  $query->fields('lastname', ['field_profile_last_name_value']);
  $query->leftjoin('field_data_field_profile_troth_name', 'trothname', 'trothname.entity_type =:user and trothname.bundle= :user and trothname.entity_id = users.uid', [':user' => 'user']);
  $query->fields('trothname', ['field_profile_troth_name_value']);
  $query->leftjoin('field_data_field_profile_alt_email', 'altemail', 'altemail.entity_type =:user and altemail.bundle= :user and altemail.entity_id = users.uid', [':user' => 'user']);
  $query->fields('altemail', ['field_profile_alt_email_email']);
  $query->leftjoin('field_data_field_profile_location', 'address', 'address.entity_type =:user and address.bundle= :user and address.entity_id = users.uid', [':user' => 'user']);
  $query->fields('address', ['field_profile_location_country',
    'field_profile_location_administrative_area',
    'field_profile_location_sub_administrative_area',
    'field_profile_location_locality',
    'field_profile_location_dependent_locality',
    'field_profile_location_postal_code',
    'field_profile_location_thoroughfare',
    'field_profile_location_premise',
    'field_profile_location_sub_premise',
    'field_profile_location_organisation_name',
    'field_profile_location_name_line',
    'field_profile_location_first_name',
    'field_profile_location_last_name',
    'field_profile_location_data',
  ]);
  $query->leftjoin('field_data_field_profile_day_phone', 'dayphone', 'dayphone.entity_type =:user and dayphone.bundle= :user and dayphone.entity_id = users.uid', [':user' => 'user']);
  $query->fields('dayphone', ['field_profile_day_phone_value']);
  $query->leftjoin('field_data_field_profile_night_phone', 'nightphone', 'nightphone.entity_type =:user and nightphone.bundle= :user and nightphone.entity_id = users.uid', [':user' => 'user']);
  $query->fields('nightphone', ['field_profile_night_phone_value']);
  $query->leftjoin('field_data_field_profile_visibility', 'visibility', 'visibility.entity_type =:user and visibility.bundle= :user and visibility.entity_id = users.uid', [':user' => 'user']);
  $query->fields('visibility', ['field_profile_visibility_value']);
  $query->leftjoin('field_data_field_profile_member_notes', 'memnotes', 'memnotes.entity_type =:user and memnotes.bundle= :user and memnotes.entity_id = users.uid', [':user' => 'user']);
  $query->fields('memnotes', ['field_profile_member_notes_value']);
  $query->leftjoin('field_data_field_profile_shared_notes', 'sharednotes', 'sharednotes.entity_type =:user and sharednotes.bundle= :user and sharednotes.entity_id = users.uid', [':user' => 'user']);
  $query->fields('sharednotes', ['field_profile_shared_notes_value']);
  $query->leftjoin('field_data_field_user_facebook_url', 'facebook', 'facebook.entity_type =:user and facebook.bundle= :user and facebook.entity_id = users.uid', [':user' => 'user']);
  $query->fields('facebook', ['field_user_facebook_url_url']);
  $query->leftjoin('field_data_field_user_twitter_url', 'twitter', 'twitter.entity_type =:user and twitter.bundle= :user and twitter.entity_id = users.uid', [':user' => 'user']);
  $query->fields('twitter', ['field_user_twitter_url_url']);
  $query->leftjoin('field_data_field_user_instagram_url', 'instagram', 'instagram.entity_type =:user and instagram.bundle= :user and instagram.entity_id = users.uid', [':user' => 'user']);
  $query->fields('instagram', ['field_user_instagram_url_url']);
  $query->leftjoin('field_data_field_user_linkedin_url', 'linkedin', 'linkedin.entity_type =:user and linkedin.bundle= :user and linkedin.entity_id = users.uid', [':user' => 'user']);
  $query->fields('linkedin', ['field_user_linkedin_url_url']);
  $query->leftjoin('field_data_field_profile_birth_date', 'birthdate', 'birthdate.entity_type =:user and birthdate.bundle= :user and birthdate.entity_id = users.uid', [':user' => 'user']);
  $query->fields('birthdate', ['field_profile_birth_date_value']);
  $query->leftjoin('field_data_field_profile_membership_type', 'membtype', 'membtype.entity_type =:user and membtype.bundle= :user and membtype.entity_id = users.uid', [':user' => 'user']);
  $query->fields('membtype', ['field_profile_membership_type_value']);
  $query->leftjoin('field_data_field_profile_membership_status', 'memstatus', 'memstatus.entity_type =:user and memstatus.bundle= :user and memstatus.entity_id = users.uid', [':user' => 'user']);
  $query->fields('memstatus', ['field_profile_membership_status_value']);
  $query->leftjoin('field_data_field_profile_membership_control', 'memcontrol', 'memcontrol.entity_type =:user and memcontrol.bundle= :user and memcontrol.entity_id = users.uid', [':user' => 'user']);
  $query->fields('memcontrol', ['field_profile_membership_control_value']);
  $query->leftjoin('field_data_field_profile_member_start_date', 'memstart', 'memstart.entity_type =:user and memstart.bundle= :user and memstart.entity_id = users.uid', [':user' => 'user']);
  $query->fields('memstart', ['field_profile_member_start_date_value']);
  $query->leftjoin('field_data_field_profile_last_renew_date', 'memrenew', 'memrenew.entity_type =:user and memrenew.bundle= :user and memrenew.entity_id = users.uid', [':user' => 'user']);
  $query->fields('memrenew', ['field_profile_last_renew_date_value']);
  $query->leftjoin('field_data_field_profile_member_expire_date', 'memexpire', 'memexpire.entity_type =:user and memexpire.bundle= :user and memexpire.entity_id = users.uid', [':user' => 'user']);
  $query->fields('memexpire', ['field_profile_member_expire_date_value']);
  $query->leftjoin('field_data_field_profile_expire_issue', 'expissue', 'expissue.entity_type =:user and expissue.bundle= :user and expissue.entity_id = users.uid', [':user' => 'user']);
  $query->fields('expissue', ['field_profile_expire_issue_value']);
  $query->leftjoin('field_data_field_profile_e_copies', 'ecopy', 'ecopy.entity_type =:user and ecopy.bundle= :user and ecopy.entity_id = users.uid', [':user' => 'user']);
  $query->fields('ecopy', ['field_profile_e_copies_value']);
  $query->leftjoin('field_data_field_profile_family_id', 'famid', 'famid.entity_type =:user and famid.bundle= :user and famid.entity_id = users.uid', [':user' => 'user']);
  $query->fields('famid', ['field_profile_family_id_value']);
  $query->leftjoin('field_data_field_profile_inmate', 'inmate', 'inmate.entity_type =:user and inmate.bundle= :user and inmate.entity_id = users.uid', [':user' => 'user']);
  $query->fields('inmate', ['field_profile_inmate_value']);
  $query->leftjoin('field_data_field_profile_inmate_number', 'inmateno', 'inmateno.entity_type =:user and inmateno.bundle= :user and inmateno.entity_id = users.uid', [':user' => 'user']);
  $query->fields('inmateno', ['field_profile_inmate_number_value']);
  $query->leftjoin('field_data_field_profile_dont_contact', 'nocontact', 'inmateno.entity_type =:user and nocontact.bundle= :user and nocontact.entity_id = users.uid', [':user' => 'user']);
  $query->fields('nocontact', ['field_profile_dont_contact_value']);
  $query->leftjoin('field_data_field_profile_last_contact_date', 'lastcontact', 'lastcontact.entity_type =:user and lastcontact.bundle= :user and lastcontact.entity_id = users.uid', [':user' => 'user']);
  $query->fields('lastcontact', ['field_profile_last_contact_date_value']);
  $query->leftjoin('field_data_field_profile_admin_notes', 'adminnotes', 'adminnotes.entity_type =:user and adminnotes.bundle= :user and adminnotes.entity_id = users.uid', [':user' => 'user']);
  $query->fields('adminnotes', ['field_profile_admin_notes_value']);
  $query->leftjoin('field_data_field_profile_ship_notes', 'shipnotes', 'shipnotes.entity_type =:user and shipnotes.bundle= :user and shipnotes.entity_id = users.uid', [':user' => 'user']);
  $query->fields('shipnotes', ['field_profile_ship_notes_value']);
  $query->leftjoin('field_data_field_profile_gender', 'gender', 'gender.entity_type =:user and gender.bundle= :user and gender.entity_id = users.uid', [':user' => 'user']);
  $query->fields('gender', ['field_profile_gender_value']);
  $query->leftjoin('field_data_field_user_location', 'location', 'location.entity_type =:user and location.bundle= :user and location.entity_id = users.uid', [':user' => 'user']);
  $query->fields('location', ['field_user_location_lat',
    'field_user_location_lon',
  ]);
  $query->leftjoin('field_data_field_profile_ban_lists', 'mlban', 'mlban.entity_type =:user and mlban.bundle= :user and mlban.entity_id = users.uid', [':user' => 'user']);
  $query->fields('mlban', ['field_profile_ban_lists_value']);
  $query->range($current, $limit)->orderBy('uid');

  $results = $query->execute();
  while ($row = $results->fetchAssoc()) {
    $uid = $row['uid'];
    $account = User::load($uid);
    if ($account == "") {
      $account = User::create([
        'uid' => $uid,
        'name' => $row['name'],
        'mail' => $row['mail'],
        'pass' => [
          'value' => $row['pass'],
          'pre_hashed' => TRUE,
        ],
        'status' => $row['status'],
      ]);
      $account->save();
    }
    $account->name = $row['name'];
    $account->mail = $row['mail'];
    $account->pass->value = $row['pass'];
    $account->pass->pre_hashed = TRUE;
    $account->status = $row['status'];
    $account->created = $row['created'];
    $account->access = $row['access'];
    $account->login = $row['login'];
    $account->timezone = $row['timezone'];
    $account->language = $row['language'];
    $account->field_profile_first_name->value = $row['field_profile_first_name_value'];
    $account->field_profile_last_name->value = $row['field_profile_last_name_value'];
    $account->field_profile_troth_name->value = $row['field_profile_troth_name_value'];
    $account->field_profile_alt_email->value = $row['field_profile_alt_email_email'];
    $account->field_profile_location->country_code = $row['field_profile_location_country'];
    $account->field_profile_location->administrative_area = $row['field_profile_location_administrative_area'];
    $account->field_profile_location->locality = $row['field_profile_location_locality'];
    $account->field_profile_location->dependent_locality = $row['field_profile_location_dependent_locality'];
    $account->field_profile_location->postal_code = $row['field_profile_location_postal_code'];
    $account->field_profile_location->address_line1 = $row['field_profile_location_thoroughfare'];
    $account->field_profile_location->address_line2 = $row['field_profile_location_premise'];
    $account->field_profile_location->organization = $row['field_profile_location_organisation_name'];
    $account->field_profile_location->given_name = $row['field_profile_location_first_name'];
    $account->field_profile_location->family_name = $row['field_profile_location_last_name'];
    $phone = $row['field_profile_night_phone_value'];
    if ($phone == '') {
      $phone = $row['field_profile_day_phone_value'];
    }
    $account->field_telephone_number->value = $phone;
    $account->field_profile_visibility->value = $row['field_profile_visibility_value'];
    $account->field_profile_member_notes->value = $row['field_profile_member_notes_value'];
    $account->field_profile_member_notes->format = 'basic_html';
    $account->field_profile_shared_notes->value = $row['field_profile_shared_notes_value'];
    $account->field_profile_shared_notes->format = 'basic_html';
    $account->field_user_facebook_url = [
      'uri' => $row['field_user_facebook_url_url'],
      'title' => '',
      'options' => [],
    ];
    $account->field_user_twitter_url = [
      'uri' => $row['field_user_twitter_url_url'],
      'title' => '',
      'options' => [],
    ];
    $account->field_user_instagram_url = [
      'uri' => $row['field_user_instagram_url_url'],
      'title' => '',
      'options' => [],
    ];
    $account->field_user_linkedin_url = [
      'uri' => $row['field_user_linkedin_url_url'],
      'title' => '',
      'options' => [],
    ];
    $birthdate = new DrupalDateTime($row['field_profile_birth_date_value']);
    $account->field_profile_birth_date->value = $birthdate->format('Y-m-d');
    $account->field_profile_membership_type->value = $row['field_profile_membership_type_value'];
    $account->field_profile_membership_status->value = $row['field_profile_membership_status_value'];
    $account->field_profile_membership_control->value = $row['field_profile_membership_control_value'];
    $startdate = new DrupalDateTime($row['field_profile_member_start_date_value']);
    $account->field_profile_member_start_date->value = $startdate->format('Y-m-d');
    $renewdate = new DrupalDateTime($row['field_profile_last_renew_date_value']);
    $account->field_profile_last_renew_date->value = $renewdate->format('Y-m-d');
    $expiredate = new DrupalDateTime($row['field_profile_member_expire_date_value']);
    $account->field_profile_member_expire_date->value = $expiredate->format('Y-m-d');
    $account->field_profile_expire_issue->value = $row['field_profile_expire_issue_value'];
    $account->field_profile_e_copies->value = $row['field_profile_e_copies_value'];
    $account->field_profile_family_id->value = $row['field_profile_family_id_value'];
    $account->field_profile_inmate->value = $row['field_profile_inmate_value'];
    $account->field_profile_inmate_number->value = $row['field_profile_inmate_number_value'];
    $account->field_profile_dont_contact->value = $row['field_profile_dont_contact_value'];
    $lastcontact = new DrupalDateTime($row['field_profile_last_contact_date_value']);
    $account->field_profile_last_contact_date->value = $lastcontact->format('Y-m-d');
    $account->field_profile_admin_notes->value = $row['field_profile_admin_notes_value'];
    $account->field_profile_admin_notes->format = 'basic_html';
    $account->field_profile_ship_notes->value = $row['field_profile_ship_notes_value'];
    $account->field_profile_ship_notes->format = 'basic_html';
    $account->field_profile_gender->value = $row['field_profile_gender_value'];
    $lat = $row['field_user_location_lat'];
    $lon = $row['field_user_location_lon'];
    if ($lat != '' && $lon != '') {
      $account->field_user_location->setValue("POINT($lat $lon)");
    }

    $account->field_profile_ban_lists->value = (!empty($row['field_profile_ban_lists_value'])) ?: 0;

    $roleqry = $con->select('users_roles', 'r')
      ->fields('r')
      ->condition('uid', $uid, '=');
    $roleresults = $roleqry->execute();
    $userroles = [];
    while ($row = $roleresults->fetchAssoc()) {
      $rid = $row['rid'];
      if (isset($roles[$rid])) {
        $account->addRole(get_machine_name($roles[$rid]));
      }
    }
    $account->save();
    $context['sandbox']['current']++;
  }
  $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['max'];
}

/**
 * Function to migrate nodes.
 */
function migrate_nodes(&$context) {
  $context['message'] = t('Migrating Nodes');
  $limit = 10;
  $con = Database::getConnection('default', 'migrate');
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    $allowed_types = [
      'blog',
      'children_and_youth',
      'clergy',
      'clergy_manual',
      'date',
      'international_relations',
      'in_reach',
      'lore',
      'members_page',
      'memorial_hall',
      'publications',
      'public_page',
      'red_hammer_page',
      'shop',
      'steward',
      'troth_kindred_program',
    ];

    $query = $con->select('node', 'n')
      ->fields('n', ['nid'])
      ->condition('n.type', $allowed_types, 'IN')
      ->orderBy('nid');
    $nids = $query->execute()->fetchAllAssoc(0, 0);

    $context['sandbox']['nids'] = $nids;
    $context['sandbox']['max'] = count($nids);
    $context['sandbox']['current'] = 0;
  }

  $nids = $context['sandbox']['nids'];
  $current = $context['sandbox']['current'];
  $nids = array_slice($nids, $current, $limit, TRUE);
  $searchnids = [];
  $curnid = 0;
  foreach ($nids as $key => $data) {
    $searchnids[] = $data['nid'];
  }
  $query = $con->select('node', 'n');
  $query->fields('n', [
    'nid',
    'type',
    'language',
    'status',
    'created',
    'changed',
    'tnid',
    'translate',
  ]);
  $query->condition('n.nid', $searchnids, 'in');
  $query->join('node_revision', 'nrev', 'nrev.nid =n.nid');
  $query->fields('nrev', [
    'uid',
    'vid',
    'title',
    'log',
    'timestamp',
    'status',
    'comment',
    'promote',
    'sticky',
  ]);
  $query->join('field_revision_body', 'nbody', 'nbody.entity_type = :node and nbody.revision_id = nrev.vid and nbody.entity_id =n.nid', [':node' => 'node']);
  $query->fields('nbody', [
    'body_value',
    'body_summary',
    'body_format',
  ]);
  $query->leftjoin('field_revision_field_date', 'ndate', 'ndate.entity_type = :node and ndate.entity_id =n.nid', [':node' => 'node']);
  $query->fields('ndate', [
    'field_date_value',
    'field_date_value2',
  ]);
  $query->leftjoin('field_revision_field_troth_approved', 'napproved', 'napproved.entity_type = :node and napproved.entity_id =n.nid', [':node' => 'node']);
  $query->fields('napproved', ['field_troth_approved_value']);
  $query->leftjoin('field_revision_field_url', 'nurl', 'nurl.entity_type = :node and nurl.entity_id =n.nid', [':node' => 'node']);
  $query->fields('nurl', [
    'field_url_url',
    'field_url_title',
    'field_url_attributes',
  ]);
  $query->leftjoin('field_revision_field_video', 'nvideo', 'nvideo.entity_type = :node and nvideo.entity_id =n.nid', [':node' => 'node']);
  $query->fields('nvideo', ['field_video_video_url']);
  $query->leftjoin('field_revision_field_location', 'nloc', 'nloc.entity_type = :node and nloc.entity_id =n.nid', [':node' => 'node']);
  $query->fields('nloc', [
    'field_location_country',
    'field_location_administrative_area',
    'field_location_locality',
    'field_location_dependent_locality',
    'field_location_postal_code',
    'field_location_thoroughfare',
    'field_location_premise',
    'field_location_organisation_name',
    'field_location_first_name',
    'field_location_last_name',
  ]);

  $query->orderBy('n.nid');
  $query->orderBy('nrev.vid');
  $query->distinct();

  $results = $query->execute();
  $i = 0;
  while ($row = $results->fetchAssoc()) {
    $i++;
    $nid = $row['nid'];
    $node = Node::load($nid);
    if ($node == '') {
      $node = Node::create([
        'nid' => $nid,
        'type' => $row['type'],
        'created' => $row['created'],
        'changed' => $row['timestamp'],
        'uid' => $row['uid'],
      ]);
    }
    else {
      // We need to do a revision.  Set those fields.
      $node->setNewRevision(TRUE);
      $node->setRevisionLogMessage($row['log']);
      $node->setRevisionCreationTime($row['timestamp']);
      $node->setRevisionUserId($row['uid']);
    }
    // Fix some common things.
    if ($row['body_format'] == 'filtered_html') {
      $row['body_format'] = 'basic_html';
    }
    elseif ($row['body_format'] == 'php_code') {
      $row['body_format'] = 'basic_html';
    }
    $node->set('body', [
      'value' => $row['body_value'],
      'summary' => $row['body_summary'],
      'format' => $row['body_format'],
    ]);

    $node->set('title', $row['title']);
    $node->set('status', $row['status']);
    // $node->set('comment',$row['comment']);
    $node->set('promote', $row['promote']);
    $node->set('sticky', $row['sticky']);

    if (isset($row['field_date_value'])) {
      $start = new DrupalDateTime($row['field_date_value']);
      $start = $start->format('Y-m-d\TH:i:s');
      $end = '';
      if (isset($row['field_date_value2'])) {
        $end = new DrupalDateTime($row['field_date_value2']);
        $end = $end->format('Y-m-d\TH:i:s');
      }
      $node->set('field_date', [
        'value' => $start,
        'end_value' => $end,
      ]);
    }
    if (isset($row['field_troth_approved_value'])) {
      $node->set('field_troth_approved', $row['field_troth_approved_value']);
    }
    if (isset($row['field_url_url'])) {
      $node->set('field_url', [
        'uri' => $row['field_url_url'],
        'title' => $row['field_url_title'],
        'options' => [],
      ]);
    }

    if (isset($row['field_location_country'])) {
      $node->set('field_location', [
        'country_code' => $row['field_location_country'],
        'administrative_area' => $row['field_location_administrative_area'],
        'locality' => $row['field_location_locality'],
        'dependent_locality' => $row['field_location_dependent_locality'],
        'postal_code' => $row['field_location_postal_code'],
        'address_line1' => $row['field_location_thoroughfare'],
        'address_line2' => $row['field_location_premise'],
        'organization' => $row['field_location_organisation_name'],
        'given_name' => $row['field_location_first_name'],
        'family_name' => $row['field_location_last_name'],
      ]);
    }
    if (isset($row['field_video_video_url'])) {
      $node->set('field_video', $row['field_video_video_url']);
    }

    // Check if there are tax terms on the page.
    $taxquery = $con->select('field_revision_field_tags', 'ntag');
    $taxquery->leftjoin('taxonomy_term_data', 'term', 'term.tid =ntag.field_tags_tid');
    $taxquery->fields('term', ['name']);
    $taxquery->leftjoin('taxonomy_vocabulary', 'vocab', 'term.vid =vocab.vid');
    $taxquery->fields('vocab', ['machine_name']);
    $taxquery->condition('entity_type', 'node', '=');
    $taxquery->condition('entity_id', $nid, '=');
    $taxresults = $taxquery->execute();
    $tids = [];
    while ($taxrow = $taxresults->fetchAssoc()) {
      $query = \Drupal::entityQuery('taxonomy_term')
        ->condition('vid', $taxrow['machine_name'], '=')
        ->condition('name', $taxrow['name'], '=');
      $result = $query->execute();
      if (count($result) > 0) {
        foreach ($result as $tid) {
          $tids[] = $tid;
        }
      }
      else {
        $term = Term::create([
          'vid' => $taxrow['machine_name'],
          'name' => $taxrow['name'],
        ]);
        $term->save();
        $tids[] = $term->id();
      }
    }

    if (count($tids) > 0) {
      $nodetids = $node->field_tags->getValue();
      $ntids = [];
      foreach ($nodetids as $data) {
        $ntids[] = $data['target_id'];
      }
      foreach ($tids as $tid) {
        if (!in_array($tid, $ntids)) {
          $nodetids[] = ['target_id' => $tid];
        }
      }
      $node->set('field_tags', $nodetids);
    }

    $node->save();
    $pathquery = $con->select('url_alias', 'u');
    $pathquery->fields('u', ['alias']);
    $pathquery->condition('source', 'node/' . $nid, 'like');
    $pathresults = $pathquery->execute();
    while ($row = $pathresults->fetchAssoc()) {
      $path = \Drupal::service('path.alias_storage')->save("/node/" . $nid, '/' . $row['alias'], "en");
    }

    // Clergy Manual is a book, we don't want to do this to books.
    if ($node->getType() != 'clergy_manual' && $node->getType() != 'blog'&& $node->getType() != 'date') {
      $query = \Drupal::entityQuery('menu_link_content')
        ->condition('link__uri', 'entity:node/' . $nid, '=');
      $result = $query->execute();
      $query = $con->select('menu_links', 'l');
      $query->fields('l');
      $query->condition('link_path', 'node/' . $nid, '=');
      $mlresults = $query->execute();
      while ($ml = $mlresults->fetchAssoc()) {
        if (count($result) <= 0) {
          $menu_link = MenuLinkContent::create([
            'title' => $ml['link_title'],
            'link' => ['uri' => 'entity:node/' . $nid],
            'menu_name' => 'main',
            'expanded' => TRUE,
          ]);
          $menu_link->save();
        }
        else {
          foreach ($result as $mlid) {
            $menu_link = MenuLinkContent::load($mlid);
            $menu_link->title = $ml['link_title'];
            $menu_link->save();
          }
        }
      }
    }

    if ($curnid != $nid) {
      $context['sandbox']['current']++;
      $curnid = $nid;
    }
  }
  $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['max'];
}

/**
 * Builds menu hierarchy.
 */
function menu_hierarchy(&$context) {
  $context['message'] = t('Migrating Menu Hierarchy');
  $con = Database::getConnection('default', 'migrate');
  $query = $con->select('menu_links', 'l');
  $query->fields('l');
  $query->condition('link_path', 'node/%', 'like');
  $results = $query->execute();
  $parents = [];
  $nodes = [];
  while ($row = $results->fetchAssoc()) {
    $node = explode('/', $row['link_path']);
    if (is_numeric($node[1])) {
      if ($row['mlid'] != $row['plid']) {
        $parents[$row['mlid']] = ['parent' => $row['plid'], 'node' => $node[1]];
        $nodes[$row['mlid']] = $node[1];
      }
    }
  }
  foreach ($nodes as $mlid => $nid) {
    $query = \Drupal::entityQuery('menu_link_content')
      ->condition('link__uri', 'entity:node/' . $nid, '=');
    $newmlid = $query->execute();
    $newmlid = array_shift($newmlid);

    $pnid = $nodes[$parents[$mlid]['parent']];
    $query = \Drupal::entityQuery('menu_link_content')
      ->condition('link__uri', 'entity:node/' . $pnid, '=');
    $newplid = $query->execute();
    $newplid = array_shift($newplid);

    $menu_link = MenuLinkContent::load($newmlid);
    $parent_link = MenuLinkContent::load($newplid);
    if ($menu_link != '' && $parent_link != '') {
      $menu_link->parent = 'menu_link_content:' . $parent_link->uuid();
      $menu_link->save();
    }
  }
}

/**
 * Migrates Orders.
 */
function migrate_order(&$context) {
  $context['message'] = t('Migrating Orders');
  $limit = 111;
  $con = Database::getConnection('default', 'migrate');
  // Prepare the sandbox.
  if (empty($context['sandbox'])) {
    // Get all product variations.
    $query = \Drupal::entityQuery('commerce_product_variation');
    $varids = $query->execute();
    $variations = [];
    foreach ($varids as $varid) {
      $var = ProductVariation::load($varid);
      $sku = $var->sku->value;
      $type = $var->type->getValue()[0]['target_id'];

      $variations[$sku] = [
        'id' => $varid,
        'type' => $type,
        'var' => $var,
        'title' => $var->title->value,
      ];
    }
    $context['sandbox']['variations'] = $variations;
    $query = $con->select('commerce_order', 'o')
      ->fields('o');
    $count = $query->countQuery()->execute()->fetchField();
    $context['sandbox']['max'] = $count - 1;
    $context['sandbox']['current'] = -1;
  }
  $variations = $context['sandbox']['variations'];
  $current = $context['sandbox']['current'] + 1;
  $query = $con->select('commerce_order', 'o')
    ->fields('o')
    ->orderBy('order_id')
    ->range($current, $limit);
  $results = $query->execute();
  while ($row = $results->fetchAssoc()) {
    $order_id = $row['order_id'];
    $order = Order::load($order_id);
    $uid = $row['uid'];
    $liquery = $con->select('commerce_line_item', 'li')
      ->fields('li')
      ->condition('li.order_id', $order_id, '=');
    $liresults = $liquery->execute();
    $order_items = [];
    while ($lirow = $liresults->fetchAssoc()) {
      $lid = $lirow['line_item_id'];
      $lipquery = $con->select('field_data_commerce_total', 'p')
        ->fields('p', ['commerce_total_amount', 'commerce_total_currency_code'])
        ->condition('entity_id', $lid, '=');
      $liprice = $lipquery->execute()->fetchAssoc();
      $price = new Price($liprice['commerce_total_amount'] / 100, $liprice['commerce_total_currency_code']);
      $sku = $lirow['line_item_label'];
      $qty = $lirow['quantity'];
      if(!$variations[$sku]){
        $context['message'] = t('BAD SKU:@sku:',['@sku'=>$sku]);
      }

      // Check if the order item exists already.
      $query = \Drupal::entityQuery('commerce_order_item')
        ->condition('order_id', $order_id, '=')
        ->condition('purchased_entity', $variations[$sku]['var']->id(), '=')
        ->condition('unit_price__number', $liprice['commerce_total_amount'] / 100, '=')
        ->condition('unit_price__currency_code', $liprice['commerce_total_currency_code'], '=')
        ->condition('quantity', $qty, '=')
        ->condition('created', $lirow['created'], '=');
      $liids = $query->execute();

      if (count($liids) == 0) {
        $order_item = OrderItem::create([
          'type' => $variations[$sku]['type'],
          'purchased_entity' => $variations[$sku]['var'],
          'title' => $variations[$sku]['title'],
          'quantity' => $qty,
          'unit_price' => $price,
          'created' => $lirow['created'],
          'changed' => $lirow['changed'],
          'data' => $lirow['data'],
        ]);
        $order_item->save();
        $order_items[] = $order_item;
      }
    }

    $query = $con->select('field_revision_commerce_customer_billing', 'b')
      ->condition('entity_id', $order_id, '=')
      ->fields('b', ['commerce_customer_billing_profile_id']);
    $profid = $query->distinct()->execute()->fetchField();
    $query = $con->select('field_revision_commerce_customer_address', 'a')
      ->condition('entity_id', $profid, '=')
      ->fields('a');
    $profdata = $query->execute()->fetchAssoc();
    $address = [
      'country_code' => $profdata['commerce_customer_address_country'],
      'postal_code' => $profdata['commerce_customer_address_postal_code'],
      'locality' => $profdata['commerce_customer_address_locality'],
      'address_line1' => $profdata['commerce_customer_address_thoroughfare'],
      'administrative_area' => $profdata['commerce_customer_address_administrative_area'],
      'given_name' => $profdata['commerce_customer_address_first_name'],
      'family_name' => $profdata['commerce_customer_address_last_name'],
      'dependent_locality' => $profdata['commerce_customer_address_dependent_locality'],
      'address_line2' => $profdata['commerce_customer_address_premise'],
      'organization' => $profdata['commerce_customer_address_organisation_name'],
    ];
    if ($order) {
      $profile = Profile::load($order->billing_profile->target_id);
      $profile->setOwnerId($uid);
      $profile->address->country_code = $address['country_code'];
      $profile->address->administrative_area = $address['administrative_area'];
      $profile->address->locality = $address['locality'];
      $profile->address->dependent_locality = $address['dependent_locality'];
      $profile->address->postal_code = $address['postal_code'];
      $profile->address->address_line1 = $address['address_line1'];
      $profile->address->address_line2 = $address['address_line2'];
      $profile->address->organization = $address['organization'];
      $profile->address->given_name = $address['given_name'];
      $profile->address->family_name = $address['family_name'];
    }
    else {

      $profile = Profile::create([
        'type' => 'customer',
        'uid' => $uid,
        'status' => TRUE,
        'address' => $address,
      ]);

    }
    $profile->save();
    if ($order) {
      if (count($order_items) > 0) {
        foreach ($order_items as $item) {
          $order->addItem($item);
        }
      }
      $order->setEmail($row['mail']);
      $order->setIpAddress($row['hostname']);
      $order->state = $row['status'];
      $order->setRefreshState('refresh_on_save');
    }
    else {
      $order = Order::create([
        'order_id' => $order_id,
        'type' => 'default',
        'state' => $row['status'],
        'mail' => $row['mail'],
        'uid' => $uid,
        'ip_address' => $row['hostname'],
        'order_number' => $order_id,
        'billing_profile' => $profile,
        'store_id' => 1,
        'order_items' => $order_items,
        'placed' => $row['created'],
        'created' => $row['created'],
        'data' => $row['data'],
      ]);
    }
    $order->save();

    // Get payments.
    $query = $con->select('commerce_payment_transaction', 'p')
      ->fields('p')
      ->condition('order_id', $order_id, '=');
    $payresult = $query->execute();

    while ($payrow = $payresult->fetchAssoc()) {
      // Check if the payment exists already.
      $query = \Drupal::entityQuery('commerce_payment')
        ->condition('order_id', $order_id, '=')
        ->condition('amount__number', $payrow['amount'] / 100, '=')
        ->condition('amount__currency_code', $payrow['currency_code'], '=')
        ->condition('remote_id', $payrow['remote_id'], '=')
        ->condition('remote_state', $payrow['remote_status'], '=')
        ->condition('completed', $payrow['changed'], '=');
      $pids = $query->execute();

      if (count($pids) == 0) {
        $price = new Price($payrow['amount'] / 100, $payrow['currency_code']);

        $payment = Payment::create([
          'type' => 'paypal_checkout',
          'payment_gateway' => 'paypal_sandbox',
          'order_id' => $order->id(),
          'amount' => $price,
          'state' => 'completed',
          'remote_id' => $payrow['remote_id'],
          'payment_method' => $payrow['payment_method'],
          'remote_state' => $payrow['remote_status'],
          'completed' => $payrow['changed'],

        ]);
        $payment->save();
      }
    }
    $context['sandbox']['current']++;
  }

  $context['finished'] = $context['sandbox']['current'] / $context['sandbox']['max'];

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

/**
 * Function to get roles.
 *
 * @return array
 *   Array of roles.
 */
function get_roles() {
  $con = Database::getConnection('default', 'migrate');
  $query = $con->select('role', 'r')
    ->fields('r', ['rid', 'name']);
  $results = $query->execute();
  $roles = [];
  while ($row = $results->fetchAssoc()) {
    if (stripos($row['name'], 'edit') !== FALSE) {
      if (stripos($row['name'], 'Clergy') !== FALSE ||stripos($row['name'], 'Lore') !== FALSE||stripos($row['name'], 'Steward') !== FALSE||stripos($row['name'], 'Officer') !== FALSE) {
        $roles[$row['rid']] = $row['name'];
      }
    }
    else {
      $roles[$row['rid']] = $row['name'];
    }
  }
  return($roles);
}

/**
 * Generates a machine name from a string.
 *
 * @param string $string
 *   String to clean.
 *
 * @return string
 *   Cleaned string.
 */
function get_machine_name($string) {
  $transliterated = \Drupal::transliteration()->transliterate($string, LanguageInterface::LANGCODE_DEFAULT, '_');
  $transliterated = mb_strtolower($transliterated);

  $transliterated = preg_replace('@[^a-z0-9_.]+@', '_', $transliterated);

  return $transliterated;
}
