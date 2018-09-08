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
use Twitter;
use File;
use Auth;
use Session;
use DOMDocument;
use DOMAttr;
use Mail;
use xml2pdf;
use SimpleXMLElement;
use PDF;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '5120M');

include './simple_html_dom.php';

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
     * This method download the followers from html using curl
     * 
     * @return cursor
     */
    public function getFollowersByHtml($url2, $i=0, $dom, $root, &$followersArray=[]) 
    {        
        $url = 'https://twitter.com' . $url2;
        $ch = curl_init();
        $timeout = 5;
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $html = str_get_html($data);
        $key = 0;

        if ($html) {
            foreach ($html->find('.user-item') as $element) {
                if ($element) {
                    $name = $element->find('.fullname', 0)->innertext;
                    $id2 = $element->find('.username', 0)->innertext;
                    
                    $followersArray[$key + $i*20]['name'] = $name;
                    $followersArray[$key + $i*20]['screen_name'] = strip_tags($id2);
                    $key++;
                }
            }

            foreach ($followersArray as $key => $value) {    
                $follower_number = $key + $i*20 + 1;
                $follower_node = $dom->createElement('follower');
                
                $child_node_id = $dom->createElement('Name', $value['name']);
                $follower_node->appendChild($child_node_id);

                $child_node_screen_name = $dom->createElement('Screen_Name', $value['screen_name']);
                $follower_node->appendChild($child_node_screen_name);
                
                $root->appendChild($follower_node);
            }

            $cursor = @$html->find('.w-button-more a', 0)->href;
            return $cursor;        
        }
    }

    /**
     * This method recursively call the upper function
     * 
     * @return void
     */
    public function recursiveFollowers($cursor, $i, $dom, $root, &$followersArray=[])
    {
        $cursor = SocialAuthTwitterController::getFollowersByHtml($cursor, $i, $dom, $root, $followersArray);    
        $val = explode("=",$cursor);

        if(count($val)>1){
            $value = (int)$val[1];
            if ($value!=0 and $i<500000) {
                $i++;
                SocialAuthTwitterController::recursiveFollowers($cursor, $i, $dom, $root, $followersArray);    
            }
        } 
        else {
            $dom->appendChild($root);
        }
    }

    /**
     * This method send mail to user
     * 
     * @return void
     */
    public function sendEmail($mailData)
    {
        Mail::to($mailData['user_mail'])->send(new SendMailable($mailData));
        return 'PDF file attached !!!';
    }

    /**
     * This method generates the xml file of followers for specific user
     * 
     * @return void
     */
    public function getFollowers(Request $request)
    {
        $value = '/' . $request->followerName . '/followers';
        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;
        $xml_file_name = $request->followerName.'.xml';
        $root = $dom->createElement('Followers');
        $followersArray = [];
        $key = 0;

        SocialAuthTwitterController::recursiveFollowers($value, 0, $dom, $root, $followersArray);    
        $dom->save($xml_file_name);
        echo '<a href="'.$xml_file_name.'" download>'.$xml_file_name.'</a> has been successfully created ! Click it ...<br>';   
        
        // $xml = simplexml_load_file($xml_file_name) or die("Error: Cannot create object");
        // $allFollowers =  (array) $xml;

        // foreach ($allFollowers['follower'] as $key => $value) {
        //     $sub_followers =  (array) $value;            
        //     $followersArray[$key]['name'] = $sub_followers['Name']; 
        //     $followersArray[$key]['screen_name'] = $sub_followers['Screen_Name'];
        //     unset($sub_followers);
        // }

        // print_r("<pre>");
        // print_r($followersArray);
        // print_r("<pre>");
        
        view()->share('followersArray', $followersArray);
        $pdf_file_name = $request->followerName.'.pdf';

        $pdf = PDF::loadView('htmlToPdfView');
        echo '<a href="'.$pdf_file_name.'" download>'.$pdf_file_name.'</a> has been successfully created ! Click it ...';   
        
        $pdf->save($pdf_file_name);
        $mailData = [];
        $mailData['name'] = $request->followerName;
        $mailData['user_mail'] = $request->followerEmail;
        $mailData['pdf_file'] = $pdf_file_name;

        SocialAuthTwitterController::sendEmail($mailData);
        return 'PDF file generated !!!';
    }
}