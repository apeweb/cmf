<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaigns_Prepared_Statement_Library_Mysql {
  const PR_CAMPAIGNS_GET_ALL = "
    SELECT
      c1.prtc_id AS id,
      c1.prtc_name AS name,
      c1.prtc_date_started AS date_started,
      cs1.prtcs_name AS status
      FROM pr_tracker_campaigns c1
      LEFT JOIN pr_tracker_campaign_status cs1 ON c1.prtcs_id = cs1.prtcs_id AND cs1.prtcs_active = 1 AND cs1.prtcs_deleted = 0
      WHERE c1.s_id = :s_id
      AND prtc_active = :prtc_active
      AND prtc_deleted = '0'
  ";

  const PR_CAMPAIGNS_GET_CAMPAIGN = "
    SELECT
      c1.prtc_id AS id,
      c1.prtc_name AS name,
      c1.prtc_date_started AS date_started,
      cs1.prtcs_name AS status
      FROM pr_tracker_campaigns c1
      LEFT JOIN pr_tracker_campaign_status cs1 ON c1.prtcs_id = cs1.prtcs_id
        AND cs1.prtcs_active = :prtcs_active
        AND cs1.prtcs_deleted = '0'
        AND cs1.s_id = :s_id
      WHERE c1.s_id = :s_id
      AND prtc_id = :prtc_id
      AND prtc_active = :prtc_active
      AND prtc_deleted = '0'
  ";

  const PR_CAMPAIGNS_CAMPAIGN_EXISTS = "
    SELECT COUNT(*)
      FROM pr_tracker_campaigns
      WHERE s_id = :s_id
      AND prtc_id = :prtc_id
      AND prtc_deleted = '0'
      LIMIT 1
  ";

  const PR_CAMPAIGNS_UPDATE_CAMPAIGN = "
    UPDATE pr_tracker_campaigns
      SET prtc_name = :prtc_name,
      prtc_date_started = :prtc_date_started,
      prtc_active = :prtc_active
      WHERE s_id = :s_id
      AND prtc_id = :prtc_id
      AND prtc_deleted = '0'
      LIMIT 1
  ";

  const PR_CAMPAIGNS_DELETE_CAMPAIGN = "
    UPDATE pr_tracker_campaigns
      SET prtc_deleted = '1'
      WHERE s_id = :s_id
      AND prtc_id = :prtc_id
      AND prtc_deleted = '0'
      LIMIT 1
  ";

  const PR_CAMPAIGNS_ADD_CAMPAIGN = "
    INSERT INTO pr_tracker_campaigns

  ";
}

?>
