<?php

namespace MySkeleton\Contact;

class Creative extends BaseContactApi
{

    const API_GET_CREATIVE = '2/creative/get/';

    public function getCreative(string $advertiser_id, array $ids = [], array $page = [])
    {
        $param['advertiser_id'] = $advertiser_id;
        $param['filtering']     = ['creative_ids' => $ids];
        $param['fields']        = ["creative_id", "ad_id", "advertiser_id", "status","opt_status", "image_mode", "title", "creative_word_ids","third_party_id", "image_ids", "image_id", "video_id","materials"];

        return static::parseJson($this->apiGet(self::API_GET_CREATIVE, array_merge($param, $page)));
    }
}
