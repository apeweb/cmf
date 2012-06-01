<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>

<form method="post" action="">
  <div id="main_content">
    <div class="intro">
      <table>
        <tbody>
          <tr>
            <td style="width:50%;">
              <h1 class="package">Castrol GTX High Mileage</h1>
            </td>
            <td align="right">
              <a href="/admin/campaigns" class="form-button back"><span>Back</span></a>
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
</form>