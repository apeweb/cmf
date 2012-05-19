<?php

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
          <li class="active"><a href="#listing_type"><span>Listing Type</span></a></li>
        </ul>
      </div>
      <div class="left flexi_column clearfix">
        <div class="intro">
          <table>
            <tbody>
              <tr>
                <td style="width:50%;">
                  <h1 class="package">Add a Product</h1>
                </td>
                <td align="right">
                  <a href="/admin/products/" class="form-button back"><span>Back</span></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="panel active" id="listing_type">
          <p>To add a product, you first need to choose the type of listing you wish to add:</p>
          <dl class="options">
            <dt><a href="/admin/products/add/new/kind/tangible_product/">Tangible Product</a></dt>
            <dd>A tangible product that requires shipping, such as clothing or hardware.</dd>
            <dt><a href="/admin/products/add/new/kind/product_bundle/">Product Bundle</a></dt>
            <dd>A combination of products marketed as one product, such as a toy with batteries.</dd>
            <!--
            <dt><a href="#">Product Subscription</a></dt>
            <dd>Something physical you will deliver on a periodical basis, such as a monthly magazine subscription.</dd>
            <dt><a href="#">A Service</a></dt>
            <dd>A service that will be delivered in person or digitally, such as access to a member-only area of a website.</dd>
            <dt><a href="#">Digital Download</a></dt>
            <dd>A downloadable file, such as software or an e-book.</dd>
            <dt><a href="#">Credit</a></dt>
            <dd>Credit prior to receiving a product or service, such as a gift voucher that can be redeemed on this website.</dd>
            -->
          </dl>
        </div>
      </div>
    </div>
  </div>
</form>