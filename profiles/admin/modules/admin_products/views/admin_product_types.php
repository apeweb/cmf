<?php

/**
 * Copyright (c) 2011 Ape Web Ltd.  All rights reserved.
 *
 * The use and distribution terms for this software are contained in the file
 * named license.txt, which can be found in the root of this distribution.
 * By using this software in any fashion, you are agreeing to be bound by the
 * terms of this license.
 *
 * You must not remove this notice, or any other, from this software.
 */

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
        'message'	: 'You are about to delete this product type.<br />Do you want to continue?',
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
              <h1 class="package">Product Types</h1>
            </td>
            <td align="right">
              <a href="/admin/products/types/add" class="form-button add"><span>Add Product Type</span></a>
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
      <table class="data resizable sortable" border="1">
        <thead>
          <tr>
            <th width="13" class="checkcolumn not_sortable">
              <input class="checkall" type="checkbox" name="checkall" />
            </th>
            <th><a href="?sort=code" title="Sort by code">Name</a></th>
            <th width="100" class="not_sortable">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach(Admin_Products::getAllProductTypes() as $productType): ?>
            <tr>
              <td class="checkcolumn">
                <input type="checkbox" name="toggle[]" value="<?=$productType['prdt_id'];?>" />
              </td>
              <td><a href="#"><?=$productType['prdt_name'];?></a></td>
              <td class="actions"><a href="?delete=<?=$productType['prdt_id'];?>" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="/admin/products/types/" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
            </tr>
          <?php endforeach; ?>
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