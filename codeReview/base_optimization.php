<?php

abstract class base_optimization
{
  public $concrete_network_setting;

  /** determines optimization type. has to be set in sub classes*/
  protected $opt_type;

  /** array mit allen Kampagnen die nicht durch die Filter gefallen sind */
  protected $data;

  /** current campaign that is being checked/optimized */
  public $row;/****@@comment  its better to change protected ***/

  /**
   * @var MDB2_Driver_Mysql
   */
  public $db; /****@@comment  its better to change protected ***/

  /** rpc Objects */
  protected $campObj, $campOptimization, $campLandingpages, $contCost;

  /** When did the optimization take place */
  public $optimization_runtime;

  /** Posttracking Settings */
  protected $period = '0';

  protected $group = 'banner';

  protected $type = 'html';

  protected $debugmode = false;

  /**
   * Activates or deactivates debug mode
   *
   * @param boolean $mode
   */
  public function setDebugMode($mode)
  {
    $this->debugmode = $mode;
  }

  public function __construct()
  {
    $this->campLandingpages = adition_base::getInstance('campaignlandingpages');
    $this->campObj = adition_base::getInstance('campaigns');
    $this->campOptimization = adition_base::getInstance('campaignoptimizations');
    $this->contCost = adition_base::getInstance('contentunitcosts');
  }
 
  public function get_posttracking_report()
  {
    $this->getRow(); /****@@comment   getRow should be called as abstract method ****/
    $pages = $this->campLandingpages->getAll(array('campaign_id' => $this->row['campaign_id']));


    $landingpageids = array();
    foreach ($pages as $page) {
      $landingpageids[] = $page['landingpage_id'];
    }

    $today = date('Y-m-d');
    $this->get_start_date_for_post_report();

    $params['campaign_ids'] = array($this->row['campaign_id']);
    $params['landingpage_ids'] = $landingpageids;
    $params['group'] = $this->group;
    $params['type'] = $this->type;
    $params['conversion'] = false;

    $period = null;

    $report = new soapreport();
    $rows = $report->getReport(
      'urn:Adition/Tracking/PostReport2',
      $this->row['postreport_startdate'],
      $today,
      $params,
      $this->row['network_id'],
      $params,
      $period,
      'bw'
    );

    $selected_countingspots = $this->getCountingspots();
    $sums = array();
    $newdata['rows'] = array();
    $newdata['totalsum'] = array();

    if (is_array($rows['days']) && !empty($selected_countingspots)) {
      $index = 0;
	  /****@@comment  
	  		this loop can be avoid if we use table joins
	  ***/ 
      foreach ($rows['days'] as $date => $date_values) {
        $index += 1;
        // LandingpageID
        foreach ($date_values as $landingpage_id => $landingpage_values) {
          $landingpage_data = adition_base:: getInstance('landingpages')->getOne($landingpage_id);
          foreach ($landingpage_values as $countingspot_key => $countingspot_value) {
            // Check whether it is a selective counting spot
            if (@in_array($countingspot_key, $selected_countingspots)) {
              $countingspot_data = adition_base:: getInstance('countingspots')->getOne($countingspot_key);
              foreach ($countingspot_value as $cu_key => $cu_value) {
                $cu_data = adition_base:: getInstance('contentunits')->getOne($cu_key);
                $website = adition_base:: getInstance('websites')->getOne($cu_data['website_id']);
                foreach ($cu_value as $banner_id => $banner_value) {
                  $banner_data = adition_base:: getInstance('banners')->getAll(
                    array('id' => $banner_id, 'with_server' => false)
                  );
                  $banner_data = $banner_data[0];
                  $campaignbanners = adition_base:: getInstance('campaignbanners')->getCampaigns($banner_data['id']);
                  $campaign_id = $campaignbanners[$banner_data['id']][0];

                  //$campaign_costs = adition_base::getInstance('campaigncosts')->getOne($campaign_id);

                  $newrow = array(
                    'date' => $date,
                    'landingpage_name' => $landingpage_data['name'],
                    'trackingspot_name' => $countingspot_data['name'],
                    'contentunit_name' => $cu_data['name'],
                    'contentunit_id' => $cu_data['id'],
                    'website_name' => $website['name'],
                    'website_id' => 'ID_' . $website['id'],
                    'banner_name' => $banner_data['name'],
                    'banner_id' => 'ID_' . $banner_data['id'],
                    'campaign_id' => $campaign_id,
                    'campaign_costs' => ''/* $campaign_costs */,
                  );
                  $newrow['visits_clicks'] = $this->convertNumber($banner_value['visits']['clicks']);
                  $sums[$newrow[$key]]['visits_clicks'] += $newrow['visits_clicks'];
                  $newrow['visits_views'] = $this->convertNumber($banner_value['visits']['views']);
                  $sums[$newrow[$key]]['visits_views'] += $newrow['visits_views'];
                  $newrow['unique_clicks'] = $this->convertNumber($banner_value['unique']['clicks']);
                  $sums[$newrow[$key]]['unique_clicks'] += $newrow['unique_clicks'];
                  $newrow['unique_views'] = $this->convertNumber($banner_value['unique']['views']);
                  $sums[$newrow[$key]]['unique_views'] += $newrow['unique_views'];
                  $newrow['data_clicks'] = $this->convertNumber($banner_value['data']['clicks']);
                  $sums[$newrow[$key]]['data_clicks'] += $newrow['data_clicks'];
                  $newrow['data_views'] = $this->convertNumber($banner_value['data']['views']);
                  $sums[$newrow[$key]]['data_views'] += $newrow['data_views'];
                  $newdata['rows'][] = $newrow;
                }
              }
            }
          }
        }
      }
    }
    foreach ($sums as $sum) {
      foreach ($sum as $key => $value) {
        $totalsum[$key] += $value;
      }
    }
    $this->row['cpl_by_contentunit'] = $newdata['rows'];
    $this->row['cpl'] = $totalsum['unique_clicks'] + $totalsum['unique_views'];
  }
  protected function convertNumber($a)
  {
    if (!isset($a) or $a == null) {
      $a = 0;
    }
    return $a;
  }

  protected function replaceCampaign()
  {
    try {
      $this->set_priority_if_not_in_network_range();
      $this->row['datetime'] = true;

      if (!$this->debugmode) {
        if (isset($this->row['campaign_data']['priority_hacked'])) {
          $this->row['campaign_data']['weight'] = $this->row['campaign_data']['priority'];
          $this->row['campaign_data']['priority'] = $this->row['campaign_data']['priority_hacked'];
          unset($this->row['campaign_data']['priority_hacked']);
        }

        foreach (array('priority', 'weight') as $keyToBeCasted) {
          if (isset($this->row['campaign_data'][$keyToBeCasted])) {
            $this->row['campaign_data'][$keyToBeCasted] = (int)$this->row['campaign_data'][$keyToBeCasted];
          }
        }

        $this->campObj->replaceObj($this->row['campaign_data']);

        // Because the database field `object` is too small,
        // only relevant data should be saved into the `campaignoptimizationhistory` table
		
		/****@@comment   
		Write a new class for log and pass the entire row
		***/
        $data = array(
          'order_name' => $this->row['order_name'],
          'lastrun' => $this->row['lastrun'],
          'company_name' => $this->row['company_name'],
          'concrete_cost' => $this->row['concrete_cost'],
          'type' => $this->row['type'],
          'costs' => $this->row['costs'],
          'costtype' => $this->row['costtype'],
          'ecpm' => $this->row['ecpm'],
          'new_priority' => $this->row['new_priority'],
          'old_priority' => $this->row['old_priority'],
          'calc_priority' => $this->row['calc_priority'],
          'campaign_data' => array(
            'id' => $this->row['campaign_data']['id'],
            'priority' => $this->row['campaign_data']['priority'],
            'order_id' => $this->row['campaign_data']['order_id'],
            'name' => $this->row['campaign_data']['name'],
            'type' => $this->row['campaign_data']['type'],
            'views_total' => $this->row['campaign_data']['views_total'],
            'clicks_total' => $this->row['campaign_data']['clicks_total']
          ),
          'report' => array(
            'views' => $this->row['report']['views'],
            'clicks' => $this->row['report']['clicks']
          ),
          'options' => array(
            'ecpm' => array(
              'costSelect' => $this->row['options']['ecpm']['costSelect']
            )
          ),
          'network_id' => $this->row['network_id'],
          'datetime' => $this->row['datetime'],
          'log_msg' => $this->row['log_msg']
        );
        $this->writeLog($data);

        $this->post_replace_data();
      }
    } catch (Exception $e) {
      error_log($e->getMessage());
    }
  }
 /****@@comment   log can be moved to a seprate class ***/
  protected function writeLog($data)
  {
    $logMsg = !empty($data['log_msg']) ? serialize($data['log_msg']) : null;
    $lastrun = $data['datetime'] ? $this->optimization_runtime : null;

    # Fixme go to rpc module
	// @comment database layer should be keep seprately
    $query = sprintf(
      'INSERT INTO campaignoptimizationhistory ( `order_id`, `network_id`, `campaign_id`, `messages`, `object`, `lastrun`)
      VALUES (%s, %s, %s, %s, %s, %s)',
      $this->db->quote($data['campaign_data']['order_id'], 'integer'),
      $this->db->quote($data['network_id'], 'integer'),
      $this->db->quote($data['campaign_data']['id'], 'integer'),
      $this->db->quote($logMsg, 'text'),
      $this->db->quote(serialize($data), 'text'),
      $this->db->quote($lastrun, 'timestamp')
    );

    $result = $this->db->exec($query);
    rpcMiddlewareStorageException::throwForMdb2Error($result);
  }

  # overwritten me in subclass
  protected function set_priority_if_not_in_network_range()
  {
    ;
  }

  # overwrite me in subclass
  protected function post_replace_data()
  {
    ;
  }

  protected function get_start_date_for_post_report()
  {
    if ($this->concrete_network_setting['runtime'] == 'lastrun') {
      if (strtotime($this->row['lastrun']) <= time() - (adReportConst::getReportConfig()->getSoapReportMaxDays() * 24 * 3600)) {
        $this->row['postreport_startdate'] = date('Y-m-d', time() - (adReportConst::getReportConfig()->getSoapReportMaxDays() * 24 * 3600));
      } else {
        $this->row['postreport_startdate'] = $this->row['lastrun'];
      }
    }
    if ($this->concrete_network_setting['runtime'] == 'campaign') {
      if (strtotime($this->row['runtimes'][sizeof($this->row['runtimes']) - 1]['startdate']) <= time() - (adReportConst::getReportConfig()->getSoapReportMaxDays() * 24 * 3600)) {
        $this->row['postreport_startdate'] = date('Y-m-d', time() - (adReportConst::getReportConfig()->getSoapReportMaxDays() * 24 * 3600));
      } else {
        $this->row['postreport_startdate'] = $this->row['runtimes'][sizeof($this->row['runtimes']) - 1]['startdate'];
      }
    }
  }
}

?>
