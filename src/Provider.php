<?php

namespace DataHiveDevelopment\HiveIDSocialiteProvider;

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
        return $this->buildAuthUrlFromBase(($this->config['server'] ?: 'https://id.datahivedev.com') . '/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return ($this->config['server'] ?: 'https://id.datahivedev.com') . '/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(($this->config['server'] ?: 'https://id.datahivedev.com') . '/api/me', [
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
            'id'         => $user['id'],
            'nickname'   => $user['username'],
            'name'       => $user['name'],
            'email'      => ($user['email'] ?: null),
            'avatar'     => ($user['photo'] ?: null),
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
