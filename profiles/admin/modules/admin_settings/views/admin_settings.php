<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

?>
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

    <div class="admin-panel">
      <h3>E-mail Notifications</h3>
      <ul class="admin-list">
        <li>
          <a href="/admin/config/system/site-information">Notifications</a><br />
          Manage the notifications that are sent out when a page is found.
        </li>
        <li>
          <a href="/admin/config/system/site-information">Notification Templates</a><br />
          Manage the templates for notifications.
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
          <a href="/admin/config/system/statistics">Statistics</a><br />
          Control what usage and performance data is logged.
        </li>
        <li>
          <a href="/admin/config/system/scheduled-tasks">Scheduled Tasks</a><br />
          Manage automatic site maintenance tasks.
        </li>
        <li>
          <a href="/admin/menus">Manage Menus</a><br />
          Configure menus and specify the links that appear.
        </li>
        <li>
          <a href="/admin/config/system/registry">Registry</a><br />
          Edit low level system component settings.
        </li>
      </ul>
    </div>
  </div>
</div>