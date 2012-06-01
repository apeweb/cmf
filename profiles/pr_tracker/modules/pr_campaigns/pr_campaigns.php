<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Pr_Campaigns extends Model {
  const Prepared_Statement_Library = 'pr_campaigns_prepared_statement_library';

  public static function getAllCampaigns ($active = TRUE) {
    Assert::isBoolean($active);

    $campaigns = array();

    $query = Cmf_Database::call('pr_campaigns_get_all', self::Prepared_Statement_Library);
    $query->bindValue(':prtc_active', $active);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $campaigns[] = new Pr_Campaign($row);
    }

    return $campaigns;
  }
}

?>