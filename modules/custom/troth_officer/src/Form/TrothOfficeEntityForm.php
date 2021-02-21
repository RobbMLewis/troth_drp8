<?php

namespace Drupal\troth_officer\Form;

use Drupal\Core\Url;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\user\Entity\Role;
use Drupal\contact\Entity\ContactForm;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\troth_officer\Entity\TrothOffice;

/**
 * Form for the TrothOfficerType entity.
 */
class TrothOfficeEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $admin = 0;
    if ($user->hasPermission('access administration pages')) {
      $admin = 1;
    }

    $groups = troth_officer_office_groups();
    if (count($groups) == 0) {
      $reddirectUrl = Url::fromRoute('troth_officer.admin_settings_form');
      $reddirectUrl->setOptions(['absolute' => TRUE, 'https' => TRUE]);
      drupal_set_message($this->t('There are no office groups available.  <a href=":url">Create one first.</a>', [':url' => $reddirectUrl->toString()]), 'error');
    }

    $form['office_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Office Name'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->getName() ?: $this->entity->getName(),
      '#description' => $this->t("The name of the office."),
      '#required' => TRUE,
    ];

    $form['office_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Office Group'),
      '#options' => $groups,
      '#default_value' => $this->entity->getOfficeType() ?: $this->entity->getOfficeType(),
      '#description' => $this->t("The group of offices this office belongs to, eg BOD for the board of directors."),
      '#required' => TRUE,
    ];

    if (count($groups) == 1) {
      $form['office_type']['#disabled'] = TRUE;
      $form['office_type']['#description'] = $this->t("The group of offices this office belongs to, eg BOD for the board of directors.  If this is not the right grouping, create a new one.");
      if (!$this->entity->getOfficeType()) {
        $form['office_type']['#default_value'] = array_key_first($groups);
      }
    }
    $form['office_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#default_value' => $this->entity->getEmail() ?: $this->entity->getEmail(),
      '#description' => $this->t('The email address for this office.  This may be left blank.'),
      '#required' => FALSE,
    ];

    $form['office_description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#default_value' => $this->entity->getDescription() ? $this->entity->getDescription()['value'] : "",
      '#format' => $this->entity->getDescription() ? $this->entity->getDescription()['format'] : 'basic_html',
      '#description' => $this->t('Please enter the description of this office.  This should be from the bylaws if possible as it will be used on both the officer page and elections (if enabled).'),
      '#required' => TRUE,
    ];

    $form['office_term'] = [
      '#type' => 'number',
      '#title' => $this->t('Term Length'),
      '#step' => 1,
      '#min' => 0,
      '#max' => 3,
      '#default_value' => $this->entity->getTerm() ?: 2,
      '#description' => $this->t('How many years is the term for this office?'),
      '#required' => FALSE,
    ];

    $roles = $this->entity->getRoles();
    if ($roles == '') {
      $roles = [];
    }

    $form['office_roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Roles to Apply'),
      '#default_value' => $roles,
      '#description' => $this->t('The roles that automatically get applied to this office.'),
      '#required' => FALSE,
    ];
    foreach (Role::loadMultiple() as $role) {
      /** @var \Drupal\user\RoleInterface $role */
      if ($role->id() !== Role::ANONYMOUS_ID && $role->id() !== Role::AUTHENTICATED_ID) {
        $form['office_roles']['#options'][$role->id()] = $role->label();
      }
    }
    asort($form['office_roles']['#options']);

    if (\Drupal::moduleHandler()->moduleExists('troth_elections')) {
      $form['office_open'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Office Open'),
        '#default_value' => $this->entity->getOpen() ?: $this->entity->getOpen(),
        '#description' => $this->t('If checked, this office will show up in the elections.'),
        '#required' => FALSE,
      ];

      $form['office_number_open'] = [
        '#type' => 'number',
        '#title' => $this->t('Number Open'),
        '#step' => 1,
        '#min' => 0,
        '#max' => 10,
        '#default_value' => $this->entity->getNumOpen() ?: 0,
        '#description' => $this->t('Enter the number of positions currently open.  This will be used for elections.'),
        '#required' => FALSE,
        '#states' => [
          'invisible' => [
            ':input[name="office_open"]' => ['checked' => FALSE],
          ],
          'visible' => [
            ':input[name="office_open"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    // hide/prevent editing of fields if it's the EO.
    if ($admin == 0) {
      $form['label']['#attributes'] = ['readonly' => 'readonly'];
      $form['label']['#disabled'] = TRUE;
      $form['id']['#type'] = 'hidden';
      $form['office_id']['#type'] = 'hidden';
      $form['office_type']['#type'] = 'hidden';
      $form['office_email']['#type'] = 'hidden';
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    // Enter custom validation here.
    $entid = $this->entity->id();

    // We need to confirm name/group is unique.
    $name = $this->getMachineName($form_state->getValue('office_name'));
    $type = $form_state->getValue('office_type');
    $query = \Drupal::entityQuery('troth_office')
      ->condition('office_type', $type, '=')
      ->condition('id', $entid, '!=');
    $officeids = $query->execute();

    if (count($officeids) > 0) {
      foreach ($officeids as $id) {
        $office = TrothOffice::load($id);
        $officename = $this->getMachineName($office->getName());
        if ($officename == $name) {
          $form_state->setErrorByName('office_name', t('The office name ":name" is already in use.', [':name' => $form_state->getValue('office_name')]));
        }
      }
    }

    // We need to confirm email addrss is unique.
    $email = $form_state->getValue('office_email');
    $query = \Drupal::entityQuery('troth_office')
      ->condition('office_email', $email, 'like')
      ->condition('id', $entid, '!=');
    $count = $query->count()->execute();
    if ($count > 0 && $email != '') {
      $form_state->setErrorByName('office_email', t('The office email ":email" is already in use.', [':email' => $email]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;
    $office_name = $form_state->getValue('office_name');
    $entity->setName($office_name);
    $office_type = $form_state->getValue('office_type');
    $entity->setOfficeType($office_type);
    $office_email = $form_state->getValue('office_email') ?: '';
    $entity->setEmail($office_email);
    $office_description = $form_state->getValue('office_description');
    $entity->setDescription($office_description);
    $office_term = $form_state->getValue('office_term');
    $entity->setTerm($office_term);
    $office_roles = $form_state->getValue('office_roles');
    $entity->setRoles($office_roles);
    $entity->setOpen(0);
    $entity->setNumOpen(0);
    if (\Drupal::moduleHandler()->moduleExists('troth_elections')) {
      $office_open = $form_state->getValue('office_open');
      $office_number_open = $form_state->getValue('office_number_open');
      $entity->setOpen($office_open);
      $entity->setNumOpen($office_number_open);
    }
    $status = $entity->save();

    $email = $entity->getEmail();
    $officeName = $entity->getName();
    $officeType = $entity->getOfficeType();
    $officeMN = $this->getMachineName($officeName);

    $contact_form = \Drupal::entityTypeManager()
      ->getStorage('contact_form')
      ->load($officeMN);

    if ($email == NULL || $email == '') {
      if ($contact_form != '') {
        $contact_form->delete();
      }
    }
    elseif ($contact_form == NULL || $contact_form == '') {
      $contact_form = ContactForm::create([
        'id' => $officeMN,
        'label' => $officeName,
        'recipients' => [$email],
        'message' => $this->t('Your email has been sent.'),
        'redirect' => '',
      ]);
      $contact_form->save();
    }
    else {
      $contact_form->setRecipients([$email]);
      $contact_form->save();
    }

    // Create the menu link.
    // Check if group has a link.
    $groups = troth_officer_office_groups();
    $path = 'internal:/about/leadership/' . $groups[$entity->getOfficeType()] . '.html';
    $query = \Drupal::entityQuery('menu_link_content')
      ->condition('link.uri', $path)
      ->condition('menu_name', 'main');

    $result = $query->execute();
    $menu_link_id = (!empty($result)) ? reset($result) : FALSE;

    if ($menu_link_id === FALSE) {
      $parentpath = 'internal:/about/leadership.html';
      $query = \Drupal::entityQuery('menu_link_content')
        ->condition('link.uri', $parentpath)
        ->condition('menu_name', 'main');

      $result = $query->execute();
      $menu_link_id = (!empty($result)) ? reset($result) : FALSE;
      if (!$menu_link_id) {
        // We need to check by alias.
        $parentpath = '/about/leadership.html';
        $leaderpath = \Drupal::service('path.alias_manager')->getPathByAlias($parentpath);
        $leaderpath = Url::fromUri("internal:" . $leaderpath)->getRouteParameters();
        $parentpath = 'entity:node/' . $leaderpath['node'];
        $query = \Drupal::entityQuery('menu_link_content')
          ->condition('link.uri', $parentpath)
          ->condition('menu_name', 'main');

        $result = $query->execute();
        $menu_link_id = (!empty($result)) ? reset($result) : FALSE;
      }
      $parent = MenuLinkContent::load($menu_link_id);
      $menu_link = MenuLinkContent::create([
        'title' => troth_officer_office_groups_name($entity->getOfficeType()),
        'link' => ['uri' => $path],
        'menu_name' => 'main',
        'expanded' => TRUE,
        'weight' => 0,
      ]);
      if ($parent != NULL) {
        $menu_link->parent = 'menu_link_content:' . $parent->uuid();
      }
      $menu_link->save();
    }

    // We have the group url created.  Lets make the office url.
    $path = 'internal:/about/leadership/' . $groups[$entity->getOfficeType()] . '/' . $officeMN . '.html';
    $query = \Drupal::entityQuery('menu_link_content')
      ->condition('link.uri', $path)
      ->condition('menu_name', 'main');

    $result = $query->execute();
    $menu_link_id = (!empty($result)) ? reset($result) : FALSE;
    if (!$menu_link_id) {
      $parentpath = 'internal:/about/leadership/' . $groups[$entity->getOfficeType()] . '.html';
      $query = \Drupal::entityQuery('menu_link_content')
        ->condition('link.uri', $parentpath)
        ->condition('menu_name', 'main');

      $result = $query->execute();
      $menu_link_id = (!empty($result)) ? reset($result) : FALSE;
      $parent = MenuLinkContent::load($menu_link_id);
      $menu_link = MenuLinkContent::create([
        'title' => $entity->getName(),
        'link' => ['uri' => $path],
        'menu_name' => 'main',
        'expanded' => TRUE,
        'weight' => 0,
      ]);
      if ($parent != NULL) {
        $menu_link->parent = 'menu_link_content:' . $parent->uuid();
      }
      $menu_link->save();
    }

    $message_params = [
      '%name' => $entity->getName(),
      '%officer_entity_id' => $entity->getEntityType()->getBundleOf(),
    ];

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %name %officer_entity_id entity type.', $message_params));
        break;

      default:
        drupal_set_message($this->t('Saved the %name %officer_entity_id entity type.', $message_params));
    }

    $form_state->setRedirectUrl($entity->toUrl('collection'));
  }

  /**
   * {@inheritdoc}
   */
  private function getMachineName($string) {
    $transliterated = \Drupal::transliteration()->transliterate($string, LanguageInterface::LANGCODE_DEFAULT, '_');
    $transliterated = mb_strtolower($transliterated);

    $transliterated = preg_replace('@[^a-z0-9_.]+@', '_', $transliterated);

    return $transliterated;
  }

}
