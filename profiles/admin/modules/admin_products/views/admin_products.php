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
              <h1 class="package">Products</h1>
            </td>
            <td align="right">
              <a href="/admin/products/add/" class="form-button add"><span>Add Product</span></a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <table>
      <tbody>
        <tr>
          <td valign="bottom">
            Pages: &nbsp; <strong>1</strong> &nbsp; <a href="?page=2">2</a> &nbsp; <a href="?page=3">3</a> &nbsp; <a href="?page=4">4</a> &nbsp; <a href="?page=5">5</a> &nbsp; <a href="?page=6">6</a> &nbsp; <a href="?page=7">7</a> &nbsp; <a href="?page=8">8</a> &nbsp; <a href="?page=9">9</a> &nbsp; <a href="?page=10">10</a> &nbsp; <a href="?page=next">&raquo;</a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page 1 of 323
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
            <th width="50"><a href="?sort=id" title="Sort by id">Id</a></th>
            <th width="200"><a href="?sort=code" title="Sort by code">Code</a></th>
            <th><a href="?sort=description" title="Sort by description">Description</a></th>
            <th width="50"><a href="?sort=price" title="Sort by price">Price</a></th>
            <th width="50"><a href="?sort=stock" title="Sort by stock">Stock</a></th>
            <th width="130"><a href="?sort=updated" title="Sort by last updated">Last Updated</a></th>
            <th width="100" class="not_sortable">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">1</a></td>
            <td><a href="#">GQUFAX</a></td>
            <td><a href="#">Canon EOS Rebel T2i</a></td>
            <td><a href="#">&pound;9.99</a></td>
            <td><a href="#">22</a></td>
            <td><a href="#">22/01/2012 9:53</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Copy" class="copy"><img src="/themes/admin/images/mini-icons/copy.gif" alt="Copy" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">101</a></td>
            <td><a href="#">A</a></td>
            <td><a href="#">Canon EOS Rebel T2i</a></td>
            <td><a href="#">&pound;7.99</a></td>
            <td><a href="#">22</a></td>
            <td><a href="#">21/01/2012 7:53</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Copy" class="copy"><img src="/themes/admin/images/mini-icons/copy.gif" alt="Copy" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
          <tr>
            <td class="checkcolumn">
              <input type="checkbox" name="toggle" />
            </td>
            <td><a href="#">2</a></td>
            <td><a href="#">B</a></td>
            <td><a href="#">Canon EOS Rebel T2i</a></td>
            <td><a href="#">&pound;8.99</a></td>
            <td><a href="#">22</a></td>
            <td><a href="#">21/01/2012 9:53</a></td>
            <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Copy" class="copy"><img src="/themes/admin/images/mini-icons/copy.gif" alt="Copy" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
          </tr>
        </tbody>
      </table>
    </fieldset>

    <table>
      <tbody>
        <tr>
          <td valign="bottom">
            Pages: &nbsp; <strong>1</strong> &nbsp; <a href="?page=2">2</a> &nbsp; <a href="?page=3">3</a> &nbsp; <a href="?page=4">4</a> &nbsp; <a href="?page=5">5</a> &nbsp; <a href="?page=6">6</a> &nbsp; <a href="?page=7">7</a> &nbsp; <a href="?page=8">8</a> &nbsp; <a href="?page=9">9</a> &nbsp; <a href="?page=10">10</a> &nbsp; <a href="?page=next">&raquo;</a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page 1 of 323
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