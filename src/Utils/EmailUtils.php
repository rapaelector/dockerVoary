<?php

namespace App\Utils;

use App\Entity\User;
use Symfony\Component\Mime\Address;

class EmailUtils
{
	public static function toAddress(User $user): ?Address
	{
		if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
			return null;
		}

		return new Address($user->getEmail(), $user->getName());
	}

	public static function toAddresses(array $users): array
	{
		$addresses = [];

		foreach ($users as $user) {
			if ($address = self::toAddress($user)) {
				$addresses[] = $address;
			}
		}

		return $addresses;
	}
}