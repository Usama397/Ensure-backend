<?php

namespace App\Traits;

trait ColorScheme
{
    public static function getGraphColor($modelName)
    {
        $colors = array(
            'econRevenue' => 'rgba(147, 42, 173, 1)',
            'mobilink' => 'rgba(0, 145, 210, 1)',
            'telenor' => 'rgba(215, 25, 32, 1)',
            'zong' => 'rgba(140, 198, 62, 1)',
            'ufone' => 'rgba(245, 89, 25, 1)',

            'bizstore' => 'rgba(0, 145, 210, 1)',
            'voiler' => 'rgba(215, 25, 32, 1)',
            'islam' => 'rgba(140, 198, 62, 1)',
            'rozgar' => 'rgba(245, 89, 25, 1)',
            'foodcourt' => 'rgba(113, 184, 255, 1)',
            'kc' => 'rgba(242, 180, 0, 1)',
            'gameworld' => 'rgba(82, 38, 16, 1)',
            'sciencelab' => 'rgba(245, 130, 32, 1)',
            'sl' => 'rgba(245, 130, 32, 1)',
            'langtutor' => 'rgba(255, 191, 0, 1)',
            'cp' => 'rgba(77, 238, 234, 1)',
            'uradio' => 'rgba(250, 60, 76, 1)',
            'rbt' => 'rgba(245, 130, 32, 1)',

            'total_subs' => 'rgba(0, 145, 210, 1)',
            'sms_subs' => 'rgba(215, 25, 32, 1)',
            'appsubs' => 'rgba(140, 198, 62, 1)',
            'websubs' => 'rgba(245, 89, 25, 1)',
            'ussdsubs' => 'rgba(113, 184, 255, 1)',
            'jasubs' => 'rgba(242, 180, 0, 1)',
            'ivrsubs' => 'rgba(82, 38, 16, 1)',
        );

        return $colors[$modelName];
    }

    public static function getMorrisColor($modelName)
    {
        $colors = array(
            'econRevenue' => '#6c5ad1',
            'eConceptions' => '#6c5ad1',

            'telenor' => '#007ad0',
            'mobilink' => '#f76878',
            'zong' => '#8cc63e',
            'ufone' => '#f4821f',

            'bizstore' => '#007ad0',
            'voiler' => '#f76878',
            'rozgar' => '#8cc63e',
            'islam' => '#f4821f',
            'kc' => '#ff80ed',
            'sciencelab' => '#065535',
            'sl' => '#065535',
            'foodcourt' => '#ffd700',
            'gameworld' => '#633b27',
            'langtutor' => '#ff7373',
            'cp' => '#666666',
            'uradio' => '#8cc63e',
            'rbt' => '#ff80ed',

            'total_subs' => '#6c5ad1',
            'sms_subs' => '#007ad0',
            'app_subs' => '#f76878',
            'web_subs' => '#8cc63e',
            'ussd_subs' => '#f4821f',
            'ja_subs' => '#633b27',
            'ivr_subs' => '#f79b4c',
        );

        return $colors[$modelName];
    }
}
