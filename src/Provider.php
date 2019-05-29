<?php

namespace BioHiveTech\HiveIDSocialiteProvider;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'HIVEID';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['user.read'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(($this->config['server'] ?: 'https://account.biohivetech.com') . '/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return ($this->config['server'] ?: 'https://account.biohivetech.com') . '/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(($this->config['server'] ?: 'https://account.biohivetech.com') . '/api/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['uuid'],
            'nickname' => $user['username'],
            'name'     => ($user['name'] ?: null),
            'email'    => ($user['email'] ?: null),
            'avatar'   => $user['avatar'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }

    /**
     * Add the additional configuration key 'server' to enable developers to test with local instances
     *
     * @return array
     */
    public static function additionalConfigKeys()
    {
        return ['server'];
    }
}
