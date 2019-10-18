<?php

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: Censor.php xxx 2019-10-15 16:47:00 LiuDongdong $
 */

namespace Discuz\Censor;

use App\Models\StopWord;
use Discuz\Contracts\Setting\SettingRepository;

class Censor
{
    public $config = 'default';

    public $isMod = false;

    public $wordBanned = [];

    public $wordMod = [];

    public $wordReplace = [];

    private $isTurnOff;

    public function __construct(SettingRepository $setting)
    {
        // 加载设置
        // dump($setting->all());
    }

    public function check($content, $type = 'ugc') {
        // 设置关闭时，直接返回原内容
        // if ($this->isTurnOff) {
        //     return $content;
        // }

        StopWord::orderBy($type)->cursor()->tapEach(function ($word) use ($content, $type) {
            $find = '/' . addcslashes($word->find, '/') . '/i';
            // dump($find);
            if ($word->{$type} == '{REPLACE}') {
                preg_replace($find, $word->replacement, $content);
            } else {
                if ($word->{$type} == '{MOD}') {
                    if (preg_match($find, $content, $matches)) {
                        $this->isMod = true;
                    }
                } else if ($word->{$type} == '{BANNED}') {
                    if (preg_match($find, $content, $matches)) {
                        throw new CensorNotPassedException('content_banned');
                    }
                }
            }
        })->each(function ($word) {
            // 触发 tapEach
        });

        return $content;
    }
}
