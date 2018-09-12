<?php

/**
 * SocialAuthTwitterController.php
 *
 * @category  PHP 
 * @package   PHP_CodeSniffer
 * @author    Vishal Soni <vishalsoni611@gmail.com>
 * @copyright 2018 My Company
 * @license   Licence Name
 * @version   SVN: $Id: coding-standard-tutorial.xml,
 * v 1.9 2008-10-09 15:16:47 cweiske Exp $
 * @link      At github       
 **/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Services\SocialTwitterAccountService;
use App\SocialTwitterAccount;
use App\Mail\SendMailable;
use App\Jobs\SendEmailJob;
use Twitter;
use File;
use Auth;
use Session;
use DOMDocument;
use Mail;
use DOMAttr;
use SimpleXMLElement;
use PDF;
use Carbon\Carbon;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '5120M');

/**
 * This class handles the all functionalities required 
 *
 * @category  PHP 
 * @package   PHP_CodeSniffer
 * @author    Vishal Soni <vishalsoni611@gmail.com>
 * @copyright 2018 My Company
 * @license   Licence Name
 * @version   SVN: $Id: coding-standard-tutorial.xml,
 * v 1.9 2008-10-09 15:16:47 cweiske Exp $
 * @link      At github       
 **/

class SocialAuthTwitterController extends Controller
{
    /**
     * Redirect method to twitter.
     *
     * @return twitter page
     */
    public function redirect()
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Callback method.
     * 
     * @return userTimeline
     */
    public function callback(SocialTwitterAccountService $service)
    {
        $user = $service->createOrGetUser(Socialite::driver('twitter')->user());
        auth()->login($user);
        return redirect()->to('/twitterTimeline');
    }
    
    /**
     * This method gives the tweets and followers of the user
     *
     * @return userTimeline with relevent data
     */
    public function getUserTweets()
    {
        $id = session()->get('provider_user_id');
        $data = Twitter::getUserTimeline(['user_id' => $id, 'count' => 10, 'format' => 'array']);
        $followers = Twitter::getFollowers(['user_id' => $id, 'count' => 10, 'format' => 'array']);
        $followers_id = [];
        foreach ($followers['users'] as $key => $value) {
            array_push($followers_id, $value['screen_name']);
        }

        foreach ($data as $key => $value) {
            if (!empty($value['entities']['media'])) {
                $imageData = base64_encode(file_get_contents($value['entities']['media'][0]['media_url']));
                $imageData = 'data:image/jpeg;base64,'.$imageData;
                $data[$key]['imageData'] = $imageData;
            }
        }
        
        return view('twitterTimeline', compact('data', 'followers', 'followers_id'));
    }

    /**
     * This method gives the tweets of specified user
     *
     * @return data
     */
    public function getSearchDetails(Request $request)
    {
        $data = Twitter::getUserTimeline(['screen_name' => $request->search_string, 'count' => 10, 'format' => 'array']);
        foreach ($data as $key => $value) {
            if (!empty($value['entities']['media'])) {
                $imageData = base64_encode(file_get_contents($value['entities']['media'][0]['media_url']));
                $imageData = 'data:image/jpeg;base64,'.$imageData;
                $data[$key]['imageData'] = $imageData;
            }
        }
        return $data;    
    }

    /**
     * This method generates the xml file of followers for specific user
     * 
     * @return void
     */
    public function getFollowers(Request $request)
    {
        SendEmailJob::dispatch(new SendEmailJob($request))
                ->delay(Carbon::now()->addSeconds(1));
        
        return 'XML file will be generated and emailed !!!';
    }
}