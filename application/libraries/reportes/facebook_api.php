<?php

/**
 * Description of facebook_api
 *
 * @author romario
 */
class facebook_api{
    public $cuenta;
    public $pages;

    public function __construct() {
        \FacebookAds\Api::init(
            '621828947999590', // App ID
            'd443ab207c7751c9feedd4f998813c47', // App secret
            'EAAI1jNWOe2YBAFIZBjYjnMHChPLymoE5NewlgimuZBcxuTYDsIjFBqGZA73YBZBZAOaNF42UmKv6Ca6LfbJmYrLQj8O8CHfxH61JMW8ZCGPnd7lX2fku0lgGukDfuN8fiZAjY5Snej9ZAZBG3OOvJMhF2FkRVS0JhpUJiAsC0NnHTFzoBUH8NeN4E' // Acess token
        );
        $this->pages = array(
            '101092503282863',//latinoamerica
            '170631959742675',//brasil
            '423123044539220'//usa
        );
        $this->cuenta = '107344722768577';
    }
    
    public function getAllFacebookCampaigns($fecha = null) {
        $account = new FacebookAds\Object\AdAccount('act_' . $this->cuenta);
        $datetime = new DateTime($fecha);
        $datetime->sub(new DateInterval('P2D'));

        $params = array(
            \FacebookAds\Object\Fields\CampaignFields::EFFECTIVE_STATUS => array(
                \FacebookAds\Object\Campaign::STATUS_ACTIVE
            ),
            'limit' => 1000
        );
        $campa = $account->getCampaigns(array(
            \FacebookAds\Object\Fields\CampaignFields::NAME,
            \FacebookAds\Object\Fields\CampaignFields::ID,
            \FacebookAds\Object\Fields\CampaignFields::STATUS,
            \FacebookAds\Object\Fields\CampaignFields::STOP_TIME
        ), $params);

        $ret = array();
        foreach($campa as $cam){
            $nombre_filial = explode('-', $cam->{\FacebookAds\Object\Fields\CampaignFields::NAME});

            //verificar se hay el codigo de la filial en el nombre de la campaña y para que no retorne campanas que ya terminaram
            if(isset($nombre_filial[1]) && !empty($nombre_filial[1]) && is_numeric($nombre_filial[1]) && ($cam->{\FacebookAds\Object\Fields\CampaignFields::STOP_TIME} == null || (new DateTime($cam->{\FacebookAds\Object\Fields\CampaignFields::STOP_TIME})) > $datetime)) {
                $data = array(
                    'codigo' => $cam->{\FacebookAds\Object\Fields\CampaignFields::ID},
                    'nombre' => $nombre_filial[0],
                    'filial_codigo' => $nombre_filial[1],
                    'origen' => 'facebook',
                    'status' => $cam->{\FacebookAds\Object\Fields\CampaignFields::STATUS},
                    'stop_time' => $cam->{\FacebookAds\Object\Fields\CampaignFields::STOP_TIME}
                );

                $ret[] = $data;
            }
        }

        return $ret;
    }
    
    public function getAllFacebookCampaignsDataByDate($campanas, $fecha = null) {
        $ret = array();
        
        foreach ($campanas as $key => $campana) {
            $campaign = new \FacebookAds\Object\Campaign($campana['codigo']);

            $columnas = array(
                FacebookAds\Object\Fields\AdsInsightsFields::CAMPAIGN_ID,
                FacebookAds\Object\Fields\AdsInsightsFields::CAMPAIGN_NAME,
                FacebookAds\Object\Fields\AdsInsightsFields::REACH,
                FacebookAds\Object\Fields\AdsInsightsFields::OBJECTIVE,
                FacebookAds\Object\Fields\AdsInsightsFields::ACTIONS
            );
            
            $params = array(
                'time_increment' => '1',
                'limit' => '1000'
            );
            
            if($fecha == null) {
                $params['date_preset'] = FacebookAds\Object\Values\AdsInsightsDatePresetValues::LIFETIME;
            }
            else {
                $params['time_range'] = array('since' => $fecha, 'until' => date('Y-m-d'));
            }

            $in = $campaign->getInsights($columnas, $params);
            
            $datos = $in->getResponse()->getContent();

            if(isset($datos['data']) && !empty($datos['data'])) {
                foreach ($datos['data'] as $key2 => $dato) {

                    //checkeando
                    if($dato['objective'] == 'LEAD_GENERATION') {
                        $action_type = 'leadgen.other';
                    }
                    else if($dato['objective'] == 'CONVERSIONS') {
                        $action_type = 'offsite_conversion';
                    }

                    $results = 0;
                    if(isset($dato['actions']) && !empty($dato['actions'])) {
                        foreach ($dato['actions'] as $key3 => $actions) {
                            if($actions['action_type'] == $action_type) {
                                $results = $actions['value'];
                                break; 
                            }
                        }
                    }

                    $ret[] = array(
                        'alcance' => $dato['reach'],
                        'resultados' => $results,
                        'fecha' => $dato['date_start'],
                        'campana_codigo' => $campana['codigo'],
                        'tipo_resultado' => $dato['objective']
                    );
                }
            }
        }

        return $ret;
    }

    public function getFacebookLeadsByDate($date) {
        $datos = array();
        if(!empty($date)) {
            $date = strtotime($date.' -3 hours');
            $params = array('filtering' => array(array('field' => 'time_created', 'operator' => 'GREATER_THAN', 'value' => $date)), 'limit' => 1000);
        }
        else {
            $params = array();
        }

        foreach ($this->pages as $igapage) {
            $page = new \FacebookAds\Object\Page($igapage);
            $leadgen_forms = $page->getLeadgenForms(array(), array('limit' => 1000));
            foreach ($leadgen_forms as $leadgen_form) {
                //busca curso y filial
                $id_curso = 1;
                $cursos = array(1);
                $form_datos = $leadgen_form->getData();
                $filial = explode('-', $form_datos['name']);//Form intensivo GRU - id_filial
                if(isset($filial[1]) && is_numeric($filial[1])) {
                    $id_filial = $filial[1];
                    if(isset($filial[2])) {
                        $cursos = explode(',', $filial[2]);
                        $id_curso = $cursos[0];
                    }
                    //busca consultas web
                    $form_leads = $leadgen_form->getLeads(array(), $params);
                    foreach($form_leads as $lead){
                        $dato = $lead->getData();
                        $dato['id_filial'] = $id_filial;
                        $dato['id_curso'] = $id_curso;
                        $dato['cursos'] = $cursos;
                        foreach ($dato['field_data'] as $data) {
                            if($data['name'] == 'email'){
                                $dato['email'] = $data['values'][0];
                            }
                            else if($data['name'] == 'phone_number'){
                                $dato['telefono'] = $data['values'][0];
                            }
                            else if($data['name'] == 'full_name'){
                                $dato['nombre'] = $data['values'][0];
                            }
                        }
                        $datos[] = $dato;
                    }
                }
                else {
                    echo $form_datos['name'].'<br>';
                }
            }
        }
        return $datos;
    }
}
