<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
  $(function () {
    // Delete product confirmation
    $('.delete').click(function(){
      $.confirm({
        'title'		: 'Delete Confirmation',
        'message'	: 'You are about to delete this menu.<br />Do you want to continue?',
        'buttons'	: {
          'Yes'	: {
            'class'	: 'blue',
            'action': function(){
              // xxx send user to location
            }
          },
          'No'	: {
            'class'	: 'grey',
            'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
          }
        }
      });

      return false;
    });
  });
//]]>
</script>
<form method="post" action="">
  <div id="main_content">
    <div class="intro">
      <table>
        <tbody>
          <tr>
            <td style="width:50%;">
              <h1 class="package">Menus</h1>
            </td>
            <td align="right">
              <a href="/admin/menus/add" class="form-button add"><span>Add Menu</span></a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

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
            <th width="13" class="checkcolumn not_sortable">
              <input class="checkall" type="checkbox" name="checkall" />
            </th>
            <th><a href="?sort=name" title="Sort by name">Name</a></th>
            <th><a href="?sort=style" title="Sort by style">Type</a></th>
            <th class="not_sortable" width="100">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (Cmf_Menus::getAllMenus() as $menu): ?>
            <tr>
              <td class="checkcolumn">
                <input type="checkbox" name="toggle" />
              </td>
              <td><a href="/admin/menus/edit/<?=$menu->getId()?>"><?=$menu->getName()?></a></td>
              <td><a href="/admin/menus/edit/<?=$menu->getId()?>"><?=$menu->getType()?></a></td>
              <td class="actions"><a href="/admin/menus/delete/<?=$menu->getId()?>" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="/admin/menus/edit/<?=$menu->getId()?>" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
            </tr>
          <?php endforeach; ?>
          <!--
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">Admin Menu</a></td>
            <td><a href="#">Navigation Bar</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="/admin/menus/edit/1" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">Admin Settings</a></td>
            <td><a href="#">Settings</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="/admin/menus/edit/2" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">Main Menu</a></td>
            <td><a href="#">Navigation Bar</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="/admin/menus/edit/3" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          -->
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
</form>