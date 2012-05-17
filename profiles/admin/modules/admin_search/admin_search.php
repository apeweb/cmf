<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Search {
  public static function install () {

  }

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::modifyContent, __CLASS__ . '::buildSearchBlock');
  }

  public static function buildSearchBlock () {
    $searchTextBoxLabel = new Cmf_Label_Control;
    $searchTextBoxLabel->for = 'search_form';
    $searchTextBoxLabel->cssClass = 'print-only';
    $searchTextBoxLabel->text = 'Search:';
    $searchTextBoxLabel->weight = 0;

    $searchTextBox = new Cmf_Text_Box_Control;
    $searchTextBox->name = 'search';
    $searchTextBox->id = 'search_form';
    $searchTextBox->toolTip = 'Search for products, users, etc.';
    $searchTextBox->weight = 1;
    $searchTextBox->cssClass = 'input-text wide title_is_label';

    $searchSubmit = new Cmf_Button_Control;
    $searchSubmit->type = Cmf_Button_Control_Type::image;
    $searchSubmit->name = 'action';
    $searchSubmit->value = 'Search';
    $searchSubmit->toolTip = 'Search';
    $searchSubmit->alternativeText = 'Search';
    $searchSubmit->imageUrl = '/themes/admin/images/search.gif';
    $searchSubmit->weight = 2;

    $searchBox = new Cmf_Block_Control;
    $searchBox->children['label'] = $searchTextBoxLabel;
    $searchBox->children['textbox'] = $searchTextBox;
    $searchBox->children['submit_button'] = $searchSubmit;
    $searchBox->renderCallback = __CLASS__ . '::renderSearchBox';

    View_Data::setValue('navigation_search', $searchBox);
  }

  public static function renderSearchBox (Cmf_Block_Control $control) {
    $control->setContent('
      <table class="search_box" style="width:auto">
        <tbody>
          <tr>
            <td valign="bottom">
              ' . $control->children['label'] . ' ' . $control->children['textbox'] . ' ' . $control->children['submit_button'] . '
            </td>
          </tr>
        </tbody>
      </table>
    ');
  }
}

?>