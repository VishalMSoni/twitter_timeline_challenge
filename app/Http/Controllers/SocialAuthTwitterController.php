<?php

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

class SocialAuthTwitterController extends Controller
{
	public function redirect()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function callback(SocialTwitterAccountService $service)
    {
        $user = $service->createOrGetUser(Socialite::driver('twitter')->user());
        auth()->login($user);
	    return redirect()->to('/twitterTimeline');
    }
    
    public function getUserTweets(){
        $id = session()->get('provider_user_id');
        $data = Twitter::getUserTimeline(['user_id' => $id, 'count' => 10, 'format' => 'array']);
    	$followers = Twitter::getFollowers(['user_id' => $id, 'count' => 10, 'format' => 'array']);
        $followers_id = [];
        foreach ($followers['users'] as $key => $value) {
            array_push($followers_id, $value['screen_name']);
        }
        
        return view('twitterTimeline',compact('data','followers','followers_id'));
    }

    public function getSearchDetails(Request $request)
    {
        $data = Twitter::getUserTimeline(['screen_name' => $request->search_string, 'count' => 10, 'format' => 'array']);
        return $data;    
    }

    public function getFollowers(Request $request){

        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;
        $xml_file_name = $request->followerName.'.xml';
        $sum = 0;
        $root = $dom->createElement('Followers');
        
        $allFollowers = [];

        if($request->downloadType=="xml_id"){
            $followers = Twitter::get('followers/ids',array('screen_name' => $request->followerName, 'count' => 5000 ,'format' => 'array'));
            array_push($allFollowers,$followers);
            
            while($followers['next_cursor']!=0) {
                $followers = Twitter::get('followers/ids',array('screen_name' => $request->followerName, 'count' => 5000 ,'cursor' => $followers['next_cursor'] ,'format' => 'array'));
                array_push($allFollowers, $followers);
            }

            foreach ($allFollowers as $key => $value) {
                foreach ($value['ids'] as $inner_key => $inner_value) {
                    $follower_number = $sum + $inner_key + 1;
                    $follower_node = $dom->createElement('follower_'.$follower_number);
                    
                    $child_node_id = $dom->createElement('Id',$inner_value);
                    $follower_node->appendChild($child_node_id);
                    
                    $root->appendChild($follower_node);
                }
                $sum = $follower_number;
            }
        }

        else if($request->downloadType=="xml_name"){
            $followers = Twitter::getFollowers(['screen_name' => $request->followerName, 'count' => 200,'format' => 'array']);
            array_push($allFollowers,$followers);
            
            while($followers['next_cursor']!=0) {
                $followers = Twitter::getFollowers(['screen_name' => $request->followerName, 'count' => 200,'cursor' => $followers['next_cursor'] ,'format' => 'array']);    
                array_push($allFollowers, $followers);
            } 

            foreach ($allFollowers as $key => $value) {
                foreach ($value['users'] as $inner_key => $inner_value) {
                    $follower_number = $sum + $inner_key + 1;
                    $follower_node = $dom->createElement('follower_'.$follower_number);
                    
                    $child_node_id = $dom->createElement('Id',$inner_value['id']);
                    $follower_node->appendChild($child_node_id);
                    
                    $child_node_screen_name = $dom->createElement('Screen_Name',$inner_value['screen_name']);
                    $follower_node->appendChild($child_node_screen_name);
                    
                    $child_node_name = $dom->createElement('Name',htmlspecialchars($inner_value['name']));
                    $follower_node->appendChild($child_node_name);
                    $root->appendChild($follower_node);
                }
                $sum = $follower_number;
            }
        }
                
        $dom->appendChild($root);
        $dom->save($xml_file_name);
        echo '<a href="'.$xml_file_name.'">'.$xml_file_name.'</a> has been successfully created'; 
        
        // print_r("<pre>");
        // print_r($inner_value);
        // print_r("<pre>");
        // exit();        

        // header('Content-type: text/xml');
        // header('Content-Disposition: attachment; filename='.$xml_file_name);
        
        // $xml = simplexml_load_file($xml_file_name);
        // print_r($xml);
    }
}
