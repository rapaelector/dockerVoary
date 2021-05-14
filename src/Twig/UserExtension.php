<?php

namespace App\Twig;

use App\Entity\User;
use App\Utils\Resolver;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\Asset\Packages;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\Security\Core\Security;

class UserExtension extends AbstractExtension
{
    public function __construct(Packages $packages, StorageInterface $storage, Security $security)
    {
        $this->security = $security;
        $this->packages = $packages;
        $this->storage = $storage;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_avatar', [$this, 'userAvatar']),
        ];
    }

    public function userAvatar(?User $user = null): string
    {
        return $this->getUserPhoto($user, 'profileName', 'profileFile', 'user.png', 'images');
    }

    public function getUserPhoto(?User $user = null, $prop, $uploadableField, $defaultValue, $packageName = 'images'): string
    {
        if ($user) {
            if (Resolver::resolve([$user, $prop], null)) {
                return $this->storage->resolveUri($user, $uploadableField, User::class);
            }
        } else if ($user = $this->security->getUser()) {
            if (Resolver::resolve([$user, $prop], null)) {
                return $this->storage->resolveUri($user, $uploadableField, User::class);
            }
        }

        return $this->packages->getUrl($defaultValue, $packageName);
    }
}
