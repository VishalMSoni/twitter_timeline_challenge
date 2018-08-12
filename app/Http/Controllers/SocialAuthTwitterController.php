<?php

/**
 * SocialAuthTwitterController.php
 *
 * @category  PHP 
 * @package   PHP_CodeSniffer
 * @author    Vishal Soni <vishalsoni611@gmail.com>
 * @copyright 2018 My Company
 * @license   Licence Name
 * @version   SVN: $Id: coding-standard-tutorial.xml,v 1.9 2008-10-09 15:16:47 cweiske Exp $
 * @link      At github       
 **/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Services\SocialTwitterAccountService;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Twitter;
use File;
use Auth;
use App\SocialTwitterAccount;
use Session;
use DOMDocument;
use DOMAttr;
use PDF;

/**
 * Class summary
 * This class handles the all functionalities required 
 * @category  PHP 
 * @package   PHP_CodeSniffer
 * @author    Vishal Soni <vishalsoni611@gmail.com>
 * @copyright 2018 My Company
 * @license   Licence Name
 * @version   SVN: $Id: coding-standard-tutorial.xml,v 1.9 2008-10-09 15:16:47 cweiske Exp $
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
        // print_r("<pre>");
        // print_r($data);
        // print_r("<pre>");
        // exit();
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
        if ($request->downloadType=="xml_id" || $request->downloadType=="xml_name") {
            $dom = new DOMDocument();
            $dom->encoding = 'utf-8';
            $dom->xmlVersion = '1.0';
            $dom->formatOutput = true;
            $xml_file_name = $request->followerName.'.xml';
            $sum = 0;
            $root = $dom->createElement('Followers');
            
            $allFollowers = [];

            if ($request->downloadType=="xml_id") {
                $followers = Twitter::get('followers/ids', array('screen_name' => $request->followerName, 'count' => 5000 ,'format' => 'array'));

                array_push($allFollowers, $followers);
                while ($followers['next_cursor']!=0) {
                    $followers = Twitter::get('followers/ids', array('screen_name' => $request->followerName, 'count' => 5000 ,'cursor' => $followers['next_cursor'] ,'format' => 'array'));
                    array_push($allFollowers, $followers);
                }

                foreach ($allFollowers as $key => $value) {
                    if ($value['ids']) {
                        foreach ($value['ids'] as $inner_key => $inner_value) {
                            $follower_number = $sum + $inner_key + 1;
                            $follower_node = $dom->createElement('follower_'.$follower_number);
                            
                            $child_node_id = $dom->createElement('Id', $inner_value);
                            $follower_node->appendChild($child_node_id);
                            
                            $root->appendChild($follower_node);
                        }
                        $sum = $follower_number;
                    }
                }
            } else if ($request->downloadType=="xml_name") {
                $followers = Twitter::getFollowers(['screen_name' => $request->followerName, 'count' => 200, 'format' => 'array']);
                array_push($allFollowers, $followers);
                
                while ($followers['next_cursor']!=0) {
                    $followers = Twitter::getFollowers(['screen_name' => $request->followerName, 'count' => 200, 'cursor' => $followers['next_cursor'] , 'format' => 'array']);    
                    array_push($allFollowers, $followers);
                } 

                foreach ($allFollowers as $key => $value) {
                    if ($value['users']) {
                        foreach ($value['users'] as $inner_key => $inner_value) {
                            $follower_number = $sum + $inner_key + 1;
                            $follower_node = $dom->createElement('follower_'.$follower_number);
                            
                            $child_node_id = $dom->createElement('Id', $inner_value['id']);
                            $follower_node->appendChild($child_node_id);
                            
                            $child_node_screen_name = $dom->createElement('Screen_Name', $inner_value['screen_name']);
                            $follower_node->appendChild($child_node_screen_name);
                            
                            $child_node_name = $dom->createElement('Name', htmlspecialchars($inner_value['name']));
                            $follower_node->appendChild($child_node_name);
                            $root->appendChild($follower_node);
                        }
                        $sum = $follower_number;
                    }
                }
            } 

            $dom->appendChild($root);
            $dom->save($xml_file_name);
            echo '<a href="'.$xml_file_name.'" download>'.$xml_file_name.'</a> has been successfully created ! Click it ...';
        } else if ($request->downloadType=="pdf") {
            $allFollowers = [];
            $followers = Twitter::getFollowers(['screen_name' => $request->followerName, 'count' => 200, 'format' => 'array']);
            array_push($allFollowers, $followers);
            
            while ($followers['next_cursor']!=0) {
                $followers = Twitter::getFollowers(['screen_name' => $request->followerName, 'count' => 200, 'cursor' => $followers['next_cursor'] , 'format' => 'array']);    
                array_push($allFollowers, $followers);
            }

            view()->share('allFollowers', $allFollowers);
            $pdf_file_name = $request->followerName.'.pdf';

            $pdf = PDF::loadView('htmlToPdfView');
            return $pdf->download($request->followerName.'.pdf');
        } 
    }
}
