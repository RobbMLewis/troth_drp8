<?php

namespace Drupal\troth_google\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;
use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;

/**
 * Defines the troth_google entity.
 *
 * @ContentEntityType(
 *   id = "troth_google",
 *   label = @Translation("Google Group Subscriptions"),
 *   base_table = "troth_google",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "bundle",
 *     "uid" = "uid",
 *     "email" = "email",
 *     "subscribed" = "subscribed",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   fieldable = FALSE,
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\troth_google\TrothGoogleGroupListBuilder",
 *     "access" = "Drupal\troth_google\TrothGoogleEntityAccessControlHandler",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\troth_google\Form\TrothGoogleGroupEntityForm",
 *       "add" = "Drupal\troth_google\Form\TrothGoogleGroupEntityForm",
 *       "edit" = "Drupal\troth_google\Form\TrothGoogleGroupEntityForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   links = {
 *     "canonical" = "/admin/config/troth/google/users/{troth_google}",
 *     "add-page" = "/admin/config/troth/google/users/add",
 *     "add-form" = "/admin/config/troth/google/users/add/{troth_google_type}",
 *     "edit-form" = "/admin/config/troth/google/users/{troth_google}/edit",
 *     "collection" = "/admin/config/troth/google/users",
 *   },
 *   admin_permission = "administer site configuration",
 *   bundle_entity_type = "troth_google_type",
 *   field_ui_base_route = "entity.troth_google_type.edit_form",
 *   constraints = {
 *     "TrothGoogleGroupUnique" = {}
 *   }
 * )
 */
class TrothGoogleGroup extends ContentEntityBase implements TrothGoogleGroupEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getEmail() {
    return $this->get('email')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail($email) {
    $this->set('email', $email);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscribed() {
    return $this->get('subscribed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSubscribed($subscribed) {
    $this->set('subscribed', $subscribed);
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
   * Determines the schema for the base_table property defined above.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Owner'))
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
      ->setDescription(t('The user ID of user.'));

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDisplayOptions('form', [
        'type' => 'email',
      ])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'string',
        'weight' => 2,
      ])
      ->setDescription(t('Email Address of the user that was used.'));

    $fields['subscribed'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Subscribed to the group'))
      ->setDisplayOptions('form', [
        'type' => 'checkbox',
      ])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDescription(t('Is the user subscribed to the group.'));

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

  /**
   * Creates a Google Client.
   */
  public function trothGoogleClient() {
    $reddirectUrl = Url::fromRoute('troth_google.admin_settings_form');
    $reddirectUrl->setOptions(['absolute' => TRUE, 'https' => TRUE]);

    // We need the client_secret.json.  Check if it exists.
    // if it doesn't exist, create it from the config variable.
    $client_secret = "private://oauth/client_secret.json";
    if (!file_exists($client_secret)) {
      // File does not exist, create it.
      $client_secret_json = \Drupal::config('troth_google.adminsettings')->get('oauth_client_secret');
      $dir = "private://oauth/";
      file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
      $path = $dir . "client_secret.json";
      $file = file_save_data($client_secret_json, $path, FILE_EXISTS_REPLACE);
      if (!is_object($file)) {
        \Drupal::logger('troth_gogle')->error('Client_secret.json could not be saved to @path', ['@path' => $path]);
        return;
      }
    }
    $client = new \Google_Client();
    $client->setApplicationName("Google Group");
    $client->setScopes([
      'https://apps-apis.google.com/a/feeds/groups/',
      'https://www.googleapis.com/auth/admin.directory.group',
      'https://www.googleapis.com/auth/admin.directory.group.member',
    ]);
    $client->setScopes(['https://apps-apis.google.com/a/feeds/groups/']);
    $client->setRedirectUri($reddirectUrl->toString());
    $client->setAccessType('offline');
    $client_secret_path = \Drupal::service('file_system')->realpath($client_secret);
    $client->setAuthConfig($client_secret_path);

    // Do we have an access token?
    if (!$access_token = \Drupal::state()->get('troth_google_access_token')) {
      \Drupal::logger('troth_gogle')->error('The site needs to be re-authorized: @url', ['@url' => $reddirectUrl->toString()]);
    }
    // Authenticate with access token.
    else {
      $client->setAccessToken($access_token);
      if ($client->isAccessTokenExpired()) {
        // If expired, use refresh token.
        if ($refresh_token = \Drupal::state()->get('troth_google_refresh_token')) {
          \Drupal::logger('troth_gogle')->notice('Access token expired, using refresh token.');
          $client->refreshToken($refresh_token);
          // Check and see if that worked...
          if (!$access_token = $client->getAccessToken()) {
            \Drupal::logger('troth_gogle')->error('Unable to authenticate.');
          }
        }
      }
    }
    return $client;
  }

  /**
   * Creates a Google Member.
   */
  public function trothGoogleMember($email = NULL) {
    if ($email == NULL) {
      $email = $this->getEmail();
    }
    $member = new \Google_Service_Directory_Member();
    $member->email = $email;
    $member->role = 'MEMBER';
    return $member;
  }

  /**
   * Subscribes a user to a list.
   */
  public function trothGoogleSubscribe($email = NULL) {
    $bundle = $this->bundle();
    $enttype = TrothGoogleGroupType::load($this->bundle());
    $group_id = $enttype->getGroupId();
    $domain = \Drupal::config('troth_google.adminsettings')->get('domain_name');
    $groupEmail = $group_id . "@" . $domain;
    $member = $this->trothGoogleMember($email);
    $client = $this->trothGoogleClient();

    // Add member to the group.
    try {
      $group = new \Google_Service_Directory($client);
      try {
        $group->members->insert($groupEmail, $member);
      }
      catch (\Google_Service_Exception $e) {
        $errors = $e->getErrors();
        $reason = $errors[0]['reason'];
        $message = $errors[0]['message'];
        if (strtolower($reason) == 'duplicate') {
          \Drupal::logger('troth_gogle')->error('Email address @email already subscribed: @error', [
            '@email' => $this->getEmail(),
            '@error' => $message,
          ]);
        }
        else {
          throw new \Exception(Json::encode($errors));
        }
      }

      \Drupal::logger('troth_gogle')->notice('Email added to @group: @email', [
        '@group' => $groupEmail,
        '@email' => $this->getEmail(),
      ]);
      // The access token may have been updated lazily, save it just in case.
      $token_array = $client->getAccessToken();
      if ($token_array != '') {
        \Drupal::state()->set('troth_google_access_token', $token_array['access_token']);
        \Drupal::state()->set('troth_google_refresh_token', $token_array['refresh_token']);
      }
      return;
    }
    catch (Exception $e) {
      \Drupal::logger('troth_gogle')->error('An error occured: @error  List:@list  UID: @uid', [
        '@error' => $e->getMessage(),
        '@list' => $group_email,
        '@uid' => $uid,
      ]);
    }
  }

  /**
   * Unubscribes a user to a list.
   */
  public function trothGoogleUnsubscribe($email = NULL) {
    $bundle = $this->bundle();
    $enttype = TrothGoogleGroupType::load($this->bundle());
    $group_id = $enttype->getGroupId();
    $domain = \Drupal::config('troth_google.adminsettings')->get('domain_name');
    $groupEmail = $group_id . "@" . $domain;
    if ($email == NULL) {
      $email = $this->getEmail();
    }
    $client = $this->trothGoogleClient();

    // Delete member to the group.
    try {
      $group = new \Google_Service_Directory($client);
      try {
        $group->members->delete($groupEmail, $email);
      }
      catch (\Google_Service_Exception $e) {
        $errors = $e->getErrors();
        $reason = $errors[0]['reason'];
        $message = $errors[0]['message'];
        if (strtolower($reason) == 'notfound') {
          \Drupal::logger('troth_gogle')->error('Email address @email not subscribed: @error', [
            '@email' => $this->getEmail(),
            '@error' => $message,
          ]);
        }
        else {
          throw new \Exception(Json::encode($errors));
        }
      }
      \Drupal::logger('troth_gogle')->notice('Email removed from @group: @email', [
        '@group' => $groupEmail,
        '@email' => $email,
      ]);
      // The access token may have been updated lazily, save it just in case.
      $token_array = $client->getAccessToken();
      if ($token_array != '') {
        \Drupal::state()->set('troth_google_access_token', $token_array['access_token']);
        \Drupal::state()->set('troth_google_refresh_token', $token_array['refresh_token']);
      }
      return;
    }
    catch (Exception $e) {
      \Drupal::logger('troth_gogle')->error('An error occured: @error  List:@list  UID: @uid', [
        '@error' => $e->getMessage(),
        '@list' => $group_email,
        '@uid' => $uid,
      ]);
    }
  }

}
