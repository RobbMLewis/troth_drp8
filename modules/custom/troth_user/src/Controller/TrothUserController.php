<?php

namespace Drupal\troth_user\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route controllers for elections module.
 */
class TrothUserController extends ControllerBase {

  /**
   * Returns the elections members main page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
    $uid = \Drupal::currentUser()->id();
    $out = "<div class=\"clearfix container-fluid row\">";
    $out .= "<div class=\"col-sm-1 col-md-6\">";
    $out .= "<figure class=\"image\"><a href=\"/news/20160905-180300\"><img alt=\"The Troth &amp; declaration 127\" src=\"/system/files/web/front-page/troth-dec-127.png\" /></a>";
    $out .= "<figcaption>The Troth is an Inclusive Heathen Organization &amp; Proud Signatory of Declaration 127</figcaption>";
    $out .= "</figure></div>";
    $out .= "<div class=\"col-sm-1 col-md-6\">";
    if ($uid >= 10) {
      $out .= "<figure class=\"image\"><a href=\"/renew.html\"><img alt=\"Renew\" src=\"/system/files/web/front-page/renew-troth.png\" /></a>";
      $out .= "<figcaption>Renew Your Troth Membership!</figcaption>";
    }
    else {
      $out .= "<figure class=\"image\"><a href=\"/join-troth.html\"><img alt=\"Join the Troth\" src=\"/system/files/web/front-page/join-troth.png\" /></a>";
      $out .= "<figcaption>Join Our Inclusive Heathen Mission! Join The Troth!</figcaption>";
    }
    $out .= "</figure></div></div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<hr /></div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<p>The Troth is an organization that focuses on the bringing together a variation of the old Norse ways through groups such as Asatru, Theodism, Urglaawe, Irminism, Odinism, Vanatru, and Anglo-Saxon Heathenry. We bring together an all-inclusive atmosphere that welcomes all that have been called to follow the elder ways of Heathenry. Through The Troth, the Gods and Goddesses of Northern Europe, our ancestors, the landvaettir, and the spirits around us, we welcome all people.</p>";
    $out .= "</div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<hr /></div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    if ($uid >= 10) {
      $out .= "<figure class=\"image\"><a href=\"/user\"><img alt=\"Your Profile\" src=\"/system/files/web/front-page/troth-profile.png\" /></a>";
      $out .= "<figcaption>Your Member Profile &amp; Information</figcaption>";
    }
    else {
      $out .= "<figure class=\"image\"><a href=\"/user/login?destination=/index.html\"><img alt=\"Login\" src=\"/system/files/web/front-page/troth-login.png\" /></a>";
      $out .= "<figcaption>Member Login / Access Your Profile and Member Documents</figcaption>";
    }
    $out .= "</figure></div>";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    $out .= "<figure class=\"image\"><a href=\"/news/20160527-203100\"><img alt=\"Our Logo Story\" height=\"195\" src=\"/system/files/web/front-page/troth-logo.png\" /></a>";
    $out .= "<figcaption>Learn The Lessons &amp; Story of our Troth Logo!</figcaption>";
    $out .= "</figure></div>";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    $out .= "<figure class=\"image\"><a href=\"/shop/merchandise.html\"><img alt=\"Troth Shop &amp; Publications\" height=\"195\" src=\"/system/files/web/front-page/troth-shop.png\" /></a>";
    $out .= "<figcaption>Grab Official Troth Merchandise &amp; Publications!</figcaption>";
    $out .= "</figure></div></div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    $out .= "<figure class=\"image\"><a href=\"/contact\"><img alt=\"Contact\" src=\"/system/files/web/front-page/troth-contact.png\" /></a>";
    $out .= "<figcaption>Contact Us! Questions &amp; Comments Welcome!</figcaption>";
    $out .= "</figure></div>";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    $out .= "<figure class=\"image\"><a href=\"/donate.html\"><img alt=\"Donate\" height=\"195\" src=\"/system/files/web/front-page/troth-donate.png\" /></a>";
    $out .= "<figcaption>Help The Troth With Your Donation</figcaption>";
    $out .= "</figure></div>";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    $out .= "<figure class=\"image\"><a href=\"/resources.html\"><img alt=\"Resources\" height=\"195\" src=\"/system/files/web/front-page/troth-resources.png\" /></a>";
    $out .= "<figcaption>Inclusive Heathen Resources &amp; Links&nbsp;</figcaption>";
    $out .= "</figure></div></div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<hr /></div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<p>The Troth takes great pride in being an organization that welcomes all people without judging them.&nbsp;We believe that, no matter what a&nbsp;person's religious or cultural background, ancestral background, physical ability, gender identity, or sexual orientation may be,&nbsp;if the Gods and Goddesses called to them, they are welcome. The Troth follows the principles of our predecessors in the practicing&nbsp;following: Boldness, Truth, Honor, Troth, Self-Rule, Hospitality, Industry, Self-Reliance, Steadfastness, Equality, Strength, Wisdom, Generosity, and Family responsibility.</p>";
    $out .= "</div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<hr /></div>";
    $out .= "<div class=\"clearfix container-fluid row\">";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    $out .= "<figure class=\"image\"><a href=\"/programs-offered.html\"><img alt=\"Troth Programs\" src=\"/system/files/web/front-page/troth-programs.png\" /></a>";
    $out .= "<figcaption>The Troth's programs in Clergy, Lore, and Community.&nbsp;</figcaption>";
    $out .= "</figure></div>";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    $out .= "<figure class=\"image\"><a href=\"/news/index.html\"><img alt=\"Troth Blog &amp; News\" src=\"/system/files/web/front-page/troth-blog.png\" /></a>";
    $out .= "<figcaption>The Troth's Official Blog &amp; News Updates</figcaption>";
    $out .= "</figure></div>";
    $out .= "<div class=\"col-sm-1 col-md-4\">";
    $out .= "<figure class=\"image\"><a href=\"/leadership.html\"><img alt=\"Troth Leadership\" src=\"/system/files/web/front-page/troth-leadership.png\" /></a>";
    $out .= "<figcaption>Discover The Troth Leadership</figcaption>";
    $out .= "</figure></div></div>";

    $output = [];
    $output[] = [
      '#markup' => $out,
    ];
    return $output;
  }

}
