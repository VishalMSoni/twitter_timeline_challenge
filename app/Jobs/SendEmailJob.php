<?php

/**
 * This script handles functionalities for background jobs 
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

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\SocialTwitterAccount;
use App\Mail\SendMailable;
use Mail;
use DOMDocument;
use DOMAttr;
use SimpleXMLElement;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '5120M');

include(public_path('simple_html_dom.php'));

/**
 * This class handles functionalities required for background jobs 
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

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $followerName, $followerEmail, $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->followerName =  $request->followerName;
        $this->followerEmail = $request->followerEmail;
    }

    /**
     * This method download the followers from html using curl
     * 
     * @return cursor
     */
    public function getFollowersByHtml($url2, $i=0, $dom, $root) 
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

        if ($html) {
            $allFollowers = [];
            $key = 0;
            
            foreach ($html->find('.user-item') as $element) {
                if ($element) {
                    $name = $element->find('.fullname', 0)->innertext;
                    $id2 = $element->find('.username', 0)->innertext;
                    
                    $allFollowers[$key]['name'] = $name;
                    $allFollowers[$key]['screen_name'] = strip_tags($id2);
                    $key++;
                }
            }

            foreach ($allFollowers as $key => $value) {    
                $follower_number = $key + $i*20 + 1;
                $follower_node = $dom->createElement('follower_'.$follower_number);
                
                $child_node_id = $dom->createElement('Name', $value['name']);
                $follower_node->appendChild($child_node_id);

                $child_node_screen_name = $dom->createElement('Screen_Name', $value['screen_name']);
                $follower_node->appendChild($child_node_screen_name);
                
                $root->appendChild($follower_node);
            }
            
            unset($allFollowers);
            $cursor = @$html->find('.w-button-more a', 0)->href;
            return $cursor;        
        }
    }

    /**
     * This method recursively call the upper function
     * 
     * @return void
     */
    public function recursiveFollowers($cursor, $i, $dom, $root)
    {
        $cursor = SendEmailJob::getFollowersByHtml($cursor, $i, $dom, $root);    
        $val = explode("=",$cursor);

        if(count($val)>1){
            $value = (int)$val[1];
            if ($value!=0 and $i<500000) {
                $i++;
                SendEmailJob::recursiveFollowers($cursor, $i, $dom, $root);    
            }
        } 
        else {
            $dom->appendChild($root);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $value = '/' . $this->followerName . '/followers';
        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;
        $xml_file_name = $this->followerName.'.xml';
        $root = $dom->createElement('Followers');
        $followersArray = [];
        
        SendEmailJob::recursiveFollowers($value, 0, $dom, $root);    
        
        $dom->save(public_path($xml_file_name));

        $to = $this->followerEmail;
        $subject = 'Attached XML file for '.$this->followerName.' this follower$
        $message = 'Here is XML file attached for '.$this->followerName.' this $
        $file_type = "XML";

        $content = file_get_contents(public_path($xml_file_name));

        //$handle = fopen(public_path($xml_file_name), "rb");
        //$size = filesize(public_path($xml_file_name));
        //$content = fread($handle, $size);
        //fclose($handle);

        $boundary = md5("random");
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Reply-To: ".$this->followerEmail."\r\n";
        $headers .= "Content-Type: multipart/mixed;\r\n";
        $headers .= "boundary = $boundary\r\n";

        $headers = "--$boundary\r\n";
        $headers .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
        $headers .= $message; 

        $headers = "--$boundary\r\n";
        $headers ="Content-Type: $file_type; name=".$xml_file_name."\r\n";
        $headers .="Content-Disposition: attachment; filename=".$xml_file_name.$
        //$headers .="Content-Transfer-Encoding: base64\r\n";
        //$body .="X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n"; 

        $body = $content;
        mail($to, $subject, $body, $headers);

        //$mailData = [];
        //$mailData['name'] = $this->followerName;
        //$mailData['user_mail'] = $this->followerEmail;
        //$mailData['xml_file'] = $xml_file_name;

        //Mail::to($this->followerEmail)->send(new SendMailable($mailData));   
    }
}
