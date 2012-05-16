<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Toolbar {
  public static function install () {
    // Config settings
    //Config::setValue(CMF_REGISTRY, 'admin', 'toolbar', 'setting_name', 'setting_value');
  }

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::modifyContent, __CLASS__ . '::buildTextLinks');
    Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::modifyContent, __CLASS__ . '::buildNavigationBar');
  }

  // xxx need to be site & user specific
  public static function buildTextLinks () {
    // $links = new Cmf_Navigation_Bar_Control;
    View_Data::setValue('text_links', '<a href="/admin/">Dashboard</a> | <a href="/">Homepage</a> | <a href="/admin/settings/">Settings</a> | <a href="/admin/tools/">Tools</a> | <a href="?action=logout">Log Out</a>');
  }

  // xxx need to be site & user specific
  public static function buildNavigationBar () {
    // $links = new Cmf_Navigation_Bar_Control;
    // $links->renderCallback = 'Admin_Controls::tabbedMenuLinks';

    View_Data::setValue('navigation_bar', '
      <ul>
        <li class="hover">
          <a><span class="folder_table">Transactions</span></a>
          <ul class="drop_down">
            <li>
              <a href="/admin/orders/" class="creditcards" title="View all orders">
                <strong>Orders</strong>
              </a>
            </li>
            <li>
              <a href="/admin/returns/" class="folder_table" title="View all returns">
                <strong>Returns</strong>
              </a>
            </li>
          </ul>
        </li>
        <li class="hover active-trail">
          <a><span class="package">Catalogue</span></a>
          <ul class="drop_down">
            <li>
              <a href="/admin/products/" class="package" title="View or edit your existing products and add new products">
                <strong>Products</strong>
              </a>
            </li>
            <li>
              <a href="/admin/products/categories/" class="folder_database" title="View or edit your existing product categories and add new product categories">
                <strong>Product Categories</strong>
              </a>
            </li>
            <li>
              <a href="/admin/products/reviews/" title="View or edit customer product reviews">
                <strong>Product Reviews (1)</strong> <!-- xxx (1) is the number of new reviews that need to be approved -->
              </a>
            </li>
          </ul>
        </li>
        <li class="hover">
          <a><span class="group">Customers</span></a>
          <ul class="drop_down">
            <li>
              <a href="/admin/customers/" class="group" title="View all Customers and Customer details">
                <strong>Customers</strong>
              </a>
            </li>
          </ul>
        </li>
        <li class="hover">
          <a><span class="layout">Content</span></a>
          <ul class="drop_down">
            <li>
              <a href="/admin/pages/" class="group" title="View all pages and edit content">
                <strong>Pages</strong>
              </a>
            </li>
          </ul>
        </li>
        <li class="hover">
          <a><span class="chart_pie">Reports</span></a>
          <ul class="drop_down">
            <li>
              <a href="/admin/pages/" class="group" title="View all sales and performance data">
                <strong>Sales Report</strong>
              </a>
            </li>
            <li>
              <a href="/admin/pages/" class="group" title="View all customer data">
                <strong>Customer Report</strong>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    ');
  }
}

?>