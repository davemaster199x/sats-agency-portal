<?php

class Alarm_job_type_model extends MY_Model
{
    public $table = 'alarm_job_type'; // you MUST mention the table name
    public $primary_key = 'id'; // you MUST mention the primary key
    
    
    // ...Or you can set an array with the fields that cannot be filled by insert/update
    public $protected = [
        'id'
    ];
    
    function __construct()
    {
        $this->has_many['job'] = array(
            'foreign_model'=>'Jobs_model',
            'foreign_table'=>'jobs',
            'foreign_key'=>'job_id',
            'local_key'=>'id'
        );
        
        $this->load->driver('cache');
        
        parent::__construct();
    }
    
    const SMOKE_ALARM_IDS = [2,12,32,50];
    
    const SAFETY_SWITCH_VIEW_ONLY_IDS = [3];
    
    const SAFETY_SWITCH_IDS = [5];
    const CORDED_WINDOWS_IDS = [6];
    const WATER_METER_IDS = [7];
    const WATER_EFFICIENCY_IDS = [15];
    
    public static function icons($ajt_id,$custom_title=null)
    {
        if(empty($ajt_id)){
			log_message('error', 'Alarm_job_type_model::icons invalid parameters: ' . $ajt_id);
			return '';
        }

		$data = [];
        $cache_name = 'service-icons';
        $cache_ttl = 604800;
        
        $self = new self();
        if ( !$data = $self->cache->get($cache_name) ) {
            $services = $self->get_all();
            foreach($services as $service){
                
                $service_title = ( $custom_title != '' )?$custom_title:$service->full_name; // custom title
                
                $icons = explode('-',$service->html_id);
                $html = '<span class="service-icons" data-toggle="tooltip" title="' . $service_title . '">';
                foreach($icons as $icon){
                    $html .= '<img src="/images/icons-services/' . $icon . '.png" alt="' . $icon . '" />';
                }
                $html .= '</span>';
                $data[$service->id] = $html;
            }
            $self->cache->save($cache_name, $data, $cache_ttl);
        }
        return $data[$ajt_id];
    }
}