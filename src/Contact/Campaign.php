<?php

namespace MySkeleton\Contact;

class Campaign extends BaseContactApi
{

    const API_GET_CAMPAIGN = '2/campaign/get/';

    public function getCampaign(string $advertiser_id, array $ids = [], array $page = [])
    {
        $param['advertiser_id'] = $advertiser_id;
        $param['filtering']     = ['ids' => $ids];
        $param['fields']        = ["id", "name", "budget", "budget_mode", "landing_type", "status", "modify_time",
            "status", "modify_time", "campaign_modify_time", "campaign_create_time"];

        return static::parseJson($this->apiGet(self::API_GET_CAMPAIGN, array_merge($param, $page)));
    }
}
