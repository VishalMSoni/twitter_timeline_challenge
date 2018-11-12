# Twitter Timeline Challenge - rtCamp

## About Twitter Timeline Challenge

This Challenge consists of three parts such as:
1. Connect using twitter auth & getting user tweets in JS slider
2. Show user followers & search user tweets with showing in same slider via AJAX
3. Download User's followers in pdf & xml format.

## Working Demo of the challenge
- **[Twitter Timeline Challenge - rtCamp](http://ec2-3-16-89-248.us-east-2.compute.amazonaws.com/)**

## Added Corrections as per conversation

### Downloading followers part
* As per last conversation you have said to that followers are not downloading properly on that thing i want to say that i am taking data using **Web Scrapping** not through any **API** so it may happen that sometime by taking live data whole data may not come due to any reason.
* So i request you that if any such thing happens then refresh the process you will get your desired output.

### Background Job Processing
* Now, i have implemented background job processing through **[Laravel Queue](https://laravel.com/docs/5.7/queues)** and after that i am sending **XML** file to user's mail address.
* In sending mail i have changed the process because earlier i am sending mail through **[Laravel Mail](https://laravel.com/docs/5.7/mail)** but in this method there is bottlenack that it is not able to hold **SMTP** connection for long time.
* So for large amount of followers it shows *SMTP connection timed out error* after that i have implemented **[PHP Mail](http://php.net/manual/en/function.mail.php)** for sending mail and then it solved the issue.
* I have used **[tmux commands](https://gist.github.com/MohamedAlaa/2961058)** to run the commands on server for background job processing.

## Corrections as said
* As per the **twitter api** limit which is *15 call in 15 minutes* so after calling api more than 15 times it shows that **Rate limit excedded** as expected so to overcome the rate limit we have to think another solution.
* As we have to download thousands of followers then through api it is tidius task.
* So for that thing i have implemented the solution using **[Client URL Library](http://php.net/manual/en/book.curl.php)**.
* This will download the html page of followers by providing **URL** and then it will extract data from that page but in this method it will give only *20 followers* at a time. So after that we have to call another **URL** by providing **Cursor** value.
* So it will iterate untill all the followers are finished and due to this process of **downloading the page and extracting the data continously** it will take a time !! for that you have to wait for a while.
* But once we have run the script and then if we close the window then process will run on the server untill **XML** file generated.That file will be on the **public** folder so you may look at it after some time. 
* All important methods(*main business logic*) are provided in **[SocialAuthTwitterController.php](https://github.com/VishalMSoni/twitter_timeline_challenge/blob/master/app/Http/Controllers/SocialAuthTwitterController.php)**
* Apart from that i am sending email to user as said in conversation by taking email id of a user.

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
- **getfollowers()** : this functions will use twitter api and return followers of the user by taking parameters like:
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

## Coding Guidelines 
- **UI Framework** : As i am using laravel application so that is following **Twitter Bootstrap** so i have used as much as i can of twitter Bootstrap
- **Responsive** : I have taken care of this and for that media queries are written in '*public/css/css_file.css*'.Also checked in different mobile like iphone4,iphone6,Mi A1 and Mi Note3.It works succesfully in all the mobiles and followers are downloaded in desired formats.
- **Code Organisation** : As i am using laravel application so it is not possible to put all the 3rd party codes and libraries in **lib** folder because controller's code should be in *app* folder, 3rd party libraries are in *vendor & composer.json* folder. 
- **Coding Standard** : I have used **[PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)** & **[JSHint](http://jshint.com/)** for cheking coding standards in localhost and i have tried to minimize the *errors & warnings*.
- **GitHub** : From the starting of this challenge i have started to use **[GitHub](https://github.com/)** and commiting and pushing the code in my **[GitHub Repository](https://github.com/VishalMSoni/twitter_timeline_challenge)**. 

## List of some important files
- **[SendEmailJob.php](https://github.com/VishalMSoni/twitter_timeline_challenge/blob/master/app/Jobs/SendEmailJob.php)**
- **[SocialAuthTwitterController.php](https://github.com/VishalMSoni/twitter_timeline_challenge/blob/master/app/Http/Controllers/SocialAuthTwitterController.php)**
- **[SocialTwitterAccountService.php](https://github.com/VishalMSoni/twitter_timeline_challenge/blob/master/app/Services/SocialTwitterAccountService.php)**
- **[twitterTimeline.blade.php](https://github.com/VishalMSoni/twitter_timeline_challenge/blob/master/resources/views/twitterTimeline.blade.php)**
- **Model for relationship**
  - **[SocialTwitterAccount.php](https://github.com/VishalMSoni/twitter_timeline_challenge/blob/master/app/SocialTwitterAccount.php)**
  - **[User.php](https://github.com/VishalMSoni/twitter_timeline_challenge/blob/master/app/User.php)**

#### Created By : 
* Vishal Murlidhar Soni
* Email : vishal.s.btechi15@ahduni.edu.in , vishalsoni611@gmail.com
* School of Engineering and Applied Science (Ahmedabad University)
* Phone no. 9429707486/8460806485
