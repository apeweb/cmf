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
<form method="post" action="">
  <div id="main_content">
    <div class="columns">
      <div class="left single_column clearfix">
        <h2>Product Information</h2>
        <ul class="product_tabs">
          <li class="active"><a href="#product_types"><span>Product Type</span></a></li>
        </ul>
      </div>
      <div class="left flexi_column clearfix">
        <div class="intro">
          <table>
            <tbody>
              <tr>
                <td style="width:50%;">
                  <h1 class="package">Product Type</h1>
                </td>
                <td align="right">
                  <a href="/admin/products/add/" class="form-button back"><span>Back</span></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="panel active" id="product_types">
          <p>Next, you need to choose the type of product you are adding:</p>
          <div class="form-item">
            <label for="product_type">Product Type</label><br />
            <select name="product_type" id="product_type">
              <option>Rubber Mulch</option>
            </select><br />
            The product type determines which options are available for the product
          </div>
          <span class="form-button save"><input type="submit" class="wrapped-button" name="action" value="Continue" /></span><br /><br />
          You can also <a href="/admin/products/types/">customise the product types</a> to suite your needs.
        </div>
      </div>
    </div>
  </div>
</form>