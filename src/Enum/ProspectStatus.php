<?php

namespace App\Enum;

class ProspectStatus
{
    //Roles name
    public const STATUS_AWAITING_CUSTOMER_RESPONSE = "STATUS_AWAITING_CUSTOMER_RESPONSE";
    public const STATUS_AWAITING_COMMERCIAL_RESPONSE = "STATUS_AWAITING_COMMERCIAL_RESPONSE";
    public const STATUS_CLOSED_WON = "STATUS_CLOSED_WON";
    public const STATUS_CLOSE_LOST = "STATUS_CLOSED_LOST";

    public static function getAllStatus()
    {
        return [
            ProspectStatus::STATUS_AWAITING_CUSTOMER_RESPONSE,
            ProspectStatus::STATUS_AWAITING_COMMERCIAL_RESPONSE,
            ProspectStatus::STATUS_CLOSED_WON,
            ProspectStatus::STATUS_CLOSE_LOST
        ];
    }

    public static function getAllStatusWithLabel()
    {
        return [
            ProspectStatus::STATUS_AWAITING_CUSTOMER_RESPONSE => "Awaiting customer response",
            ProspectStatus::STATUS_AWAITING_COMMERCIAL_RESPONSE => "Awaiting commercial response",
            ProspectStatus::STATUS_CLOSED_WON => "Case close - Won",
            ProspectStatus::STATUS_CLOSE_LOST => "Case closed - Lost"
        ];
    }
}
