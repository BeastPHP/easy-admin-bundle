<?php

namespace Beast\EasyAdminBundle\Service;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class Sha1SaltEncoder implements PasswordEncoderInterface
{
    const KEY = 'beast_xin_yi';

    /**
     * @param $raw
     * @param $salt
     *
     * @return string
     */
    public function encodePassword($raw, $salt): string
    {
        return sha1($raw . self::KEY . $salt);
    }

    /**
     * @param string $encoded
     * @param string $raw
     * @param string $salt
     *
     * @return bool
     */
    public function isPasswordValid($encoded, $raw, $salt): bool
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }
}
