<?php

/**
 * SocialTwitterAccountService.php
 *
 * @category  PHP 
 * @package   PHP_CodeSniffer
 * @author    Vishal Soni vishalsoni611@gmail.com
 * @copyright 2018 My Company
 * @license   Licence Name
 * @version   PHP 7.2.7
 * @link      At github       
 **/

namespace App\Services;
use App\SocialTwitterAccount;
use App\User;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Session;

/**
 * This class enters the details of the user and saves it
 * 
 * @category  PHP 
 * @package   PHP_CodeSniffer
 * @author    Vishal Soni <vishalsoni611@gmail.com>
 * @copyright 2018 My Company
 * @license   Licence Name
 * @version   PHP 7.2.7
 * @link      At github       
 **/

class SocialTwitterAccountService
{
    /**
     * CreateOrGetUser method for user data processing.
     *
     * @return twitter page
     */
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = SocialTwitterAccount::whereProvider('twitter')
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($account) {
            session()->flush();
            session()->put('provider_user_id', $providerUser->getId());
            return $account->user;
        } else {
            $account = new SocialTwitterAccount(['provider_user_id' => $providerUser->getId(),'provider' => 'twitter']);

            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {
                $user = User::create(['email' => $providerUser->getEmail(),'name' => $providerUser->getName(),'password' => md5(rand(1, 10000)),]);
            }

            $account->user()->associate($user);
            $account->save();
            session()->flush();
            session()->put('provider_user_id', $providerUser->getId());
            return $user;
        }
    }
}