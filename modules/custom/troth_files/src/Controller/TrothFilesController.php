<?php

namespace Drupal\troth_files\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route controllers for files module.
 */
class TrothFilesController extends ControllerBase {

  /**
   * Returns the the page main page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function displayPage($uri = NULL, $header = NULL) {
    $account = user_load(\Drupal::currentUser()->id());
    $files = $this->getFileList($uri, 1);
    $files = $this->cleanFileArr($uri, $files);
    if (count($files[$uri]) == 0 && $account->hasRole('administrator')) {
      $output[] = [
        '#markup' => $this->t('<p>Either there are no files or this directory has not been set up.  Go to <a href=":link">Private files download permission</a> to set it up.</p>',
          [':link' => "/admin/config/media/private-files-download-permission"]
        ),
      ];
    }
    else {
      $output[] = [
        '#markup' => $this->t('<p>@header</p>', [
          '@header' => $header,
        ]),
      ];
      $output[] = $this->renderArray($files);
    }
    return $output;
  }

  /**
   * Returns a file listing array.
   *
   * @return array
   *   An array of file information.
   */
  private function getFileList($dir, $recurse = FALSE) {
    $retval = [];
    // Add trailing slash if missing.
    if (substr($dir, -1) != "/") {
      $dir .= "/";
    }

    // Open pointer to directory and read list of files.
    $d = @dir($dir) or die("getFileList: Failed opening directory {$dir} for reading");
    while (($entry = $d->read()) !== FALSE) {

      // Skip hidden files.
      if ($entry[0] == ".") {
        continue;
      }

      if (is_dir("{$dir}{$entry}")&& troth_files_has_permission("{$dir}{$entry}/")) {
        $retval[] = [
          'uri' => "{$dir}{$entry}/",
          'type' => filetype("{$dir}{$entry}"),
          'size' => 0,
          'lastmod' => filemtime("{$dir}{$entry}"),
        ];
        if ($recurse && is_readable("{$dir}{$entry}/")&& troth_files_has_permission("{$dir}{$entry}/")) {
          $retval = array_merge($retval, $this->getFileList("{$dir}{$entry}", TRUE));
        }
      }
      elseif (is_readable("{$dir}{$entry}")&& troth_files_has_permission("{$dir}{$entry}/")) {
        $retval[] = [
          'uri' => "{$dir}{$entry}",
          'type' => mime_content_type("{$dir}{$entry}"),
          'size' => filesize("{$dir}{$entry}"),
          'lastmod' => filemtime("{$dir}{$entry}"),
        ];
      }
    }
    $d->close();

    return $retval;
  }

  /**
   * Cleans up a file listing array.
   *
   * @return array
   *   An array of file information.
   */
  private function cleanFileArr($uri = NULL, $fileArr = []) {
    // Remove trailing slash if present.
    if (substr($uri, -1) == "/") {
      $uri = substr($uri, 0, -1);
    }
    // Reorder the array for help displaying.
    $retarr[$uri] = [];
    foreach ($fileArr as $entry) {
      if ($entry['type'] == 'dir') {
        if (!isset($retarr[$entry['uri']])) {
          $retarr[dirname($entry['uri'])] = [];
        }
      }
      else {
        $dir = dirname($entry['uri']);
        $file = basename($entry['uri']);
        $retarr[$dir][$file] = $entry;
      }
    }
    return $retarr;
  }

  /**
   * Returns the renderable array of files.
   *
   * @return array
   *   A simple renderable array.
   */
  private function renderArray($files) {
    $out = '<ul id="myUL">';

    ksort($files);
    $count = 0;
    foreach ($files as $dir => $data) {
      $count++;
      $name = basename($dir);
      if ($count == 1) {
        // Very first item, we will have this expanded by default.
        $out .= $this->t('<li><span class="dcaret">@name</span><ul class="active">', ['@name' => $name]);
      }
      else {
        // Subsiquent directories, collapsed by default.
        $out .= $this->t('<li><span class="caret">@name</span><ul class="nested">', ['@name' => $name]);
      }
      ksort($data);
      foreach ($data as $name => $file) {
        // Files to display.
        $uri = $file['uri'];
        $url = file_create_url($uri);
        $out .= $this->t('<li><a href=":url">@name</a></li>', [
          ':url' => $url,
          '@name' => $name,
        ]);
      }
    }
    for ($i = 1; $i <= $count; $i++) {
      $out .= '</ul></li>';
    }
    $out .= '</ul>';

    $output[] = [
      '#markup' => $out,
      '#attached' => [
        'library' => [
          'troth_files/troth_files.collapse',
        ],
      ],
    ];
    return $output;
  }

}
