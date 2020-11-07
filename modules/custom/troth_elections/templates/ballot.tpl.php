<?php

/**
 * @file
 * Ballot Form.
 */
  if(!isset($form['voted'])){
    $year = $form['year']['#value'];
    $debug = $form['debug']['#value'];
    $positions = $form['positions']['#value'];
    $position_desc = $form['position_desc']['#value'];

    if(isset($form['warn'])){
      print render($form['warn']);
    }
    print "<H2>$year Troth Elections</H2>";
    print "<hr>";

    foreach($positions as $id => $title){
      print "<H3>$title</H3>";
      print "<p><b>Term length:</b> " . $position_desc[$id]['tlen'] . " years<br />";
      if($position_desc[$id]['tlim'] > 0){
        print "<b>Term limits:</b> " . $position_desc[$id]['tlim'] . "<br />";
      }
      print "<b>Number Open:</b> " . $position_desc[$id]['num_open'] . "<br/>";
      print "<b>Office Description:</b>" . $position_desc[$id]['office_description'];
      print "</p>";
      print "<H4>Candidates</H4>";
      if(isset($form['bios']['#value'][$id])){
        foreach($form['bios']['#value'][$id] as $can => $bio){
          print $bio;
        }
      }
      print render($form['votes_' . $id]);
      if(isset($form['votes_' . $id . '_writein'])){
        print render($form['votes_' . $id . '_writein']);
      }
      print "<hr>";
    }
  }
  if(isset($form['proposition'])){
    print "<H3>Propositions</H3>";
    print "<p>The Rede requests that you vote on the following proposition(s).  If you do not vote here, you may vote in person at Trothmoot.";
    print render($form['proposition']);
    print "<hr>";
  }
  // Always finsh with this to make sure that all required fields are rendered.
  print drupal_render_children($form);
?>
