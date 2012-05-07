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
        'message'	: 'You are about to delete this product.<br />Do you want to continue?',
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
              <h1 class="package">Product Price Labels</h1>
            </td>
            <td align="right">
              <a href="/admin/products/price_labels/add" class="form-button add"><span>Add Product Price Label</span></a>
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
      <table class="data order_rows data resizable sortable last" border="1">
        <thead>
          <tr>
            <th width="13" class="checkcolumn not_sortable">
              <input class="checkall" type="checkbox" name="checkall" />
            </th>
            <th><a href="?sort=code" title="Sort by code">Name</a></th>
            <th><a href="?sort=price" title="Sort by price">Display by Default?</a></th>
            <th><a href="?sort=stock" title="Sort by stock">Charge by Default?</a></th>
            <th><a href="?sort=updated" title="Sort by last updated">Style</a></th>
            <th class="not_sortable" width="100">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">Price</a></td>
            <td><a href="#">Yes</a></td>
            <td><a href="#">Yes</a></td>
            <td><a href="#">&pound;99.99</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">RRP Price</a></td>
            <td><a href="#">Yes</a></td>
            <td><a href="#">No</a></td>
            <td><a href="#">RRP &pound;99.99</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">Sale Price</a></td>
            <td><a href="#">No</a></td>
            <td><a href="#">No</a></td>
            <td><a href="#"><strong style="color:red">now <big>&pound;99.99</big></strong></a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">Original Price</a></td>
            <td><a href="#">No</a></td>
            <td><a href="#">No</a></td>
            <td><a href="#">was <del>&pound;99.99</del></a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">Manufacturer Price</a></td>
            <td><a href="#">No</a></td>
            <td><a href="#">No</a></td>
            <td><a href="#">&pound;99.99</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
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
</form>