<?php

namespace MySkeleton\Contact;

class Advert extends BaseContactApi
{

    const API_GET_ADVERT = '2/ad/get/';

    public function getAdvert(string $advertiser_id, array $ids = [], array $page = [])
    {
        $param['advertiser_id'] = $advertiser_id;
        $param['filtering']     = ['creative_ids' => $ids];
        $param['fields']        = ["id", "name", "budget", "budget_mode", "status", "opt_status", "open_url", "modify_time", "start_time", "end_time", "bid", "advertiser_id", "pricing", "flow_control_mode", "download_url", "inventory_type", "schedule_type", "app_type", "cpa_bid", "cpa_skip_first_phrase", "audience", "external_url", "package", "campaign_id", "ad_modify_time", "ad_create_time", "audit_reject_reason", "retargeting_type", "retargeting_tags", "convert_id", "interest_tags", "hide_if_converted", "external_actions", "device_type", "auto_extend_enabled", "auto_extend_targets", "dpa_lbs", "dpa_city", "dpa_province", "dpa_recommend_type", "roi_goal", "subscribe_url", "form_id", "form_index", "app_desc", "app_thumbnails"];

        return static::parseJson($this->apiGet(self::API_GET_ADVERT, array_merge($param, $page)));
    }
}
