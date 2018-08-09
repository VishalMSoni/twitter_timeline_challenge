<table class="table table-bordered">
        @if(!empty($data))
            <thead>
                <tr>
                    <th width="50px">No</th>
                    <th>Twitter Id</th>
                    <th>Message</th>
                    <th>Images</th>
                    <th>Favorite</th>
                    <th>Retweet</th>
                </tr>
            </thead>
            <tbody>
                    @foreach($data as $key => $value)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $value['id'] }}</td>
                            <td>{{ $value['text'] }}</td>
                            <td>
                                @if(!empty($value['extended_entities']['media']))
                                    @foreach($value['extended_entities']['media'] as $v)
                                        <img src="{{ $v['media_url_https'] }}" style="width:100px;">
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ $value['favorite_count'] }}</td>
                            <td>{{ $value['retweet_count'] }}</td>
                        </tr>
                @endforeach
            </tbody>
        @else
            <tr>
                <td colspan="6">There are no tweets.</td>
            </tr>
        @endif
    </table>
    
    <div class="slidercontainer"> 
        @foreach($data as $key => $value)
            <div class="showSlide fade">  
                @if($value['entities']['media'])
                    {{ $image = $value['entities']['media'][0]['media_url'] }}
                    {{ $imageData = base64_encode(file_get_contents($image)) }}
                    <img src="data:image/jpeg;base64,'.$imageData.'" class="img-style">
                    <div class="content">Lorem ipsum dolor sit amet</div>  
                @else
                    <img src="{{ asset('img/default_profile_normal') }}" class="img-style">
                    <div class="content">Lorem ipsum dolor sit amet</div>  
                @endif
            </div>  
        @endforeach
            
        <!-- Navigation arrows -->  
        <a class="left" onclick="nextSlide(-1)">â®</a>  
        <a class="right" onclick="nextSlide(1)">â¯</a>  
    </div>

    @foreach($data as $key => $value)
            <div class="">  
                @if($value['entities']['media'])
                    $image = $value['entities']['media'][0]['media_url']
                    $imageData = base64_encode(file_get_contents($image))
                    <img src="data:image/jpeg;base64,'.$imageData.'" class="img-style">
                    <div class="content">{{ $value['text'] }}</div>  
                @else
                    <img src="{{ asset('img/default_profile_normal') }}" class="img-style">
                    <div class="content">{{ $value['text'] }}</div>  
                @endif
            </div>  
    @endforeach
   

    <section class="slideshow">
        <div class="slideshow-container slide">  
            @foreach($data as $key => $value)
                <div class="text-container">{{ $value['text'] }}</div>  
            @endforeach
        </div>  
    </section>
        
    <div class="slideshow-container slide">
        <img src={{ asset('img/palace-front-view.jpg') }}>
        <div class="text-container">
            <p> I work with text too! And I'm just testing length here and stuff it's cool you know because that's important to do I guess hey did I ever tell you about that time that I did that stuff with the thing</p>
        </div>
        <img src={{ asset('img/palace-front-view.jpg') }}>
        <img src={{ asset('img/palace-front-view.jpg') }}>
    
    </div>