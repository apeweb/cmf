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
        <h2>Manage Menu</h2>
        <ul class="product_tabs">
          <li class="active"><a href="#settings"><span>Settings</span></a></li>
          <li><a href="#links"><span>Links</span></a></li>
        </ul>
      </div>
      <div class="left flexi_column clearfix">
        <div class="intro">
          <table>
            <tbody>
              <tr>
                <td style="width:50%;">
                  <h1 class="package"><?=View_Data::getValue('menu_name');?></h1>
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
            <label for="name">Name <span class="required">*</span></label><br />
            <input id="name" class="fluid input-text required" type="text" maxlength="255" value="<?=View_Data::getValue('menu_name');?>" name="name" /><br />
            The name of the menu for your reference
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="links">
          <h3>Links</h3>
          <p>Configure which links appear in the menu and in which order.</p>

          <a class="form-button add" href="/admin/menus/add-link/<?=View_Data::getValue('menu_id');?>">
            <span>Add Link</span>
          </a>

          <br /><br />

          <fieldset>
            <table class="data tree_order_rows" border="1">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>URL</th>
                  <th>Weight</th>
                  <th>Active</th>
                  <th width="150">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?=View_Data::getValue('menu_links');?>
              </tbody>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</form>