<?php

namespace App\Entity\Traits;

use App\Entity\Constants\Activities;

trait ClientTrait
{
    public static function getActivityChoices($associative = false)
	{
		$choices = [
			'activity.cement_factory' => Activities::ACTIVITY_CEMENT_FACTORY,
			'activity.candy' => Activities::ACTIVITY_CANDY,
			'activity.dehydration' => Activities::ACTIVITY_DEHYDRATION,
			'activity.covered' => Activities::ACTIVITY_COVERED,
			'activity.chemistry' => Activities::ACTIVITY_CHEMISTRY,
			'activity.sandpit' => Activities::ACTIVITY_SANDPIT,
			'activity.stationery' => Activities::ACTIVITY_STATIONERY,
			'activity.port_industry' => Activities::ACTIVITY_PORT_INDUSTRY,
			'activity.naval_industry' => Activities::ACTIVITY_NAVAL_INDUSTRY,
			'activity.mining_extraction' => Activities::ACTIVITY_MINING_EXTRACTION,
			'activity.food_industry' => Activities::ACTIVITY_FOOD_INDUSTRY,
			'activity.iron_and_steel_industry' => Activities::ACTIVITY_IRON_AND_STEEL_INDUSTRY,
			'activity.waste' => Activities::ACTIVITY_WASTE,
			'activity.subcontracting' => Activities::ACTIVITY_SUBCONTRACTING,
			'activity.energy' => Activities::ACTIVITY_ENERGY,
			'activity.constructor' => Activities::ACTIVITY_CONSTRUCTOR,
			'activity.intermediary_business' => Activities::ACTIVITY_INTERMEDIARY_BUSINESS,
			'activity.others' => Activities::ACTIVITY_OTHERS,
		];

		return $associative ? $choices : array_values($choices);
	}

    public static function getTvaChoices($associative = false)
    {
        $values = [0, 8.5, 20, -1];
        $labels = ['tva.value.0', 'tva.value.8', 'tva.value.20', 'tva.value.other'];

        return $associative ? array_combine($labels, $values) : $values;
    }

	public static function getPaymentPeriodChoices($associative = false)
    {
        $choices = [
            self::PAYMENT_PERIOD_45_END,
            self::PAYMENT_PERIOD_30_END,
            self::PAYMENT_PERIOD_45_NET,
            self::PAYMENT_PERIOD_30_NET,
            self::PAYMENT_PERIOD_IMMEDIATE_TRANSERT,
        ];

        return $associative ? array_combine($choices, $choices) : $choices;
    }

	public static function getPaymentTypeChoices($associative = false)
    {
        $choices = [
            self::PAYMENT_TYPE_CHECK,
            self::PAYMENT_TYPE_TRANSFER,
        ];

        return $associative ? array_combine($choices, $choices) : $choices;
    }

	public static function getTypeChoices($associative = false)
    {
        $choices = [
            'type.client' => static::TYPE_CLIENT,
            'type.prospect' => static::TYPE_PROSPECT,
        ];

        return $associative ? $choices : array_values($choices);
    }
}