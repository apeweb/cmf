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
        <h2>Manage Monitor</h2>
        <ul class="product_tabs">
          <li class="active"><a href="#settings"><span>Settings</span></a></li>
          <li><a href="#key_messages"><span>Key Messages</span></a></li>
          <li><a href="#content"><span>Content</span></a></li>
          <li><a href="#matches"><span>Matches</span></a></li>
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
                  <a href="/admin/monitors" class="form-button back"><span>Back</span></a>
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
          <p>Use the settings to configure basic information about the monitor.</p>
          <div class="form-item">
            <label for="name">Name <span class="required">*</span></label><br />
            <input id="name" class="fluid input-text required" type="text" maxlength="255" value="<?=View_Data('pr_campaign_name');?>" name="name" /><br />
            The name of the monitor
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="active" id="active" value="yes" checked="checked" /> Active?</label><br />
            Should the monitor start searching for matches immediately?
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="key_messages">
          <h3>Key Messages</h3>
          <p>Add key messages to track as part of this monitor.</p>

          <a class="form-button add" href="/admin/menus/add-link/<?=View_Data::getValue('menu_id');?>">
            <span>Add Key Message</span>
          </a>

          <br /><br />

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
                <tr>
                  <td colspan="4">There are no key messages to display</td>
                </tr>
              </thead>
              <tbody>
                <!-- xxx list the key messages -->
              </tbody>
            </table>
          </fieldset>
        </div>
        <div class="panel" id="content">
          <h3>Content</h3>
          <p>Add full body content such as press releases which will be used to identify matches.</p>

          <a class="form-button add" href="/admin/menus/add-link/<?=View_Data::getValue('menu_id');?>">
            <span>Add Content</span>
          </a>

          <br /><br />

          <fieldset>
            <table class="data resizable sortable last" border="1">
              <thead>
                <tr>
                  <th width="13" class="checkcolumn not_sortable">
                    <input class="checkall" type="checkbox" name="checkall" />
                  </th>
                  <th><a href="?sort=file_name" title="Sort by file name">File Name</a></th>
                  <th><a href="?sort=type" title="Sort by date added">Type</a></th>
                  <th class="not_sortable" width="100">Actions</th>
                </tr>
                <tr>
                  <td colspan="4">There are no files to display</td>
                </tr>
              </thead>
              <tbody>
                <!-- xxx list the key messages -->
              </tbody>
            </table>
          </fieldset>
        </div>
        <div class="panel" id="matches">
          <h3>Matches</h3>
          <p>Manage the matches found and add any matches you are aware of.</p>

          <a class="form-button add" href="/admin/menus/add-link/<?=View_Data::getValue('menu_id');?>">
            <span>Add Match</span>
          </a>

          <br /><br />

          <table>
            <tbody>
              <tr>
                <td valign="bottom">
                  Pages: &nbsp; <strong>1</strong>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page 1 of 1
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Results per page:&nbsp;
                  <span class="paging_results"><a href="?action=paging_results=20">20</a> <a href="?action=paging_results=30">30</a> <a href="?action=paging_results=50">50</a> <a href="?action=paging_results=100">100</a> <a href="?action=paging_results=200">200</a></span>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td valign="bottom" align="right">
                  With selected:
                  <select name="action">
                    <option value="">Choose an action</option>
                    <option value="delete">Delete</option>
                  </select>
                  <input type="submit" name="submit" class="form-button" value="Go" />
                </td>
              </tr>
            </tbody>
          </table>

          <fieldset>
            <table class="data resizable sortable last" border="1">
              <thead>
                <tr>
                  <th><a href="?sort=web_page" title="Sort by web page">Web Page</a></th>
                  <th><a href="?sort=quality" title="Sort by quality">Quality</a></th>
                  <th><a href="?sort=match" title="Sort by match">Match</a></th>
                  <th><a href="?sort=location" title="Sort by location">Location</a></th>
                  <th><a href="?sort=date_discovered" title="Sort by date found">Date Discovered</a></th>
                  <th><a href="?sort=important" title="Sort by importance">Importance</a></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><a href="http://eon.businesswire.com/news/eon/20100510005316/en" target="_blank">eon.businesswire.com/news/eon/20100510005316/en</a></td>
                  <td>56%</td>
                  <td>79%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>High</td>
                </tr>
                <tr>
                  <td><a href="http://www.nysportsjournalism.com/castrol-scores-deal-51110/" target="_blank">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
                  <td>69%</td>
                  <td>88%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>Medium</td>
                </tr>
                <tr>
                  <td><a href="http://eon.businesswire.com/news/eon/20100510005316/en" target="_blank">eon.businesswire.com/news/eon/20100510005316/en</a></td>
                  <td>56%</td>
                  <td>79%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>High</td>
                </tr>
                <tr>
                  <td><a href="http://www.nysportsjournalism.com/castrol-scores-deal-51110/" target="_blank">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
                  <td>69%</td>
                  <td>88%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>Medium</td>
                </tr>
                <tr>
                  <td><a href="http://eon.businesswire.com/news/eon/20100510005316/en" target="_blank">eon.businesswire.com/news/eon/20100510005316/en</a></td>
                  <td>56%</td>
                  <td>79%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>High</td>
                </tr>
                <tr>
                  <td><a href="http://www.nysportsjournalism.com/castrol-scores-deal-51110/" target="_blank">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
                  <td>69%</td>
                  <td>88%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>Medium</td>
                </tr>
                <tr>
                  <td><a href="http://eon.businesswire.com/news/eon/20100510005316/en" target="_blank">eon.businesswire.com/news/eon/20100510005316/en</a></td>
                  <td>56%</td>
                  <td>79%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>High</td>
                </tr>
                <tr>
                  <td><a href="http://www.nysportsjournalism.com/castrol-scores-deal-51110/" target="_blank">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
                  <td>69%</td>
                  <td>88%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>Medium</td>
                </tr>
                <tr>
                  <td><a href="http://eon.businesswire.com/news/eon/20100510005316/en" target="_blank">eon.businesswire.com/news/eon/20100510005316/en</a></td>
                  <td>56%</td>
                  <td>79%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>High</td>
                </tr>
                <tr>
                  <td><a href="http://www.nysportsjournalism.com/castrol-scores-deal-51110/" target="_blank">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
                  <td>69%</td>
                  <td>88%</td>
                  <td>US</td>
                  <td>2012-05-24</td>
                  <td>Medium</td>
                </tr>
              </tbody>
            </table>
          </fieldset>

          <table>
            <tbody>
              <tr>
                <td valign="bottom">
                  Pages: &nbsp; <strong>1</strong>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page 1 of 1
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Results per page:&nbsp;
                  <span class="paging_results"><a href="?action=paging_results=20">20</a> <a href="?action=paging_results=30">30</a> <a href="?action=paging_results=50">50</a> <a href="?action=paging_results=100">100</a> <a href="?action=paging_results=200">200</a></span>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
                <td valign="bottom" align="right">
                  With selected:
                  <select name="action">
                    <option value="">Choose an action</option>
                    <option value="delete">Delete</option>
                  </select>
                  <input type="submit" name="submit" class="form-button" value="Go" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</form>