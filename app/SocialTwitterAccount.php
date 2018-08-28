<?php

/**
 * SocialTwitterAccount.php
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

namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * This class creates one to one functionality by creating model 
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

class SocialTwitterAccount extends Model
{
    protected $fillable = ['user_id', 'provider_user_id', 'provider'];

    /**
     * User method to create model for database.
     *
     * @return twitter page
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
