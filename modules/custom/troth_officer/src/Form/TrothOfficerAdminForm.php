<?php

namespace Drupal\troth_officer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\troth_officer\Entity\TrothOfficerType;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\Core\Url;

/**
 * Edit Troth Officer Admin form.
 */
class TrothOfficerAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'troth_officer.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'troth_officer_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the config data.
    $config = $this->config('troth_officer.adminsettings');
    $groups = unserialize($config->get('groups'));

    $form['#tree'] = TRUE;
    $ids = [];
    foreach ($groups as $id => $group) {
      $ids[] = $id;
      $form[$id] = [
        '#type' => 'details',
        '#title' => $group['name'],
        '#open' => FALSE,
      ];
      $form[$id]['name'] = [
        '#title' => t("Name of Grouping"),
        '#description' => t('The name human readable of the grouping of officers, eg Board of Directors'),
        '#type' => 'textfield',
        '#default_value' => $group['name'],
        '#required' => TRUE,
      ];
      $form[$id]['shortname'] = [
        '#title' => t("Short/Machine Name of Grouping"),
        '#description' => t('The short or machine name of the grouping of officers, eg bod'),
        '#type' => 'textfield',
        '#default_value' => $group['shortname'],
        '#required' => TRUE,
      ];
      $form[$id]['description'] = [
        '#title' => t('Description'),
        '#description' => t('Description of the group of oficers.  This will show up at the top of a web page.'),
        '#type' => 'text_format',
        '#rows' => 15,
        '#default_value' => $group['description']['value'],
        '#format' => $group['description']['format'],
        '#required' => TRUE,
      ];
      $form[$id]['archive'] = [
        '#title' => t("Archive Grouping"),
        '#description' => t('Archive the grouping.  Historical data will be preseved and displayed as such.'),
        '#type' => 'checkbox',
        '#default_value' => 0,
        '#required' => FALSE,
      ];
      if (isset($group['archive'])) {
        $form[$id]['archive']['#default_value'] = $group['archive'];
      }

      $form[$id]['delete'] = [
        '#title' => t("Delete Grouping"),
        '#description' => t('Delete the grouping.  This will only be an option if there are no offices under this grouping.'),
        '#type' => 'checkbox',
        '#default_value' => 0,
        '#required' => FALSE,
      ];
    }

    $form['new'] = [
      '#type' => 'details',
      '#title' => t('New Grouping'),
      '#open' => TRUE,
    ];
    $form['new']['name'] = [
      '#title' => t("Name of Grouping"),
      '#description' => t('The name human readable of the grouping of officers, eg Board of Directors'),
      '#type' => 'textfield',
      '#required' => FALSE,
    ];
    $form['new']['shortname'] = [
      '#title' => t("Short/Machine Name of Grouping"),
      '#description' => t('The short or machine name of the grouping of officers, eg bod'),
      '#type' => 'textfield',
      '#required' => FALSE,
    ];
    $form['new']['description'] = [
      '#title' => t('Description'),
      '#description' => t('Description of the group of oficers.  This will show up at the top of a web page.'),
      '#type' => 'text_format',
      '#rows' => 15,
      '#required' => FALSE,
    ];
    asort($ids);
    $storage['ids'] = $ids;
    $form_state->setStorage($storage);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Enter in any validation functions here.
    // Get all short names and confirm they are unique.
    $storage = $form_state->getStorage();
    $ids = $storage['ids'];
    $short = [];
    foreach ($ids as $id) {
      $group = $form_state->getValue($id);
      if ($group['delete'] == 1) {
        // We need to prevent deletion if there are entities associated with it.
        $bundles = \Drupal::service('entity_type.bundle.info')->getBundleInfo('troth_officer');
        foreach ($bundles as $bundle => $data) {
          $type = TrothOfficerType::load($bundle);
          if ($type->getOfficeType() == $id) {
            $form_state->setErrorByName("$id][delete", $this->t('There are offices attached to this group.  We cannot delete the group unless there are no offices and officers in it.'));
          }
        }
      }
      if (!in_array($group['shortname'], $short)) {
        $short[$id] = $group['shortname'];
      }
      else {
        $form_state->setErrorByName("$id][shortname", $this->t('Short Name is not Unique'));
        $dupid = array_search($group['shortname'], $short);
        $form_state->setErrorByName("$dupid][shortname", $this->t('Short Name is not Unique'));
      }
    }
    $new = $form_state->getValue('new');
    if (in_array($new['shortname'], $short)) {
      $form_state->setErrorByName("new][shortname", $this->t('Short Name is not Unique'));
      $dupid = array_search($new['shortname'], $short);
      $form_state->setErrorByName("$dupid][shortname", $this->t('Short Name is not Unique'));
    }
    if ($new['name'] != '' || $new['shortname'] != '' || $new['description']['value'] != '') {
      // We have a value, all 3 will be required.
      if ($new['name'] == '') {
        $form_state->setErrorByName("new][name", $this->t('Name is not set.  All fields under New are requrired for a new grouping.'));
      }
      if ($new['shortname'] == '') {
        $form_state->setErrorByName("new][shortname", $this->t('Short Name is not set.  All fields under New are requrired for a new grouping.'));
      }
      if ($new['description'] == '') {
        $form_state->setErrorByName("new][description", $this->t('Description is not set.  All fields under New are requrired for a new grouping.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $storage = $form_state->getStorage();
    $ids = $storage['ids'];
    $groups = [];
    $i = 0;
    foreach ($ids as $id) {
      $group = $form_state->getValue($id);
      if ($group['delete'] == 1) {
        // We need to delete the menu link.
        $path = 'internal:/about/leadership' . $group['shortname'];
        $query = \Drupal::entityQuery('menu_link_content')
          ->condition('link.uri', $path)
          ->condition('menu_name', 'main');

        $result = $query->execute();
        $menu_link_id = (!empty($result)) ? reset($result) : FALSE;
        if ($menu_link_id) {
          $link = MenuLinkContent::load($menu_link_id);
          $link->delete();
        }

        $name = $group['name'];
        \Drupal::messenger()->addMessage(t('@name has been deleted.', ['@name' => $name]), 'notice');
      }
      else {
        $groups[$id] = $group;
        if ($id > $i) {
          $i = $id;
        }
      }
    }
    $new = $form_state->getValue('new');
    if ($new['name'] != '') {
      $id = $i + 1;
      $groups[$id] = $new;
    }

    // Save the config values.
    $this->config('troth_officer.adminsettings')->set('groups', serialize($groups))->save();

    // Now we need to create the paths.
    foreach ($groups as $id => $data) {
      $path = 'internal:/about/leadership/' . $data['shortname'] . '.html';
      $query = \Drupal::entityQuery('menu_link_content')
        ->condition('link.uri', $path)
        ->condition('menu_name', 'main');

      $result = $query->execute();
      $menu_link_id = (!empty($result)) ? reset($result) : FALSE;
      if (!$menu_link_id) {
        $query = \Drupal::entityQuery('menu_link_content')
          ->condition('link.uri', 'internal:/about/leadership.html')
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
          'title' => $data['name'],
          'link' => ['uri' => $path],
          'menu_name' => 'main',
          'parent' => 'menu_link_content:' . $parent->uuid(),
          'expanded' => TRUE,
          'weight' => 0,
        ]);
        $menu_link->save();
      }
    }
  }

}
