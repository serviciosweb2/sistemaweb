<?php

class google_analytics_reports extends Google_Service_AnalyticsReporting {
    private $default_view_id;
    
    function get_default_view_id() {
        return $this->default_view_id;
    }

    function set_default_view_id($default_view_id) {
        $this->default_view_id = $default_view_id;
    }

        
    function __construct() {
        $KEY_FILE_LOCATION = APPPATH . 'config/IGA Cloud-googleAPIs.json';

        $VIEW_ID = "122005769";
        $this->set_default_view_id($VIEW_ID);
        
        $client = new Google_Client();
        $client->setApplicationName("IGA Analytics Reporting");
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        parent::__construct($client);
    }
    
    /**
     * 
     * @param string $pais
     * @param string $ciudad
     * @param string $fecha_inicio menor soportada "2005-01-01"
     * @param string $fecha_fin
     * @return array
     */
    function getReportGoalCompletionsAllCountryCity($pais = null, $ciudad = null, $fecha_inicio = "2005-01-01", $fecha_fin = "today") {
        // Create the ReportRequest object.
        $filters = array();
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($fecha_inicio);
        $dateRange->setEndDate($fecha_fin);

        // Create the Metrics object.
        $goalCompletionsAll = new Google_Service_AnalyticsReporting_Metric();
        $goalCompletionsAll->setExpression("ga:goal1Completions");
        $goalCompletionsAll->setAlias("goal1Completions");

        //dimensions
        $country = new Google_Service_AnalyticsReporting_Dimension();
        $country->setName("ga:country");

        $city = new Google_Service_AnalyticsReporting_Dimension();
        $city->setName("ga:city");

        $date = new Google_Service_AnalyticsReporting_Dimension();
        $date->setName("ga:date");
        
        if($pais != null && !empty($pais) && $pais != -1) {
            $pais_filter = new Google_Service_AnalyticsReporting_DimensionFilter();
            $pais_filter->setDimensionName('ga:country');
            $pais_filter->setOperator('EXACT');
            $pais_filter->setExpressions(array($pais));
            $filters[] = $pais_filter;
        }
        
        if($ciudad != null && !empty($ciudad) && $ciudad != -1) {
            $ciudad_filter = new Google_Service_AnalyticsReporting_DimensionFilter();
            $ciudad_filter->setDimensionName('ga:city');
            $ciudad_filter->setOperator('EXACT');
            $ciudad_filter->setExpressions(array($ciudad));
            $filters[] = $ciudad_filter;
        }

        $request->setViewId($this->get_default_view_id());
        $request->setDateRanges($dateRange);
        $request->setMetrics(array($goalCompletionsAll));
        $request->setDimensions(array($country, $city, $date));
        $request->setPageSize(10000);
        
        $filterClause = new Google_Service_AnalyticsReporting_DimensionFilterClause();
        $filterClause->setFilters($filters);
        $filterClause->setOperator('AND');
        $request->setDimensionFilterClauses(array($filterClause));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests(array($request));
        $ret = $this->reports->batchGet($body);
        return $ret;
    }
    
    public function formatear_el_retorno($data, $codigos) {
        $datos = array();
        for ($reportIndex = 0; $reportIndex < count($data); $reportIndex++) {
            $report = $data[$reportIndex];
            $rows = $report->getData()->getRows();

            for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row = $rows[$rowIndex];
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();
                
                if($dimensions[0]=='(not set)' || in_array($dimensions[0], $codigos)) {
                    $datos['data'][$rowIndex]['campana_codigo'] = $dimensions[0]!='(not set)'?(int)$dimensions[0]:null;
                    $datos['data'][$rowIndex]['fecha'] = date("Y-m-d", strtotime($dimensions[1]));
                    $datos['data'][$rowIndex]['envios'] = (int)$metrics[0]->getValues()[0];
                    $datos['data'][$rowIndex]['clics'] = (int)$metrics[0]->getValues()[1];
                }
            }
        }
        
        $total = count($datos['data']);
        
        $datos['draw'] = '1';
        $datos['recordsTotal'] = $total;
        $datos['recordsFiltered'] = $total;
        
        return $datos;
    }
    
    /**
     * 
     * @param array $retorno retorno de la funcción getReportGoalCompletionsAllCountryCity
     * @param string $index
     */
    public function buscaValoresEnRetorno($retorno, $index = '0') {
        $data = array();
        
        foreach ($retorno as $ciudad) {
            if(!in_array($ciudad[$index], $data)) {
                $data[] = $ciudad[$index];
            }
        }
        
        return $data;
    }
    
    function getReportConsecucionesObjetivoCampana($campana_codigo = null, $fecha_inicio = "2005-01-01", $fecha_fin = "today") {
        // Create the ReportRequest object.
        $filters = array();
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($fecha_inicio);
        $dateRange->setEndDate($fecha_fin);

        // Create the Metrics object.
        $goalCompletionsAll = new Google_Service_AnalyticsReporting_Metric();
        $goalCompletionsAll->setExpression("ga:goal1Completions");
        $goalCompletionsAll->setAlias("goal1Completions");
        
        $adClicks = new Google_Service_AnalyticsReporting_Metric();
        $adClicks->setExpression("ga:adClicks");
        $adClicks->setAlias("adClicks");

        //dimensions
        $campaign = new Google_Service_AnalyticsReporting_Dimension();
        $campaign->setName("ga:campaign");

        $adwordsCampaignID = new Google_Service_AnalyticsReporting_Dimension();
        $adwordsCampaignID->setName("ga:adwordsCampaignID");

        $date = new Google_Service_AnalyticsReporting_Dimension();
        $date->setName("ga:date");
        
        if($campana_codigo != null && !empty($campana_codigo) && $campana_codigo != -1) {
            $campaign_filter = new Google_Service_AnalyticsReporting_DimensionFilter();
            $campaign_filter->setDimensionName('ga:adwordsCampaignID');
            $campaign_filter->setOperator('EXACT');
            $campaign_filter->setExpressions(array($campana_codigo));
            $filters[] = $campaign_filter;
        }

        $request->setViewId($this->get_default_view_id());
        $request->setDateRanges($dateRange);
        $request->setMetrics(array($goalCompletionsAll, $adClicks));
        $request->setDimensions(array($adwordsCampaignID, $date));
        $request->setPageSize(10000);
        
        $filterClause = new Google_Service_AnalyticsReporting_DimensionFilterClause();
        $filterClause->setFilters($filters);
        $filterClause->setOperator('AND');
        $request->setDimensionFilterClauses(array($filterClause));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests(array($request));
        $ret = $this->reports->batchGet($body);
        
        return $ret;
    }
    
    function getReportCampaings($fecha_inicio = "2005-01-01", $fecha_fin = "today") {
        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($fecha_inicio);
        $dateRange->setEndDate($fecha_fin);

        // Create the Metrics object.
        $goalCompletionsAll = new Google_Service_AnalyticsReporting_Metric();
        $goalCompletionsAll->setExpression("ga:goal1Completions");
        $goalCompletionsAll->setAlias("goal1Completions");
        
        $adClicks = new Google_Service_AnalyticsReporting_Metric();
        $adClicks->setExpression("ga:adClicks");
        $adClicks->setAlias("adClicks");

        $adwordsCampaignID = new Google_Service_AnalyticsReporting_Dimension();
        $adwordsCampaignID->setName("ga:adwordsCampaignID");
        
        $campaign = new Google_Service_AnalyticsReporting_Dimension();
        $campaign->setName("ga:campaign");

        $request->setViewId($this->get_default_view_id());
        $request->setDateRanges($dateRange);
        $request->setMetrics(array($goalCompletionsAll, $adClicks));
        $request->setDimensions(array($campaign, $adwordsCampaignID));
        $request->setPageSize(10000);

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests(array($request));
        $ret = $this->reports->batchGet($body);
        return $ret;
    }
    
     public function formatear_el_retorno_campanas($data) {
        $datos = array();
        $codigos = array();
        $i = 0;
        for ($reportIndex = 0; $reportIndex < count($data); $reportIndex++) {
            $report = $data[$reportIndex];
            $rows = $report->getData()->getRows();

            for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row = $rows[$rowIndex];
                $dimensions = $row->getDimensions();
                $campanha_nombre = explode('-', $dimensions[0]);
                
                if($dimensions[1]!='(not set)' && !in_array($dimensions[1], $codigos)) {
                    if(isset($campanha_nombre[1]) && !empty($campanha_nombre[1]) && is_numeric($campanha_nombre[1])) {
                        $datos['campanas'][$i]['nombre'] = $campanha_nombre[0];
                        $datos['campanas'][$i]['codigo'] = $dimensions[1];
                        $datos['campanas'][$i]['filial_codigo'] = $campanha_nombre[1];
                        $datos['campanas'][$i]['origen'] = 'google';
                        $datos['codigos'][] = $dimensions[1];
                        $i++;
                    $codigos[] = $dimensions[1];
                    }                    
                }
            }
        }
        
        return $datos;
    }
}
