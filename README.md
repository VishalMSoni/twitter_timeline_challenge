# Twitter Timeline Challenge - rtCamp

## About Twitter Timeline Challenge

This Challenge consists of three parts such as:
1. Connect using twitter auth & getting user tweets in JS slider
2. Show user followers & search user tweets with showing in same slider via AJAX
3. Download User's followers in pdf & xml format.

## Working Demo of the challenge
- **[Twitter Timeline Challenge - rtCamp](http://ec2-18-191-220-211.us-east-2.compute.amazonaws.com/)**

## Libraries used
* **[Laravel Socialite](https://laravel.com/docs/5.6/socialite)**
* **[thujohn twitter api](https://github.com/thujohn/twitter/blob/master/README.md)**
* **[DOMPDF](https://github.com/dompdf/dompdf)**
* PHP library **[DOMDOCUMENT](http://php.net/manual/en/class.domdocument.php)**.

## Flow of the challenge 

#### Connection part
Starting with the challenge i have created a new **Laravel** project after that for the connection part i have used **[Laravel Socialite](https://laravel.com/docs/5.6/socialite)** for authenticating user via twitter.So by login this will redirect us to the **Twitter** and once user is authenticated it will be return back to our script via callback function.

#### Getting tweets & followers
Once user is succesfully logged in now next part is to get tweets and followers of that user for that part i have used **[thujohn twitter api](https://github.com/thujohn/twitter/blob/master/README.md)**.Using this twitter api we can get the desired details by different functions ex.

- **getUserTimeline()** : this function will return latest tweets of the user based on different parameters like:
  - screen_name : user screen_name
  - id : user id
  - count : no. of tweets to get (for our part it is 10)
- **getfollowers()** : this functions will return followers of the user with parameters like:
  - screen_name : user screen_name
  - id : user id
  - count : no. of followers to get in one page after that we have to get details by providing next_cursor parameter
    - if we want only id's of the followers of the user then we can get 5000 id's in one page
    - or if we want to get id' , screen_name and other details then we can get 200 follower's detail in one page

So after details are fetched tweets & followers will be showed in **JS slider & table** respectively.
- If there are any *image* in particular tweet then that will be also showed in *JS Slider*
- If there are *no tweets* then slider will reflect accordingly showing no tweets.

When user will search follower then his/her follower will be searched and **Auto-suggest support** will suggest the follower as soon as we start typing.Once follower is searched then his screen_name will be passed to respective function by **AJAX** call 
 - if function returns data *succesfully* then it will be showed in **JS slider**
 - otherwise **Alert** will be showed to write proper screen_name

#### Download Followers
I am providing user to download his/her followers in two formats **PDF & XML**.In that i am also providing **flexibility** to user like, **get only id's of followers** or **get id's,screen_name,name's of the followers**.

1. **XML** part i am using **[DOMDOCUMENT](http://php.net/manual/en/class.domdocument.php)** and
2. **PDF** part **[DOMPDF](https://github.com/dompdf/dompdf)** is used.

Based on user's choice follower's will be downloaded succesfully to his/her preffered location in the PC.

## Limitations
- In downloading followers we can download upto 75000 followers, based on twitter api rate limit constraint.

## Coding Guidelines 
- **UI Framework** : As i am using laravel application so that is following **Twitter Bootstrap** so i have used as much as i can of twitter Bootstrap
- **Responsive** : I have taken care of this and for that media queries are written in '*public/css/css_file.css*'.Also checked in different mobile like iphone4,iphone6,Mi A1 and Mi Note3.It works succesfully in all the mobiles and followers are downloaded in desired formats.
- **Code Organisation** : As i am using laravel application so it is not possible to put all the 3rd party codes and libraries in **lib** folder because controller's code should be in *app* folder, 3rd party libraries are in *vendor & composer.json* folder. 
- **Coding Standard** : I have used **[PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)** & **[JSHint](http://jshint.com/)** for cheking coding standards in localhost and i have tried to minimize the *errors & warnings*.
- **GitHub** : From the starting of this challenge i have started to use **[GitHub](https://github.com/)** and commiting and pushing the code in my **[GitHub Repository](https://github.com/VishalMSoni/twitter_timeline_challenge)**. 

#### Created By : 
* Vishal Murlidhar Soni
* Email : vishal.s.btechi15@ahduni.edu.in , vishalsoni611@gmail.com
* School of Engineering and Applied Science (Ahmedabad University)
* Phone no. 9429707486/8460806485
