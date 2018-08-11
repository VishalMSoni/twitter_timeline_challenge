<!DOCTYPE html>
<html>
<head>
    <title>Twitter Timeline Challenge - RTCamp</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href={{ asset('css/css_file.css') }}>  
</head>

<body>

<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-7 heroTitle">
            <center><h2>Twitter Timeline Challenge - RTCamp</h2></center>
        </div>
        <div class="col-md-3"></div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="contentByAjax"></div>        
            <div class="contentById">
                <div class="slideshow">
                    @if(count($data)>0)
                        @foreach($data as $key => $value)
                            <div>
                                <p class="userInfo">{{$value['user']['name']}}</p>
                                <p class="userInfo userInfoPad">{{$value['user']['screen_name']}}</p>
                                <p id="textUser">{{ $value['text'] }}</p>
                                <p class="tweetCount">{{$key+1}}</p>
                            </div>
                        @endforeach
                    @else
                        <div>
                            <p>There are no tweets !!</p>
                        </div>
                    @endif
                </div>  
            </div>    
        </div>
        <div class="col-md-4"></div>
    </div>
    <br>
    <br>

    <div class="row">
        <div class="col-md-3">
            <a class="btn btn-success bottomStyle" href="{{ url('/home') }}">Download Followers</a>
        </div>
        <div class="col-md-6 contentStyle">
            <form autocomplete="off">
                <div class="autocomplete" style="width:300px;">
                    <input id="myInput" type="text" name="myFollowers" placeholder="Search followers" required />
                </div>&emsp;&emsp;
                <button type="button" id="get_details_btn" class="btn btn-primary topStyle">Search</button>
            </form>
        </div>
        <div class="col-md-3"></div>
    </div>
    <br>

    <table class="table table-bordered">
        @if(!empty($followers['users']))
            <thead>
                <tr>
                    <th width="50px">No</th>
                    <th>Screen name</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                    @foreach($followers['users'] as $key => $value)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $value['screen_name'] }}</td>
                            <td>{{ $value['name'] }}</td>
                        </tr>
                    @endforeach
            </tbody>
        @else
            <tr>
                <td colspan="6">There are no followers.</td>
            </tr>
        @endif
    </table>
</div>

<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous">
</script>

<script type="text/javascript" src={{ asset('js/handlebars-v4.0.11.js') }}></script>
<script id="entry-template" type="text/x-handlebars-template">
    <div class="slideshow">
        @{{#if data.length}}
            @{{#each data as |value key|}}
                <div>
                    <p class="userInfo">@{{value.user.name}}</p>
                    <p class="userInfo userInfoPad">@{{value.user.screen_name}}</p>    
                    <p>
                        @{{value.text}}
                    </p>
                    <p class="tweetCount">@{{key}}</p>
                </div>
            @{{/each}}
        @{{else}}
            <div>
                <p>
                    There are no tweets !!
                </p>
            </div>
        @{{/if}}
    </div>
</script>

<script type="text/javascript" src={{ asset('js/script.js') }}></script>

<script type="text/javascript">
    var followerNames = [];
    var followerNames = <?php echo json_encode($followers_id); ?>;
    autocomplete(document.getElementById("myInput"),followerNames);
</script>

<script type="text/javascript">
    $("#get_details_btn").on('click',function(e){
        var string = $('#myInput').val();
        $.ajax({
            type:"GET",
            url : "/getDetails",
            data:{'search_string' : string},
            success : function(data){
                $('#myInput').val('');
                $('.contentById').hide();
                var source = document.getElementById("entry-template").innerHTML;
                var template = Handlebars.compile(source);
                var context = {data};
                var theCompiledHtml = template(context); 
                $('.contentByAjax').html(theCompiledHtml);
                $('.contentByAjax').show();
            }, 
            error : function(){
                $('#myInput').val('');
                alert('Please enter valid user screen name !!');
            }
        });
    });
</script>

</body>
</html>