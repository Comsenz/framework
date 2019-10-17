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
use Illuminate\Support\LazyCollection;

class Censor
{
    public $config = 'default';

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
        if ($this->isTurnOff) {
            return $content;
        }

        StopWord::orderBy($type)->cursor()->tapEach(function ($word) use ($content, $type) {
            $find = '/' . addcslashes($word, '/') . '/i';
            // dump($find);
            if ($word->{$type} == '{REPLACE}') {
                if (preg_match_all($find, $content, $matches)) {
                    dump($matches);
                }
            } else {
                if (preg_match($find, $content, $matches)) {
                    dump($matches);
                }
            }
            if (preg_match_all($find, $content, $matches)) {
                dump($matches);
                // $this->words_found = $matches[0];
                // $this->result = DISCUZ_CENSOR_BANNED;
                // $this->words_found = array_unique($this->words_found);
                // $message = $this->highlight($message, $banned_words);
                // return DISCUZ_CENSOR_BANNED;
            }
        })->each(function ($word) {
            // 触发 tapEach
        });

        // foreach ($stopWords as $word) {
        //     echo $word->id;
        // }

        return $content;
    }
}
