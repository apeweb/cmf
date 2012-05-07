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
  <div id="main_content" class="admin_settings clearfix">
    <div class="intro">
      <table>
        <tbody>
          <tr>
            <td style="width:50%;">
              <h1 class="package">Settings</h1>
            </td>
            <td align="right">
              &nbsp;
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="left clearfix">
      <div class="admin-panel">
        <h3>Products</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/config/system/site-information">Product Types</a><br />
            Manage the the types of product you offer and their settings.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Product Sashes</a><br />
            Manage the sashes to display with product images.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Product Attributes</a><br />
            Manage the list of languages and translations for phrases.
          </li>
        </ul>
      </div>

      <div class="admin-panel">
        <h3>Inventory</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/config/system/site-information">Stock Control</a><br />
            Manage stock level settings and safety stock.
          </li>
        </ul>
      </div>

      <div class="admin-panel">
        <h3>E-mail Notifications</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/config/system/site-information">Order Notifications</a><br />
            Manage the notifications that are sent out when an order is placed.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Order Notification Templates</a><br />
            Manage the templates for order notifications.
          </li>
        </ul>
      </div>

      <div class="admin-panel">
        <h3>Promotions</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/pages/">Discounts</a><br />
            View or edit existing discounts and add new discounts.
            <br />(xxx sales, bulk-buy and early-bird discounts)
          </li>
          <li>
            <a href="/admin/pages/">Coupons</a><br />
            View or edit existing coupons and add new coupons.
          </li>
        </ul>
      </div>

      <div class="admin-panel">
        <h3>Pricing</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/prices/price_labels/">Price Labels</a><br />
            Manage the price labels available for products and their settings.
          </li>
        </ul>
      </div>

      <div class="admin-panel">
        <h3>Shipping</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/prices/price_labels/">Shipping Rates</a><br />
            Define how and what to charge for shipping.
          </li>
        </ul>
      </div>

      <div class="admin-panel">
        <h3>Tax</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/config/system/site-information">Tax Settings</a><br />
            Enable or disable tax.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Country Tax Rates</a><br />
            Manage the tax rates for each country.
          </li>
        </ul>
      </div>
    </div>
    <div class="right clearfix">
      <div class="admin-panel">
        <h3>Internationalisation</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/config/i18n/countries">Countries</a><br />
            Manage the list of countries and country specific settings.
          </li>
          <li>
            <a href="/admin/config/i18n/currencies">Currencies</a><br />
            Manage the list of currencies available and exchange rate settings.
          </li>
          <li>
            <a href="/admin/config/i18n/languages">Languages</a><br />
            Manage the list of languages and translations for phrases.
          </li>
        </ul>
      </div>

      <div class="admin-panel">
        <h3>System</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/config/system/site-information">Site information</a><br />
            Change the site name, logo, e-mail address, slogan, and error pages.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Statistics</a><br />
            Control what usage and performance data is logged.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Scheduled Tasks</a><br />
            Manage automatic site maintenance tasks.
          </li>
        </ul>
      </div>

      <div class="admin-panel">
        <h3>Users</h3>
        <ul class="admin-list">
          <li>
            <a href="/admin/config/system/site-information">Accounts</a><br />
            Manage user accounts and settings for individual users.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Groups</a><br />
            Manage the groups users can be a member of.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Roles</a><br />
            Manage the roles users and groups can be permitted.
          </li>
          <li>
            <a href="/admin/config/system/site-information">Access</a><br />
            Configure default behavior of users, including registration requirements.
            <br />(xxx this is where the anonymouse and admin groups are set)
          </li>
          <li>
            <a href="/admin/config/system/site-information">User Profiles</a><br />
            Configure which information is collected about users and user profile picture settings.
          </li>
        </ul>
      </div>
    </div>
  </div>
</form>