<?php

namespace App\Serializer\Normalizer;

final class DateTimeNormalizerCallback
{
	public static function buildCallback($dateFormat = \DateTime::ISO8601, $default = '')
	{
        return function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) use ($dateFormat, $default) {
            return $innerObject instanceof \DateTime ? $innerObject->format($dateFormat) : $default;
        };
	}
}