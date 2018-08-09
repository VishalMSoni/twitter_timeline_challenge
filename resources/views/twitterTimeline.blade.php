<!DOCTYPE html>
<html>
<head>
	<title>Twitter Timeline Challenge - RTCamp</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href={{ asset('css/css_file.css') }}>  
</head>

<body>

<div class="container">
    <h2>Twitter Timeline Challenge - RTCamp</h2>

    <div class="contentByAjax"></div>

    <div class="contentById">
        <div id="slider">
            <div id="slider-wrapper">
                @foreach($data as $key => $value)
                    <div class="slide">
                        <p class="caption">{{ $value['text'] }}</p>  
                    </div>
                @endforeach
            </div>
            <div id="slider-nav">
                @foreach($data as $key => $value)
                    <a href="#" data-slide={{$key}}>{{$key+1}}</a>
                @endforeach
            </div>
        </div>  
    </div>

    <strong>
        There are two options:<br> 1.XML (only id's will be shown - 5000 id's at a time)
    </strong>
    <br>
    <strong>
        2.XML (id's , screen_name & Name will be shown - total 200 at a time*15 call as per rate limit = 3000 id's)
    </strong>
    <br><br>

    <form action="{{ url('/downloadFollowers') }}" id="downloadForm">
        <div class="form-group row">
            <div class="col-md-4">
                <input class="form-control" id="followerName" type="text" name="followerName" placeholder="User's Screen name">
            </div>
            <div class="col-md-3">
                <input type="radio" name="downloadType" value="xml_id" id="downloadType">XML (id)<br>
                <input type="radio" name="downloadType" value="xml_name" id="downloadType">XML (Name)
            </div>
            <div class="col-md-2">
                <button type="submit" id="get_followers" class="btn btn-primary">Download</button>
            </div>
        </div>
    </form>

    <form autocomplete="off">
        <div class="autocomplete" style="width:300px;">
            <input id="myInput" type="text" name="myFollowers" placeholder="Search followers">
        </div>
        <button type="button" id="get_details_btn" class="btn btn-primary">Search</button>
    </form>
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
    <div id="slider-wrapper">
        @{{#each data as |value key|}}
            <div class="slide">
                <p class="caption">
                    @{{ value.text }}
                </p>  
            </div>
        @{{/each}}
    </div>
    <div id="slider-nav">
        @{{#each data as |value key|}}
            <a href="#" data-slide=@{{key}}>
                @{{key}}
            </a>
        @{{/each}}
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
                $('.contentById').hide();
                var source   = document.getElementById("entry-template").innerHTML;
                var template = Handlebars.compile(source);
                var context = {data};
                var theCompiledHtml    = template(context);

                function Slider( element ) {
                  this.el = document.querySelector( element );
                  this.init();
                } 

                Slider.prototype = {
                  init: function() {
                    this.links = this.el.querySelectorAll( "#slider-nav a" );
                    this.wrapper = this.el.querySelector( "#slider-wrapper" );
                    this.navigate();
                  },
                  navigate: function() {
                  
                    for( var i = 0; i < this.links.length; ++i ) {
                      var link = this.links[i];
                      this.slide( link ); 
                    }
                  },
                  
                  animate: function( slide ) {
                    var parent = slide.parentNode;
                    var caption = slide.querySelector( ".caption" );
                    var captions = parent.querySelectorAll( ".caption" );
                    for( var k = 0; k < captions.length; ++k ) {
                      var cap = captions[k];
                      if( cap !== caption ) {
                        cap.classList.remove( "visible" );
                      }
                    }
                    caption.classList.add( "visible" ); 
                  },
                  
                  slide: function( element ) {
                    var self = this;
                    element.addEventListener( "click", function( e ) {
                      e.preventDefault();
                      var a = this;
                      self.setCurrentLink( a );
                      var index = parseInt( a.getAttribute( "data-slide" ), 10 ) + 1;
                      var currentSlide = self.el.querySelector( ".slide:nth-child(" + index + ")" );
                      
                      self.wrapper.style.left = "-" + currentSlide.offsetLeft + "px";
                      self.animate( currentSlide );
                      
                    }, false);
                  },
                  setCurrentLink: function( link ) {
                    var parent = link.parentNode;
                    var a = parent.querySelectorAll( "a" );
                    
                    link.className = "current";
                    
                    for( var j = 0; j < a.length; ++j ) {
                      var cur = a[j];
                      if( cur !== link ) {
                        cur.className = "";
                      }
                    }
                  } 
                };

                document.addEventListener( "DOMContentLoaded", function() {
                  var aSlider = new Slider( "#slider" );  
                });

                $('.contentByAjax').html(theCompiledHtml);
            }
        });
    });
</script>

</body>
</html>