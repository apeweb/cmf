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
        <h2>Manage PR Campaign</h2>
        <ul class="product_tabs">
          <li class="active"><a href="#settings"><span>Settings</span></a></li>
          <li class="active"><a href="#key_messages"><span>Key Messages</span></a></li>
        </ul>
      </div>
      <div class="left flexi_column clearfix">
        <div class="intro">
          <table>
            <tbody>
              <tr>
                <td style="width:50%;">
                  <h1 class="package"><?=View_Data('pr_campaign_title');?></h1>
                </td>
                <td align="right">
                  <a href="/admin/campaigns/" class="form-button back"><span>Back</span></a>
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
          <p>Use the settings to configure basic information about the PR Campaign.</p>
          <div class="form-item">
            <label for="name">Name <span class="required">*</span></label><br />
            <input id="name" class="fluid input-text required" type="text" maxlength="255" value="<?=View_Data('pr_campaign_name');?>" name="name" /><br />
            The name of the PR campaign
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="active" id="active" value="yes" checked="checked" /> Active?</label><br />
            Should the PR tracker start searching for matches immediately?
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="key_messages">
          <h3>Settings</h3>
          <p>Add key messages to track as part of this campaign.</p>

          <div class="form-item">
            <label for="name">Key Message <span class="required">*</span></label><br />
            <input id="name" class="fluid input-text required" type="text" maxlength="255" value="<?=View_Data('pr_campaign_key_message');?>" name="name" /><br />
            The main message you are trying to get across in the campaign, if possible this should be an exert from the PR sent out
            <div class="break"><br /><br /></div>
          </div>

          <fieldset>
            <table class="data resizable sortable last" border="1">
              <thead>
                <tr>
                  <th width="13" class="checkcolumn not_sortable">
                    <input class="checkall" type="checkbox" name="checkall" />
                  </th>
                  <th><a href="?sort=key_message" title="Sort by key message">Key Message</a></th>
                  <th><a href="?sort=date_added" title="Sort by date added">Date Added</a></th>
                  <th class="not_sortable" width="100">Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- xxx list the key messages -->
              </tbody>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</form>