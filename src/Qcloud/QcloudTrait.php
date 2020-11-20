<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Qcloud;

trait QcloudTrait
{
    protected $qcloud;

    public function smsSend($to, $message, array $gateways = [])
    {
        return $this->getQcloud()->service('sms')->send($to, $message, $gateways);
    }

    public function imageModeration($param)
    {
        return $this->getQcloud()->service('cms')->ImageModeration($param);
    }

    public function textModeration($content = '')
    {
        return $this->getQcloud()->service('cms')->textModeration($content);
    }

    public function describeAccountBalance()
    {
        return $this->getQcloud()->service('billing')->DescribeAccountBalance();
    }

    public function describeCaptchaResult($ticket, $randStr, $ip = '')
    {
        return $this->getQcloud()->service('captcha')->describeCaptchaResult($ticket, $randStr, $ip);
    }

    public function checkVersion($params = [])
    {
        return $this->getQcloud()->service('discuzcloud')->checkVersion($params);
    }

    public function qcloudReport($params = [])
    {
        return $this->getQcloud()->service('discuzcloud')->qcloudReport($params);
    }

    public function report($params = [])
    {
        return $this->getQcloud()->service('discuzcloud')->report($params);
    }

    public function MsUserInfo()
    {
        return $this->getQcloud()->service('ms')->MsUserInfo();
    }

    private function getQcloud()
    {
        return $this->qcloud ?? $this->qcloud = app('qcloud');
    }

    /*
    |--------------------------------------------------------------------------
    | 云点播
    |--------------------------------------------------------------------------
    */

    public function describeStorageData($sub_app_id)
    {
        return $this->getQcloud()->service('vod')->describeStorageData($sub_app_id);
    }

    public function describeSnapshotByTimeOffsetTemplates($template_id, $qcloudVodSubAppId = null)
    {
        return $this->getQcloud()->service('vod')->describeSnapshotByTimeOffsetTemplates($template_id, $qcloudVodSubAppId);
    }

    public function DescribeTranscodeTemplates($template_id, $qcloudVodSubAppId = null)
    {
        return $this->getQcloud()->service('vod')->describeTranscodeTemplates($template_id, $qcloudVodSubAppId);
    }

    public function describeProcedureTemplates($name, $qcloudVodSubAppId = null)
    {
        return $this->getQcloud()->service('vod')->describeProcedureTemplates($name, $qcloudVodSubAppId);
    }

    // -------------

    public function deleteVodMedia($file_id, $qcloudVodSubAppId = null)
    {
        return $this->getQcloud()->service('vod')->deleteMedia($file_id, $qcloudVodSubAppId);
    }

    public function transcodeVideo($file_id, $taskType, $qcloudVodSubAppId = null)
    {
        return $this->getQcloud()->service('vod')->transcodeVideo($file_id, $taskType, $qcloudVodSubAppId);
    }

    public function describeTaskDetail($task_id, $qcloudVodSubAppId = null)
    {
        return $this->getQcloud()->service('vod')->describeTaskDetail($task_id, $qcloudVodSubAppId);
    }

    public function describeMediaInfos($file_ids, $filters, $qcloudVodSubAppId = null)
    {
        return $this->getQcloud()->service('vod')->describeMediaInfos($file_ids, $filters, $qcloudVodSubAppId);
    }

    public function processMediaByProcedure($file_id, $template_name, $qcloudVodSubAppId = null)
    {
        return $this->getQcloud()->service('vod')->processMediaByProcedure($file_id, $template_name, $qcloudVodSubAppId);
    }

}
