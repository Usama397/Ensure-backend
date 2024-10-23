<?php

namespace App\Traits;

trait Defs
{
    static $INTERVAL_MONTHLY = 'monthly';
    static $INTERVAL_DAILY = 'daily';

    static $SECONDS = 300;
    static $FACEBOOK = 'FACEBOOK';
    static $GOOGLE = 'GOOGLE';

    static $FB_ACCESS = 'EAAFfCUQutNsBAHwTj0c4qSAuKOAzFcqVrd1S8SyMeZCzTGHinLd6EcCLamiE1SeZBZBJ6HAsmYKTx8an1MqwHVL9ZBKeck2P1qnHkwBGc50JCptd8tGI7mUlvvTXxjLUZCbX7F8ZBz5mZCEWzbPIBt7ASxZAbJtEKh18blRZCZBeskL58ZASDTaPA9Y';
    static $FB_SECRET = '4054edf4d45325fa8b99dd61d29e3066';
    static $FB_APP_ID = '385968379966683';

    static $CAMPAIGN_ACTIVE = 'ACTIVE';
    static $CAMPAIGN_PAUSED = 'PAUSED';
    static $CAMPAIGN_REMOVED = 'REMOVED';

    static $ALARM_NORMAL = 'normal';
    static $ALARM_WARNING = 'warning';
    static $ALARM_CRITICAL = 'critical';

    public static function intervals(): array
    {
        return array(self::$INTERVAL_DAILY, self::$INTERVAL_MONTHLY);
    }

    public static function upDownArrow($val1, $val2)
    {
        if ($val1 < $val2) {
            /*echo '<i class="ti-arrow-down text-danger"></i>';*/
            echo '<span class="glyphicon glyphicon-arrow-down text-danger"></span>';
        } else {
            /*echo '<i class="ti-arrow-up text-success"></i>';*/
            echo '<span class="glyphicon glyphicon-arrow-up text-success"></span>';
        }
    }

    public static function percentageNumbers($val1, $val2): string
    {
        if ($val1 < 1) {
            return 0 . '%';
        }
        //$percentChange = (1 - $val2 / $val1) * 100;
        //$percentChange = (($val1 - $val2) / (($val1 + $val2)/2)) * 100;

        $percentIncDec = (($val2 - $val1) / ($val1)) * 100;

        return number_format(abs($percentIncDec), 2) . '%';
    }

    public static function campaignStatus($platform, $status)
    {
        if ($platform == self::$GOOGLE) {
            if ($status == '2') {
                return self::$CAMPAIGN_ACTIVE;
            }
            if ($status == '3') {
                return self::$CAMPAIGN_PAUSED;
            }
            if ($status == '4') {
                return self::$CAMPAIGN_REMOVED;
            }
        }

        return $status;
    }

    public static function statusClasses($status)
    {
        if ($status == '2' || $status == self::$CAMPAIGN_ACTIVE) {
            return 'badge-primary';
        }
        if ($status == '3' || $status == self::$CAMPAIGN_PAUSED) {
            return 'badge-warning';
        }
        if ($status == '4' || $status == self::$CAMPAIGN_REMOVED) {
            return 'badge-danger';
        }

        return $status;
    }

    public static function activeCampaignStatus(): array
    {
        return array(self::$CAMPAIGN_ACTIVE, '2');
    }

    public static function percentageNumbersDifference($val1, $val2): string
    {
        if ($val1 < 1) {
            return 0;
        }

        $percentIncDec = (($val2 - $val1) / ($val1)) * 100;

        return number_format($percentIncDec);
    }

    public static function raiseAlarm($billedDifference, $unsubDifference): array
    {
        $alarmBilled = self::$ALARM_NORMAL;
        $alarmUnsub = self::$ALARM_NORMAL;

        if (!empty($billedDifference)) {
            if ($billedDifference > 2) {
                if ($billedDifference > 5) {
                    $alarmBilled = self::$ALARM_CRITICAL;
                } else {
                    $alarmBilled = self::$ALARM_WARNING;
                }
            } else {
                $alarmBilled = self::$ALARM_NORMAL;
            }
        }

        if (!empty($unsubDifference)) {
            if ($unsubDifference > 10) {
                if ($unsubDifference > 25) {
                    $alarmUnsub = self::$ALARM_CRITICAL;
                } else {
                    $alarmUnsub = self::$ALARM_WARNING;
                }
            } else {
                $alarmUnsub = self::$ALARM_NORMAL;
            }
        }

        $alarmStatus = collect([
                ['name' => 'Billed', 'status' => $alarmBilled],
                ['name' => 'Unsub', 'status' => $alarmUnsub],
            ]
        );

        $checkWarning = $alarmStatus->contains('status', self::$ALARM_WARNING);
        $checkCritical = $alarmStatus->contains('status', self::$ALARM_CRITICAL);

        $finalStatus = self::$ALARM_NORMAL;
        if ($checkWarning) {
            $finalStatus = self::$ALARM_WARNING;
        }
        if ($checkCritical) {
            $finalStatus = self::$ALARM_CRITICAL;
        }

        return array(
            'final_status' => $finalStatus,
            'data' => $alarmStatus,
        );
    }

    public static function GoogleChannelTypes(): array
    {
        return array(
            'UNSPECIFIED' => 0,
            'UNKNOWN' => 1,
            'SEARCH' => 2,
            'DISPLAY' => 3,
            'SHOPPING' => 4,
            'HOTEL' => 5,
            'VIDEO' => 6,
            'MULTI_CHANNEL' => 7,
            'LOCAL' => 8,
            'SMART' => 9,
            'PERFORMANCE_MAX' => 10,
        );
    }

    public static function GoogleChannelTypesMedium(): array
    {
        return array(
            0 => 'OTHER',
            1 => 'OTHER',
            2 => 'OTHER',
            3 => 'WEB',
            4 => 'OTHER',
            5 => 'OTHER',
            6 => 'OTHER',
            7 => 'APP',
            8 => 'OTHER',
            9 => 'OTHER',
            10 => 'OTHER',
        );
    }

    public static function FacebookObjectives(): array
    {
        return array(
            'APP_INSTALLS' => 1,
            'LINK_CLICKS' => 2,
            'CONVERSIONS' => 3,
        );
    }

    public static function FacebookObjectivesMedium(): array
    {
        return array(
            'APP_INSTALLS' => 'APP',
            'LINK_CLICKS' => 'WEB',
            'CONVERSIONS' => 'WEB',
        );
    }

    public static function identifyCampaignType($platform, $itemValue): int
    {
        if($platform == self::$FACEBOOK) {
            $facebookType = self::FacebookObjectives();
            return $facebookType[$itemValue] ?? 0;
        } else {
            return $itemValue;
        }
    }

    public static function identifyCampaignMedium($platform, $itemValue): string
    {
        if($platform == self::$FACEBOOK) {
            $facebookMedium = self::FacebookObjectivesMedium();
            return $facebookMedium[$itemValue] ?? 'OTHER';
        } else {
            $googleMedium = self::GoogleChannelTypesMedium();
            return $googleMedium[$itemValue] ?? 'OTHER';
        }
    }

    public static function calculateEconRevenue($revShare, $revenueVal)
    {
        $revenue = 0;
        foreach ($revShare as $share) {
            $revenue += ($revenueVal * $share) / 100;;
        }

        return $revenue;
    }
}
