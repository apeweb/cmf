<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load('visualization', '1', {packages: ['corechart']});
</script>
<script type="text/javascript">
  function drawOverviewVisualization() {
    // Create and populate the data table.
    var data = google.visualization.arrayToDataTable([
      ['Month', 'Quality', 'Relevance'],
      ['Jan',   8,         6],
      ['Feb',   7.4,       5],
      ['Mar',   4,         6.6],
      ['Apr',   7,         6.5],
      ['May',   7,         6.4],
      ['Jun',   7.3,       6.4],
      ['Jul',   7.8,       6],
      ['Aug',   8.1,       5.5],
      ['Sep',   8.3,       5],
      ['Oct',   7.5,       6],
      ['Nov',   7.7,       6.6],
      ['Dec',   8,         7]
    ]);

    // Create and draw the visualization.
    new google.visualization.LineChart(document.getElementById('overview')).
        draw(data, {curveType: "function",
                    height: 300,
                    vAxis: {maxValue: 10},
                    backgroundColor: 'transparent',
                    theme: {chartArea: {width: '84%', top:10, left:20}}
                   }
            );
  }

  function drawMatchesVisualization() {
    // Create and populate the data table.
    var data = google.visualization.arrayToDataTable([
      ['Month', 'Page Matches', 'Site Matches'],
      ['Jan',   10, 8],
      ['Feb',   15, 14],
      ['Mar',   25, 24],
      ['Apr',   24, 20],
      ['May',   26, 23],
      ['Jun',   28, 25],
      ['Jul',   24, 20],
      ['Aug',   26, 21],
      ['Sep',   30, 30],
      ['Oct',   28, 26],
      ['Nov',   20, 18],
      ['Dec',   16, 14]
    ]);

    // Create and draw the visualization.
    new google.visualization.LineChart(document.getElementById('matches')).
        draw(data, {curveType: "function",
                    height: 300,
                    vAxis: {maxValue: 14},
                    backgroundColor: 'transparent',
                    theme: {chartArea: {width: '78%', top:10, left:20}}
                   }
            );
  }

  window.onresize = function(event) {
    drawOverviewVisualization();
    drawMatchesVisualization();
  };
 
  google.setOnLoadCallback(drawOverviewVisualization);
  google.setOnLoadCallback(drawMatchesVisualization);
</script>

<form method="post" action="">
  <div id="main_content">
    <div class="intro">
      <table>
        <tbody>
          <tr>
            <td style="width:50%;">
              <h1 class="package">Castrol Sponsors Major League Soccer Report</h1>
            </td>
            <td align="right">
              Jan 2011 - Dec 2011 <a href="">change</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>


    <div class="chart_wrapper">
      <h2>Overview</h2>
      <div class="chart" id="overview" style="float:left; width:50%; position:relative; height:230px; overflow:hidden"></div>
      <div class="chart" id="matches" style="float:left; width:50%; position:relative; height:230px; overflow:hidden"></div>
    </div>

    <h2>Matches</h2>
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
            <td><a href="/admin/monitors/match/1">eon.businesswire.com/news/eon/20100510005316/en</a></td>
            <td><a href="/admin/monitors/match/1">56%</a></td>
            <td><a href="/admin/monitors/match/1">79%</a></td>
            <td><a href="/admin/monitors/match/1">US</a></td>
            <td><a href="/admin/monitors/match/1">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/1">High</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/2">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
            <td><a href="/admin/monitors/match/2">69%</a></td>
            <td><a href="/admin/monitors/match/2">88%</a></td>
            <td><a href="/admin/monitors/match/2">US</a></td>
            <td><a href="/admin/monitors/match/2">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/2">Some</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/3">eon.businesswire.com/news/eon/20100510005316/en</a></td>
            <td><a href="/admin/monitors/match/3">56%</a></td>
            <td><a href="/admin/monitors/match/3">79%</a></td>
            <td><a href="/admin/monitors/match/3">US</a></td>
            <td><a href="/admin/monitors/match/3">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/3">High</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/4">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
            <td><a href="/admin/monitors/match/4">69%</a></td>
            <td><a href="/admin/monitors/match/4">88%</a></td>
            <td><a href="/admin/monitors/match/4">US</a></td>
            <td><a href="/admin/monitors/match/4">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/4">Some</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/5">eon.businesswire.com/news/eon/20100510005316/en</a></td>
            <td><a href="/admin/monitors/match/5">56%</a></td>
            <td><a href="/admin/monitors/match/5">79%</a></td>
            <td><a href="/admin/monitors/match/5">US</a></td>
            <td><a href="/admin/monitors/match/5">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/5">High</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/6">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
            <td><a href="/admin/monitors/match/6">69%</a></td>
            <td><a href="/admin/monitors/match/6">88%</a></td>
            <td><a href="/admin/monitors/match/6">US</a></td>
            <td><a href="/admin/monitors/match/6">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/6">Some</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/7">eon.businesswire.com/news/eon/20100510005316/en</a></td>
            <td><a href="/admin/monitors/match/7">56%</a></td>
            <td><a href="/admin/monitors/match/7">79%</a></td>
            <td><a href="/admin/monitors/match/7">US</a></td>
            <td><a href="/admin/monitors/match/7">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/7">High</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/8">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
            <td><a href="/admin/monitors/match/8">69%</a></td>
            <td><a href="/admin/monitors/match/8">88%</a></td>
            <td><a href="/admin/monitors/match/8">US</a></td>
            <td><a href="/admin/monitors/match/8">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/8">Some</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/9">eon.businesswire.com/news/eon/20100510005316/en</a></td>
            <td><a href="/admin/monitors/match/9">56%</a></td>
            <td><a href="/admin/monitors/match/9">79%</a></td>
            <td><a href="/admin/monitors/match/9">US</a></td>
            <td><a href="/admin/monitors/match/9">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/9">High</a></td>
          </tr>
          <tr>
            <td><a href="/admin/monitors/match/10">www.nysportsjournalism.com/castrol-scores-deal-51110/</a></td>
            <td><a href="/admin/monitors/match/10">69%</a></td>
            <td><a href="/admin/monitors/match/10">88%</a></td>
            <td><a href="/admin/monitors/match/10">US</a></td>
            <td><a href="/admin/monitors/match/10">2012-05-24</a></td>
            <td><a href="/admin/monitors/match/10">Some</a></td>
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
            
          </td>
        </tr>
      </tbody>
    </table>


    <div class="left">

    </div>
    <div class="right">

    </div>
  </div>
</form>