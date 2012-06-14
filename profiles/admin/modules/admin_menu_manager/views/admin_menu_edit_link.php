<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>

<form action="" method="post">
  <div id="main_content">
    <div class="columns">
      <div class="left single_column clearfix">
        <h2>Manage Menu Link</h2>
        <ul class="product_tabs">
          <li class="active"><a href="#settings"><span>Settings</span></a></li>
        </ul>
      </div>
      <div class="left flexi_column clearfix">
        <div class="intro">
          <table>
            <tbody>
              <tr>
                <td style="width:50%;">
                  <h1 class="package"><?=View_Data::getValue('page', 'title');?></h1>
                </td>
                <td align="right">
                  <a href="/admin/menus/" class="form-button back"><span>Back</span></a>
                  <span class="form-button save">
                    <input class="wrapped-button first last" type="submit" value="Save" name="action">
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="panel active" id="settings">
          <h3>Settings</h3>
          <p>Use the settings to configure basic information about the menu.</p>
          <div class="form-item">
            <label for="link_name">Name <span class="required">*</span></label><br />
            <input id="link_name" class="fluid input-text required" type="text" maxlength="255" value="<?=View_Data::getValue('menu_link_name');?>" name="name" /><br />
            The name of the menu link
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label for="link_url">URL <span class="required">*</span></label><br />
            <input id="link_url" class="fluid input-text required" type="text" maxlength="255" value="<?=View_Data::getValue('menu_link_url');?>" name="name" /><br />
            The URL the menu link points to
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label for="link_description">Description</label><br />
            <input id="link_description" class="fluid input-text" type="text" maxlength="255" value="<?=View_Data::getValue('menu_link_description');?>" name="name" /><br />
            When a visitor places the mouse cursor over the link, this is the text that will appear in the popup tooltip
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label for="link_css_class">CSS Class</label><br />
            <input id="link_css_class" class="fluid input-text" type="text" maxlength="255" value="<?=View_Data::getValue('menu_link_css_class');?>" name="name" /><br />
            The CSS class to apply to this link, if you are unsure of what to enter here do not change this value
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="active" id="link_active" value="yes" checked="checked" /> Active?</label><br />
            Should the link be displayed when the menu is in use?
            <div class="break"><br /><br /></div>
          </div>

          <!--
          xxx finish
          <div class="form-item">
            <label for="link_parent">Parent Link</label><br />
            <select id="link_parent" name="parent"><option>&lt;No Parent&gt;</option></select><br />
            The location in the menu where the link should appear
            <div class="break"><br /><br /></div>
          </div>
          -->
        </div>
      </div>
    </div>
  </div>
</form>