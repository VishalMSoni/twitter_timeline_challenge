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
use Laravel\Socialite\Facades\Socialite;
use App\Services\SocialTwitterAccountService;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as ProviderUser;
use App\SocialTwitterAccount;
use Twitter;
use File;
use Auth;
use Session;
use DOMDocument;
use DOMAttr;
use PDF;
use Mail;
use Xml2Pdf;

ini_set('max_execution_time', 0);
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
     * @return void
     */
    public function getFollowersByHtml($url2, $i = 0, $dom, $root) 
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

        $allFollowers = [];
        $key = 0;

        foreach ($html->find('.user-item') as $element) {
            $name = $element->find('.fullname', 0)->innertext;
            $id2 = $element->find('.username', 0)->innertext;
            
            $follower_number = $key + $i*20 + 1;
            $follower_node = $dom->createElement('follower_'.$follower_number);
            
            $child_node_id = $dom->createElement('Name', $name);
            $follower_node->appendChild($child_node_id);
            
            $child_node_screen_name = $dom->createElement('Screen_Name', strip_tags($id2));
            $follower_node->appendChild($child_node_screen_name);
            
            $root->appendChild($follower_node);
            $key++;
        }

        $cursor = @$html->find('.w-button-more a', 0)->href;
        if ($i < 500000 && $cursor) {
            $dom = SocialAuthTwitterController::getFollowersByHtml($cursor, ++$i, $dom, $root);
        }

        return $dom;        
    }

    /**
     * This method generates the xml file of followers for specific user
     * 
     * @return void
     */
    public function getFollowers(Request $request)
    {
        $value = '/' . $request->followerName . '/followers';
        $allFollowers = [];
        $key = 0;
        
        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;
        $xml_file_name = $request->followerName.'.xml';
        $root = $dom->createElement('Followers');
        
        $dom = SocialAuthTwitterController::getFollowersByHtml($value, 0, $dom, $root);    
        $dom->appendChild($root);
        $dom->save($xml_file_name);
        echo '<a href="'.$xml_file_name.'" download>'.$xml_file_name.'</a> has been successfully created ! Click it ...'; 

        // if ($request->downloadType=="pdf") {
        //     view()->share('allFollowers', $allFollowers);
        //     $pdf_file_name = $request->followerName.'.pdf';

        //     $pdf = PDF::loadView('htmlToPdfView');
        //     return $pdf->download($request->followerName.'.pdf');
        // } 
    }

}
