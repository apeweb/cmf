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
<form action="" method="post">
  <div id="main_content">
    <div class="columns">
      <div class="left single_column clearfix">
        <h2>Product Information</h2>
        <ul class="product_tabs">
          <li class="active"><a href="#general_information"><span>General Information</span></a></li>
          <li><a href="#unique_identifiers"><span>Unique Identifiers</span></a></li>
          <li><a href="#categories"><span>Categories</span></a></li>
          <li><a href="#prices"><span>Prices</span></a></li>
          <!-- price conditions only show on child products
          <li><a href="#price_conditions"><span>Price Conditions</span></a></li>
          -->
          <li><a href="#shipping"><span>Shipping</span></a></li>
          <li><a href="#inventory"><span>Inventory</span></a></li>
          <li><a href="#images"><span>Images</span></a></li>
          <li><a href="#particulars"><span>Particulars</span></a></li>
          <li><a href="#cross-selling"><span>Cross-selling</span></a></li> <!-- additional products and services available -->
          <li><a href="#e-mail_notifications"><span>E-mail Notifications</span></a></li>
          <li><a href="#returns"><span>Returns</span></a></li>
          <li><a href="#reviews"><span>Reviews</span></a></li>
          <li><a href="#additional_options"><span>Additional Options</span></a></li>
        </ul>
        <h2>Product Options</h2>
        <ul class="product_tabs">
          <li><a href="#options_available"><span>Options Available</span></a></li>
        </ul>
      </div>
      <div class="left flexi_column clearfix">
        <div class="intro">
          <table>
            <tbody>
              <tr>
                <td style="width:50%;">
                  <h1 class="package"><?=View_Data::getValue('product_type_name');?> Product Listing</h1>
                </td>
                <td align="right">
                  <a href="/admin/products/add/<?=View_Data::getValue('prd_id');?>/type/" class="form-button back"><span>Back</span></a>
                  <span class="form-button save">
                    <input class="wrapped-button first last" type="submit" value="Save" name="action">
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="panel active" id="general_information">
          <h3>General Information</h3>
          <p>Use the general information to supply information about the product.</p>
          <div class="form-item">
            <label for="title">Title <span class="required">*</span></label><br />
            <input id="title" class="fluid input-text required" type="text" maxlength="255" value="" name="title" /><br />
            Used for the page title and linking to the product
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="bullet_points">Bullet Points</label><br />
            <textarea id="bullet_points" class="fluid input-text" rows="6" cols="100"></textarea><br />
            Add 1 bullet point per line
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="summary">Summary</label><br />
            <input id="summary" class="fluid input-text" type="text" maxlength="255" value="" name="summary" /><br />
            Used for the meta description, search listings description, and will appear in place of the bullet points if no bullet points were listed
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="description">Description <span class="required">*</span></label><br />
            <textarea id="description" class="fluid input-text required" rows="10" cols="100"></textarea><br />
            The full product description
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="keywords">Keywords</label><br />
            <input id="keywords" class="fluid input-text" type="text" maxlength="255" value="" name="keywords" /><br />
            Comma separated list, used for the meta keywords and making it easier to search for products
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="unique_identifiers">
          <h3>Unique Identifiers</h3>
          <p>Supply a SKU code, stock code, and any other unique identifiers for this product. This information is essential to linking the product to 3rd party services such as Google Products.</p>
          <div class="form-item">
            <label for="internal_stock_code">Stock Code</label><br />
            <input id="internal_stock_code" class="fluid input-text" type="text" maxlength="255" value="" name="internal_stock_code" /><br />
            A unique reference to this product, you can leave the stock code blank if you want an automatically generated stock code to be assigned
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="sku">SKU</label><br />
            <input id="sku" class="fluid input-text" type="text" maxlength="255" value="" name="sku" /><br />
            Stock-Keeping Unit
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="upc">UPC</label><br />
            <input id="upc" class="fluid input-text" type="text" maxlength="255" value="" name="upc" /><br />
            Universal Product Code
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="ean">EAN</label><br />
            <input id="ean" class="fluid input-text" type="text" maxlength="255" value="" name="ean" /><br />
            International Article Number (formerly the European Article Number)
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="isbn">ISBN</label><br />
            <input id="isbn" class="fluid input-text" type="text" maxlength="255" value="" name="isbn" /><br />
            International Standard Book Number
            <div class="break"><br /><br /></div>
          </div>
          <div class="form-item">
            <label for="mpn">MPN</label><br />
            <input id="mpn" class="fluid input-text" type="text" maxlength="255" value="" name="mpn" /><br />
            Manufacturer Part Number
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="categories">
          <h3>Categories</h3>
          <p>Pick the categories the product is featured in, and pick the main category for the product.</p>

          <div class="form-item">
            <label for="bullet_points">Manufacturer <span class="required">*</span></label><br />
            <select name=""><option>Category 5</option></select><br />
            By linking the product to a manufacturer you can make it easier for Customers to find the product as well as enabling special features relating to the product manufacturer
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label for="bullet_points">Main Product Category <span class="required">*</span></label><br />
            <select name="" class="required"><option>Category 5</option></select><br />
            By picking a main category the product page and the pages that link to the product can be optimised for search engines
            <br />(by ticking this the tree below should automatically tick the category too)
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label>Additional Product Categories</label><br />
            <p>Pick the categories relating to the product</p>
            <ul class="tree">
              <li>
                <label>
                  <input type="checkbox" /> Category 1
                </label>
              </li>
              <li>
                <label>
                  <input type="checkbox" /> Category 2
                </label>
                <ul>
                  <li>
                    <label>
                      <input type="checkbox" /> Category 3
                    </label>
                  </li>
                  <li>
                    <label>
                      <input type="checkbox" /> Category 4
                    </label>
                    <ul>
                      <li>
                        <label>
                          <input type="checkbox" checked="checked" /> Category 5
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" /> Category 6
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" /> Category 7
                        </label>
                      </li>
                    </ul>
                  </li>
                  <li>
                    <label>
                      <input type="checkbox" /> Category 8
                    </label>
                    <ul>
                      <li>
                        <label>
                          <input type="checkbox" /> Category 9
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" /> Category 10
                        </label>
                      </li>
                      <li>
                        <label>
                          <input type="checkbox" /> Category 11
                        </label>
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="prices">
          <h3>Prices</h3>
          <p>Specify the price you want to charge for the product along with options to display other prices such as the RRP price.</p>
          <div class="form-item">
            <label for="automatically_convert_currency"><input type="checkbox" name="automatically_convert_currency" id="automatically_convert_currency" value="yes" checked="checked" /> Automatically Convert Currency?</label><br />
            If you primarily sell in multiple countries opposed to one main country, it looks more professional if currencies are &euro;1.99 as opposed to &euro;1.93
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label for="tax_classification">Tax Classification <span class="required">*</span></label><br />
            <select id="tax_classification" name="tax_classification"><option>Standard</option><option>Reduced</option><option>Zero Rated</option></select><br />
            What tax rate should be applied to this product?
            <div class="break"><br /><br /></div>
          </div>

          <table class="data maxtrix" border="1">
            <thead>
              <tr>
                <th>Price Label</th>
                <th width="150">Price</th>
                <th width="100">Display?</th>
                <th width="100">Charge <span class="required">*</span></th>
                <!-- child option only
                <th width="100">Allow Conditions?</th>
                -->
              </tr>
            </thead>
            <tbody>
              <tr class="group group1">
                <td valign="top">
                  Price
                </td>
                <td valign="top">
                  <div class="price-line">
                    <label for="">GBP &pound;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                  </div>
                  <div class="currencies">
                    <div class="price-line">
                      <label for="">EUR &euro;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">USD $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">AU $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">CA $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                  </div>
                </td>
                <td valign="top">
                  <input type="checkbox" name="pid1_display_spl1" value="yes" checked="checked" />
                </td>
                <td valign="top">
                  <input type="radio" name="pid1_charge" value="yes" checked="checked" />
                </td>
                <!--
                <td rowspan="5" valign="middle">
                  <input type="checkbox" name="allow_conditions" value="yes" checked="checked" />
                </td>
                -->
              </tr>

              <tr class="group group1">
                <td valign="top">
                  RRP Price
                </td>
                <td valign="top">
                  <div class="price-line">
                    <label for="">GBP &pound;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                  </div>
                  <div class="currencies">
                    <div class="price-line">
                      <label for="">EUR &euro;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">USD $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">AU $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">CA $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                  </div>
                </td>
                <td valign="top">
                  <input type="checkbox" name="pid1_display_spl1" value="yes" checked="checked" />
                </td>
                <td valign="top">
                  <input type="radio" name="pid1_charge" value="yes" />
                </td>
              </tr>

              <tr class="group group1">
                <td valign="top">
                  Original Price
                </td>
                <td valign="top">
                  <div class="price-line">
                    <label for="">GBP &pound;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                  </div>
                  <div class="currencies">
                    <div class="price-line">
                      <label for="">EUR &euro;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">USD $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">AU $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">CA $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                  </div>
                </td>
                <td valign="top">
                  <input type="checkbox" name="pid1_display_spl1" value="yes" />
                </td>
                <td valign="top">
                  <input type="radio" name="pid1_charge" value="yes" />
                </td>
              </tr>

              <tr class="group group1">
                <td valign="top">
                  Sale Price
                </td>
                <td valign="top">
                  <div class="price-line">
                    <label for="">GBP &pound;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                  </div>
                  <div class="currencies">
                    <div class="price-line">
                      <label for="">EUR &euro;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">USD $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">AU $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">CA $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                  </div>
                </td>
                <td valign="top">
                  <input type="checkbox" name="pid1_display_spl1" value="yes" />
                </td>
                <td valign="top">
                  <input type="radio" name="pid1_charge" value="yes" />
                </td>
              </tr>

              <tr class="group group1">
                <td valign="top">
                  Manufacturer Price
                </td>
                <td valign="top">
                  <div class="price-line">
                    <label for="">GBP &pound;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                  </div>
                  <div class="currencies">
                    <div class="price-line">
                      <label for="">EUR &euro;</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">USD $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">AU $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                    <div class="price-line">
                      <label for="">CA $</label> <input type="text" class="input-text narrow" name="price" id="" value="" /><br />
                    </div>
                  </div>
                </td>
                <td valign="top">
                  <input type="checkbox" name="pid1_display_spl1" value="yes" />
                </td>
                <td valign="top">
                  <input type="radio" name="pid1_charge" value="yes" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="panel" id="price_conditions">
          <h3>Price Conditions</h3>
          <p>Use price conditions to set bulk buy discounts, sale prices and early-bird booking fees.</p>
          <fieldset class="form-item">
            <legend>Add A Price Condition</legend>
            <div class="form-item clearfix">
              <label for="condition_name">Name</label><br />
              <input type="text" id="condition_name" class="input-text fluid" name="condition_name" value="" /><br />
              The name for this condition to make it easy to identify what the condition relates to, for instance &quot;New Year Sale, 10% off&quot;
              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item clearfix">
              <div class="column">
                <label for="condition_price_option">Option</label><br />
                <select name="condition_price_option" id="condition_price_option">
                  <option>All Options</option>
                  <option>Default</option>
                  <option>Color: Red, Weight: 50g</option>
                  <option>Color: Red, Weight: 100g</option>
                </select><br />
                Apply to a specific product option
              </div>

              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item clearfix">
              <div class="column">
                <label for="condition_where">Attribute</label><br />
                <select name="condition_where" id="condition_where">
                  <option>Colour</option>
                  <option>Weight</option>
                  <option>Quantity</option>
                  <option>Date of order</option>
                  <option>Order subtotal</option>
                  <option>Shipping country</option>
                </select>
              </div>

              <div class="column">
                <label for="condition_is">Is</label><br />
                <select name="condition_is" id="condition_is">
                  <option>Equal to</option>
                  <option>Not equal to</option>
                  <option>More than</option>
                  <option>Less than</option>
                  <option>Difference of</option>
                </select>
              </div>

              <!-- Attribute value will list the product attributes for products so that the order date can be compared with the room booking date to give a early bird discount -->
              <div class="column">
                <label for="condition_type">Type</label><br />
                <select name="condition_type" id="condition_type">
                  <option>Custom value</option>
                  <option>Attribute value</option>
                </select>
              </div>

              <!-- will automatically only show the correct untits for the product based on the field type -->
              <div class="column">
                <label for="condition_value">Value</label><br />
                <input type="text" id="condition_value" class="input-text" name="condition_value" value="30" />
                <select name="condition_value_type" id="condition_value_type">
                  <option>Day(s)</option>
                  <option>Week(s)</option>
                  <option>Months(s)</option>
                </select>
              </div>

              <div class="clear">Define the patterns for when this condition applies</div>

              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item clearfix">
              <div class="column">
                <label for="condition_user_has_group">Customer Has Group</label><br />
                <select name="condition_user_has_group" id="condition_user_has_group">
                  <option>Any group</option>
                  <option>Price Band A</option>
                </select><br />
                Specify whether this condition relates to a specific group or not
              </div>
              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item clearfix">
              <div class="column">
                <label for="condition_then">Then</label><br />
                <select name="condition_then" id="condition_then">
                  <option>Discount the price by</option>
                  <option>Add a charge of</option>
                  <option>Set price to</option>
                </select>
              </div>

              <div class="column">
                <label for="condition_amount">Amount</label><br />
                <input type="text" id="condition_amount" class="input-text" name="condition_amount" value="30" />
                <select name="condition_amount_type" id="condition_amount_type">
                  <option>GBP &pound;</option>
                  <option>Percent</option>
                </select>
              </div>

              <a href="" class="form-button form-item-column-item-no-label add"><span>Add Another Condition</span></a>

              <div class="clear">Define the patterns for when this condition applies</div>

              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item clearfix">
              <div class="column">
                <label for="condition_date_from">Date From</label><br />
                <input type="text" id="condition_date_from" class="input-text narrow" name="condition_date_from" value="12/01/2012" />
              </div>

              <div class="column">
                <label for="condition_date_to">Date To</label><br />
                <input type="text" id="condition_date_to" class="input-text narrow" name="condition_date_to" value="20/01/2012" /><br />
              </div>

              <div class="clear">Condition will automatically enable and disable based on the date range set</div>

              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item clearfix">
              <label class="clearfix"><input type="checkbox" name="priced_by_attributes" id="priced_by_attributes" value="yes" checked="checked" /> Change Original Label?</label><div class="break"><br /></div>
              Update an existing label to show the original price charged is not the price being charged currently<br /><br />

              <div class="column">
                <label for="condition_original_label_style">Original Label</label><br />
                <select name="condition_original_label_style" id="condition_original_label_style">
                  <option>Price</option>
                  <option>RRP Price</option>
                  <option>Original Price</option>
                  <option>Sale Price</option>
                  <option>Manufacturer Price</option>
                </select>
              </div>

              <div class="column">
                <label for="condition_original_label_style">Original Label Style</label><br />
                <select name="condition_original_label_style" id="condition_original_label_style">
                  <option>Inherit</option>
                  <option>Standard</option>
                  <option>Sale</option>
                  <option>Bulk Buy</option>
                  <option>Early-bird</option>
                  <option>Crossed Out</option>
                </select><br />
              </div>

              <div class="clear">Set how you want the original label to display</div>

              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item clearfix">
              <label class="clearfix"><input type="checkbox" name="priced_by_attributes" id="priced_by_attributes" value="yes" checked="checked" /> Add a New Label?</label><div class="break"><br /></div>
              Adding a new label means the system will show the price to charge based on the conditions in the new label and show the original price in the original label<br /><br />

              <div class="column">
                <label for="condition_original_label_style">New Label</label><br />
                <select name="condition_original_label_style" id="condition_original_label_style">
                  <option>Price</option>
                  <option>RRP Price</option>
                  <option>Original Price</option>
                  <option>Sale Price</option>
                  <option>Manufacturer Price</option>
                </select>
              </div>

              <div class="column">
                <label for="condition_new_label_style">New Label Style</label><br />
                <select name="condition_new_label_style" id="condition_new_label_style">
                  <option>Standard</option>
                  <option>Sale</option>
                  <option>Bulk Buy</option>
                  <option>Early-bird</option>
                  <option>Crossed Out</option>
                </select>
              </div><br />

              <div class="clear">Set how you want the new label to display</div>

              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item">
              <label><input type="checkbox" name="continue_processing_conditions" value="yes" /> Continue Processing Conditions?</label><br />
              Should all other conditions continue to be processed or ignored
              <div class="break"><br /><br /></div>
            </div>

            <a href="#" class="form-button add"><span>Add Price Condition</span></a>
          </fieldset>

          <fieldset>
            <p class="hint"><strong>Tip:</strong> Drag the table rows using the drag handles (<img class="dragHandle" src="/themes/admin/images/drag-handle.png" alt="Drag handle" />) to change the order of which conditions are processed.</p>
            <table class="data order_rows" border="1">
              <thead>
                <tr>
                  <th width="5" align="center">
                    <input class="checkall" type="checkbox" name="checkall" />
                  </th>
                  <th>Condition Name</th>
                  <th>Apply To</th>
                  <th>Condition</th>
                  <th>Effect</th>
                  <th>Date Range</th>
                  <th>New Label Format</th>
                  <th>Continue?</th>
                  <th width="150">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td align="center">
                    <input type="checkbox" name="toggle" />
                  </td>
                  <td>
                    Early-bird discount
                  </td>
                  <td>
                    <em>All Options</em>
                  </td>
                  <td>
                    Where <em>Date of order</em> Is <em>Difference of</em> <em>Date Booked</em> By <em>30</em> <em>days</em>
                  </td>
                  <td>
                    <em>Discount the price by</em> <em>30</em> <em>GBP &pound;</em>
                  </td>
                  <td>
                    Permanently
                  </td>
                  <td>&pound;99.99</td>
                  <td>
                    Yes
                  </td>
                  <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
                </tr>
                <tr>
                  <td align="center">
                    <input type="checkbox" name="toggle" />
                  </td>
                  <td>
                    Bulk buy discount
                  </td>
                  <td>
                    <em>All Options</em>
                  </td>
                  <td>
                    Where <em>Quantity</em> Is <em>More Than</em> <em>50</em>
                  </td>
                  <td>
                    <em>Discount the price by</em> <em>30</em> <em>GBP &pound;</em>
                  </td>
                  <td>
                    Permanently
                  </td>
                  <td>&pound;99.99</td>
                  <td>
                    Yes
                  </td>
                  <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
                </tr>
                <tr>
                  <td align="center">
                    <input type="checkbox" name="toggle" />
                  </td>
                  <td>
                    January 10% sale
                  </td>
                  <td>
                    <em>All Options</em>
                  </td>
                  <td>
                    Where <em>Quantity</em> Is <em>More Than</em> <em>0</em>
                  </td>
                  <td>
                    <em>Discount the price by</em> <em>10</em> <em>%</em>
                  </td>
                  <td>
                    <em>01/01/2012</em> to <em>31/01/2012</em>
                  </td>
                  <td><strong style="color:red">now <big>&pound;99.99</big></strong></td>
                  <td>
                    No
                  </td>
                  <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
                </tr>
              </tbody>
            </table>
          </fieldset>
        </div>

        <div class="panel" id="shipping">
          <h3>Shipping</h3>
          <p>Specify how much you want to charge to ship the product if it requires shipping.</p>

          <div class="form-item">
            <label><input type="checkbox" name="require_shipping" id="require_shipping" value="yes" checked="checked" /> Requires Shipping?</label><br />
            Does this product need to be shipped out to the Customer?
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="require_user_address" id="require_user_address" value="yes" checked="checked" /> Require Shipping Address?</label><br />
            Is the Customer required to supply a shipping address?
            <div class="break"><br /><br /></div>
          </div>

          <div class="shipping_options">
            <div class="form-item">
              <label><input type="checkbox" name="require_delivery_date" id="require_delivery_date" value="yes" /> Require Delivery Date?</label><br />
              Is the Customer required to supply a delivery date?
              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item">
              <label><input type="checkbox" name="free_shipping" id="free_shipping" value="yes" /> Free Shipping?</label><br />
              Is shipping for this product free regardless of any conditions set?
              <div class="break"><br /><br /></div>
            </div>

            <!-- if free shipping picked don't show shipping price table -->

            <!-- <p>Shipping options, base price on order? What tax to apply and so on</p> -->

            <?php /*
            <p>Use the shipping conditions to set the shipping price for the product based on the location of the customer and the products ordered.</p>
            <fieldset class="form-item">
              <legend>Add A Shipping Condition</legend>
              <div class="form-item clearfix">
                <label for="condition_name">Name</label><br />
                <input type="text" id="condition_name" class="input-text fluid" name="condition_name" value="" />
                <div class="break"><br /><br /></div>
              </div>

              <div class="form-item clearfix">
                <div class="column">
                  <label for="condition_price_option">Option</label><br />
                  <select name="condition_price_option" id="condition_price_option">
                    <option>All Options</option>
                    <option>Default</option>
                    <option>Color: Red, Weight: 50g</option>
                    <option>Color: Red, Weight: 100g</option>
                  </select><br />
                  Apply to a specific product option
                </div>

                <div class="break"><br /><br /></div>
              </div>

              <div class="form-item clearfix">
                <div class="column">
                  <label for="condition_price_option">Delivery Method</label><br />
                  <select name="condition_price_option" id="condition_price_option">
                    <option>All Methods</option>
                    <option>Parcelforce Next Day Delivery</option>
                    <option>Parcelforce Next Day Delivery (Before 9am)</option>
                  </select><br />
                  Apply to a specific delivery method
                </div>

                <div class="break"><br /><br /></div>
              </div>

              <div class="form-item clearfix">
                <div class="column">
                  <label for="condition_where">Attribute</label><br />
                  <select name="condition_where" id="condition_where">
                    <option>Colour</option>
                    <option>Weight</option>
                    <option>Quantity</option>
                    <option>Date of order</option>
                    <option>Order subtotal</option>
                    <option>Shipping country</option>
                  </select>
                </div>

                <div class="column">
                  <label for="condition_is">Is</label><br />
                  <select name="condition_is" id="condition_is">
                    <option>Equal to</option>
                    <option>Not equal to</option>
                    <option>More than</option>
                    <option>Less than</option>
                    <option>Difference of</option>
                  </select>
                </div>

                <!-- Attribute value will list the product attributes for products so that the order date can be compared with the room booking date to give a early bird discount -->
                <div class="column">
                  <label for="condition_type">Type</label><br />
                  <select name="condition_type" id="condition_type">
                    <option>Custom value</option>
                    <option>Attribute value</option>
                  </select>
                </div>

                <!-- will automatically only show the correct untits for the product based on the field type -->
                <div class="column">
                  <label for="condition_value">Value</label><br />
                  <input type="text" id="condition_value" class="input-text" name="condition_value" value="30" />
                  <select name="condition_value_type" id="condition_value_type">
                    <option>Day(s)</option>
                    <option>Week(s)</option>
                    <option>Months(s)</option>
                  </select>
                </div>

                <div class="clear">Define the patterns for when this condition applies</div>

                <div class="break"><br /><br /></div>
              </div>

              <div class="form-item clearfix">
                <div class="column">
                  <label for="condition_where">Attribute</label><br />
                  <select name="condition_where" id="condition_where">
                    <option>Colour</option>
                    <option>Weight</option>
                    <option>Quantity</option>
                    <option>Date of order</option>
                    <option>Order subtotal</option>
                    <option>Shipping country</option>
                  </select>
                </div>

                <div class="column">
                  <label for="condition_is">Is</label><br />
                  <select name="condition_is" id="condition_is">
                    <option>Equal to</option>
                    <option>Not equal to</option>
                    <option>More than</option>
                    <option>Less than</option>
                    <option>Difference of</option>
                  </select>
                </div>

                <!-- Attribute value will list the product attributes for products so that the order date can be compared with the room booking date to give a early bird discount -->
                <div class="column">
                  <label for="condition_type">Type</label><br />
                  <select name="condition_type" id="condition_type">
                    <option>Custom value</option>
                    <option>Attribute value</option>
                  </select>
                </div>

                <!-- will automatically only show the correct untits for the product based on the field type -->
                <div class="column">
                  <label for="condition_value">Value</label><br />
                  <input type="text" id="condition_value" class="input-text" name="condition_value" value="30" />
                  <select name="condition_value_type" id="condition_value_type">
                    <option>Day(s)</option>
                    <option>Week(s)</option>
                    <option>Months(s)</option>
                  </select>
                </div>

                <a href="" class="form-button form-item-column-item-no-label add"><span>Add Another Condition</span></a><br />

                <div class="clear">Define the patterns for when this condition applies</div>

                <div class="break"><br /><br /></div>
              </div>

              <div class="form-item clearfix">
                <div class="column">
                  <label for="condition_user_has_group">Customer Has Group</label><br />
                  <select name="condition_user_has_group" id="condition_user_has_group">
                    <option>Any group</option>
                    <option>Price Band A</option>
                  </select>
                </div>
                <div class="break"><br /><br /></div>
              </div>

              <div class="form-item clearfix">
                <div class="column">
                  <label for="condition_then">Then</label><br />
                  <select name="condition_then" id="condition_then">
                    <option>Set delivery charge to</option>
                    <option>Apply free delivery</option>
                    <option>Increase the delivery charge by</option>
                    <option>Decrease the delivery charge by</option>
                  </select>
                </div>

                <div class="column">
                  <label for="condition_amount">Amount</label><br />
                  <input type="text" id="condition_amount" class="input-text" name="condition_amount" value="30" />
                  <select name="condition_amount_type" id="condition_amount_type">
                    <option>GBP &pound;</option>
                    <option>Percent</option>
                  </select>
                </div>

                <div class="break"><br /><br /></div>
              </div>

              <div class="form-item">
                <label><input type="checkbox" name="continue_processing_conditions" value="yes" /> Continue Processing Conditions?</label><br />
                Should all other conditions continue to be processed or ignored
                <div class="break"><br /><br /></div>
              </div>

              <a href="#" class="form-button add"><span>Add Shipping Condition</span></a>
            </fieldset>

            <fieldset>
              <p class="hint"><strong>Tip:</strong> Drag the table rows using the drag handles (<img class="dragHandle" src="/themes/admin/images/drag-handle.png" alt="Drag handle" />) to change the order of which conditions are processed.</p>
              <table class="data order_rows" border="1">
                <thead>
                  <tr>
                    <th width="5" align="center">
                      <input class="checkall" type="checkbox" name="checkall" />
                    </th>
                    <th>Condition Name</th>
                    <th>Apply To</th>
                    <th>Condition</th>
                    <th>Effect</th>
                    <th>Date Range</th>
                    <th>New Label Format</th>
                    <th>Continue?</th>
                    <th width="150">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td align="center">
                      <input type="checkbox" name="toggle" />
                    </td>
                    <td>
                      Early-bird discount
                    </td>
                    <td>
                      <em>All Options</em>
                    </td>
                    <td>
                      Where <em>Date of order</em> Is <em>Difference of</em> <em>Date Booked</em> By <em>30</em> <em>days</em>
                    </td>
                    <td>
                      <em>Discount the price by</em> <em>30</em> <em>GBP &pound;</em>
                    </td>
                    <td>
                      Permanently
                    </td>
                    <td>&pound;99.99</td>
                    <td>
                      Yes
                    </td>
                    <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
                  </tr>
                  <tr>
                    <td align="center">
                      <input type="checkbox" name="toggle" />
                    </td>
                    <td>
                      Bulk buy discount
                    </td>
                    <td>
                      <em>All Options</em>
                    </td>
                    <td>
                      Where <em>Quantity</em> Is <em>More Than</em> <em>50</em>
                    </td>
                    <td>
                      <em>Discount the price by</em> <em>30</em> <em>GBP &pound;</em>
                    </td>
                    <td>
                      Permanently
                    </td>
                    <td>&pound;99.99</td>
                    <td>
                      Yes
                    </td>
                    <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
                  </tr>
                  <tr>
                    <td align="center">
                      <input type="checkbox" name="toggle" />
                    </td>
                    <td>
                      January 10% sale
                    </td>
                    <td>
                      <em>All Options</em>
                    </td>
                    <td>
                      Where <em>Quantity</em> Is <em>More Than</em> <em>0</em>
                    </td>
                    <td>
                      <em>Discount the price by</em> <em>10</em> <em>%</em>
                    </td>
                    <td>
                      <em>01/01/2012</em> to <em>31/01/2012</em>
                    </td>
                    <td><strong style="color:red">now <big>&pound;99.99</big></strong></td>
                    <td>
                      No
                    </td>
                    <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp; <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>
            */ ?>
          </div>
        </div>
        <div class="panel" id="inventory">
          <h3>Inventory</h3>
          <p>Control stock levels and quantity options for orders.</p>

          <div class="form-item">
            <label><input type="checkbox" name="stock_control_enabled" id="stock_control_enabled" value="yes" checked="checked" /> Subtract Stock?</label><br />
            If ticked, stock control for this product is enabled
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item stock-controlled">
            <label for="stock_level">Stock Level</label><br />
            <input id="stock_level" class="fluid input-text required" type="text" value="" name="stock_level" /><br />
            How much stock are you currently holding in your warehouse? If you leave this blank, or set the value to 0 or less, stock level restrictions will not be applied (which is useful if you are drop shipping)
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item stock-controlled">
            <label><input type="checkbox" name="" value="yes" checked="checked" /> Show Out of Stock Message?</label><br />
            Should a message be displayed explaining that the product isn't in stock
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item stock-controlled">
            <label><input type="checkbox" name="" value="yes" checked="checked" /> Allow Customer Notification When Back in Stock?</label><br />
            Allow the Customer to receive an e-mail notification when the product is back in stock
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="quantity_selectable" id="quantity_selectable" value="yes" checked="checked" /> Show Quantity?</label><br />
            Allow the Customer to select the quantity that they want to order?
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item quantity-option">
            <label><input type="checkbox" name="" id="" value="yes" checked="checked" /> Show a Quantity Form Field?</label><br />
            Show a form field (<input type="text" size="1" value="99" />) for setting the quantity the Customer wants to order?
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item quantity-option">
            <label><input type="checkbox" name="" id="" value="yes" checked="checked" /> Show Quantity Increment and Decrement Buttons?</label><br />
            Show buttons for increasing or decreasing the quantity the Customer wants to order?
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item quantity-option">
            <label for="stock_level">Quantity Increments <span class="required">*</span></label><br />
            <input id="" class="fluid input-text required" type="text" value="1" name="stock_level" /><br />
            The quantity to increase or decrease by when Customer clicks on the quantity increment and decrement buttons
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item quantity-option">
            <label for="stock_level">Minimum Order Quantity <span class="required">*</span></label><br />
            <input id="" class="fluid input-text" type="text" value="1" name="stock_level" /><br />
            The minimum quantity the Customer has to order before processing the sale
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item quantity-option">
            <label for="stock_level">Maximum Order Quantity</label><br />
            <input id="" class="fluid input-text" type="text" value="" name="stock_level" /><br />
            The maximum quantity the Customer can order to process the sale, if you leave this blank, or set the value to 0 or less, the value will default to the maximum quantity allowed in any one transaction which is 9999
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="images">
          <h3>Images</h3>
          <p>Add photos and images relating to the product.</p>

          <div class="form-item">
            <label><input type="checkbox" name="" id="" value="yes" checked="checked" /> Allow Image Zoom?</label><br />
            Allow the Customer to zoom into the photo to view the fine detail, in order to support this option you have to upload very high quality images
            <div class="break"><br /><br /></div>
          </div>

          <div class="product_images file_upload_container">
            <span class="fileinput-button form-button add image_upload" id="product_images"></span>
          </div>

          <br /><br />

          <table id="product_image_table" class="data maxtrix order_rows" border="1">
            <thead>
              <tr>
                <th>Thumbnail</th>
                <th>Filename</th>
                <th width="150">Action</th>
              </tr>
            </thead>
            <tbody class="product_images">
              <tr class="product_image static">
                <td valign="top">
                  <span class="gallery_image"><img src="/themes/admin/images/product_thumb.jpg" alt="" /></span>
                </td>
                <td valign="middle">
                  <span class="file_name">waterproof_mulch.jpg</span>
                </td>
                <td valign="middle">
                  <a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a>
                </td>
              </tr>
              <tr class="product_image static">
                <td valign="top">
                  <span class="gallery_image"><img src="/themes/admin/images/product_thumb.jpg" alt="" /></span>
                </td>
                <td valign="middle">
                  waterproof_mulch.jpg
                </td>
                <td  valign="middle">
                  <a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="panel" id="particulars">
          <h3>Particulars</h3>
          <p>Attach additional files which can be downloaded to provide supplimantory information such as a brochure.</p>

          <span class="fileinput-button form-button add">
            <span>Add Files...</span>
            <input type="file" name="files[]" multiple>
          </span>

          <br /><br />

          <fieldset>
            <table class="data order_rows" border="1">
              <thead>
                <tr>
                  <th>Link Title</th>
                  <th>Filename</th>
                  <th width="150">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><a href="#">Brochure</a></td>
                  <td><a href="#">Brochure.pdf</a></td>
                  <td class="actions">
                    <a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp;
                    <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </fieldset>
        </div>
        <div class="panel" id="cross-selling">
          <h3>Cross-selling</h3>
          <p>Suggest other products and services relating to this product.</p>

          <fieldset class="form-item">
            <legend>Add a Related Product or Service</legend>

            <div class="form-item">
              <label for="product_search">Search</label><br />
              <input id="product_search" class="fluid input-text" type="text" maxlength="255" value="" name="product_search" /><br />
              Search for a product by title or stock code
              <div class="break"><br /><br /></div>
            </div>

            <!-- xxx populated by search -->
            <fieldset>
              <table class="data sortable" border="1">
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
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td width="13" class="checkcolumn">
                      <input type="checkbox" name="toggle" />
                    </td>
                    <td><a href="#">1</a></td>
                    <td><a href="#">GQUFAX</a></td>
                    <td><a href="#">Canon EOS Rebel T2i</a></td>
                    <td><a href="#">&pound;9.99</a></td>
                    <td><a href="#">22</a></td>
                    <td><a href="#">22/01/2012 9:53</a></td>
                  </tr>
                </tbody>
              </table>
            </fieldset>

            <a href="#" class="form-button add"><span>Add Related Listings</span></a>
          </fieldset>

          <fieldset>
            <table class="data sortable" border="1">
              <thead>
                <tr>
                  <th width="50"><a href="?sort=id" title="Sort by id">Id</a></th>
                  <th width="200"><a href="?sort=code" title="Sort by code">Code</a></th>
                  <th><a href="?sort=description" title="Sort by description">Description</a></th>
                  <th width="50"><a href="?sort=price" title="Sort by price">Price</a></th>
                  <th width="50"><a href="?sort=stock" title="Sort by stock">Stock</a></th>
                  <th width="130"><a href="?sort=updated" title="Sort by last updated">Last Updated</a></th>
                  <th width="150" class="not_sortable">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><a href="#">1</a></td>
                  <td><a href="#">GQUFAX</a></td>
                  <td><a href="#">Canon EOS Rebel T2i</a></td>
                  <td><a href="#">&pound;9.99</a></td>
                  <td><a href="#">22</a></td>
                  <td><a href="#">22/01/2012 9:53</a></td>
                  <td class="actions"><a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a></td>
                </tr>
              </tbody>
            </table>
          </fieldset>
        </div>
        <div class="panel" id="e-mail_notifications">
          <h3>E-mail Notifications</h3>
          <p>Who should be e-mailed what, for instance e-mail out a product key or e-mail a supplier about an order.</p>

          <fieldset class="form-item">
            <legend>Add an E-mail Notification</legend>

            <div class="form-item">
              <label for="notification_name">Notification Name</label><br />
              <input id="notification_name" class="fluid input-text" type="text" maxlength="255" value="" name="notification_name" /><br />
              Enter the name for this notification, such as &quot;Supplier&quot;
              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item">
              <label for="email_address">E-mail Address</label><br />
              <input id="email_address" class="fluid input-text" type="text" maxlength="255" value="" name="email_address" /><br />
              Enter the e-mail address of the person to be notified when you recieve a sale for this product
              <div class="break"><br /><br /></div>
            </div>

            <div class="form-item">
              <label for="email_address">E-mail Template</label><br />
              <select name=""><option>New Order</option><option>Supplier Dispatch</option></select><br />
              Select which e-mail template will be used when sending the e-mail notification
              <div class="break"><br /><br /></div>
            </div>

            <a href="#" class="form-button add"><span>Add E-mail Notification</span></a>
          </fieldset>

          <fieldset>
            <table class="data" border="1">
              <thead>
                <tr>
                  <th>Notification Name</th>
                  <th>E-amil Address</th>
                  <th>E-mail Template</th>
                  <th width="150">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><a href="#">Supplier</a></td>
                  <td><a href="#">sales@rubber-mulch.co.uk</a></td>
                  <td><a href="#">Supplier Dispatch</a></td>
                  <td class="actions">
                    <a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp;
                    <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a>
                  </td>
                </tr>
                <tr>
                  <td><a href="#">Administrator</a></td>
                  <td><a href="#">admin@monstermulch.co.uk</a></td>
                  <td><a href="#">New Order</a></td>
                  <td class="actions">
                    <a href="?xxx" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a> &nbsp;
                    <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </fieldset>
        </div>
        <div class="panel" id="returns">
          <h3>Returns</h3>
          <p>Specify whether a product can be returned or not and the conditions for being able to return a product.</p>

          <div class="form-item">
            <label><input type="checkbox" name="accept_returns" id="accept_returns" value="yes" checked="checked" /> Accept Returns?</label><br />
            Can this product be returned? Some products perish or have a software license preventing them from being returned
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item return_period">
            <label for="return_period">Return Period (Days) <span class="required">*</span></label><br />
            <input id="return_period" class="fluid input-text required" type="text" maxlength="255" value="30" name="return_period" /><br />
            How many days does the Customer have to return a defective product?
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="reviews">
          <h3>Reviews</h3>
          <p>Enable or disable reviews for this product including whether a Customer can add a review or not.</p>

          <div class="form-item">
            <label><input type="checkbox" name="customer_reviews_allowed" id="customer_reviews_allowed" value="yes" checked="checked" /> Show Reviews?</label><br />
            Show reviews of this product?
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="customer_reviews_allowed" id="customer_reviews_allowed" value="yes" checked="checked" /> Allow Customer Reviews?</label><br />
            Allow Customers to comment on this product?
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="customer_reviews_allowed" id="customer_reviews_allowed" value="yes" checked="checked" /> Reviews From Customers Who Have Ordered Only?</label><br />
            Allow only Customers who have purchased this product through this website to comment on this product?
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="moderate_ratings_allowed" id="moderate_ratings_allowed" value="yes" checked="checked" /> Moderate Customer Ratings?</label><br />
            Moderate Customer reviews?
            <div class="break"><br /><br /></div>
          </div>

          <div class="form-item">
            <label><input type="checkbox" name="customer_ratings_allowed" id="customer_ratings_allowed" value="yes" checked="checked" /> Allow Customer Ratings?</label><br />
            Allow Customers to rate this product?
            <div class="break"><br /><br /></div>
          </div>
        </div>
        <div class="panel" id="additional_options">
          <h3>Additional Options</h3>
          <p>Set additional options for this product.</p>

          <div class="form-item">
            <label><input type="checkbox" name="customer_ratings_allowed" id="customer_ratings_allowed" value="yes" checked="checked" /> Show Coverage Calculator?</label><br />
            Show a tool allowing Customers to work out the correct quantity to specify for the order?
            <div class="break"><br /><br /></div>
          </div>

          <!--
          <p>Set date available</p>
          <p>Set date to mark as unavailable</p>
          <p>Set whether product is available now or not</p>
          -->
        </div>
        <div class="panel" id="options_available">
          <h3>Options Available</h3>
          <p>Choose the options available for this product and once you have done so you can then edit specific information for each option.</p>
          <fieldset class="form-item">
            <legend>Colour</legend>
            <label><input type="checkbox" name="colour[]" value="1" checked="checked" /> Red</label> &nbsp; <label><input type="checkbox" name="colour[]" value="2" /> Green</label> &nbsp; <label><input type="checkbox" name="colour[]" value="3" /> Blue</label>
          </fieldset>
          <fieldset class="form-item">
            <legend>Weight</legend>
            <label><input type="checkbox" name="weight[]" value="1" checked="checked" /> 50g</label> &nbsp; <label><input type="checkbox" name="weight[]" value="2" /> 100g</label> &nbsp; <label><input type="checkbox" name="weight[]" value="3" /> 150g</label>
          </fieldset>

          <fieldset>
            <table class="data" border="1">
              <thead>
                <tr>
                  <th>Option</th>
                  <th width="150">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><a href="#"><strong>Color:</strong> Red, <strong>Weight:</strong> 50g</a></td>
                  <td class="actions">
                    <a href="?xxx" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    You have not selected any options for this product.
                  </td>
                </tr>
              </tbody>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</form>