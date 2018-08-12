@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                    
                    <br>
                    <br>
                    <a class="btn btn-primary" href="{{ url('/twitterTimeline') }}">
                        Get tweets
                    </a>
                    <br>
                    <br>

                    {{-- <div class="fontStyle">    
                        <strong>
                            There are two options:<br> 1.XML (only id's will be shown -> 5000 id's at a time*15 call as per rate limit = 75000 followers)
                        </strong>
                        <br>
                        <strong>
                            2.XML (id's , screen_name & Name will be shown -> 200 at a time*15 call = 3000 followers)
                        </strong>
                    </div>
                    <br><br> --}}

                    <form action="{{ url('/downloadFollowers') }}" id="downloadForm">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <input class="form-control" id="followerName" type="text" name="followerName" placeholder="Enter valid Screen name of user" required>
                            </div>
                            <div class="col-md-3 topStyle bottomStyle">
                                <input type="radio" name="downloadType" value="xml_id" id="downloadType" required>XML (only id's)<br>
                                <input type="radio" name="downloadType" value="xml_name" id="downloadType">XML (id's & Names)<br>
                                <input type="radio" name="downloadType" value="pdf" id="downloadType">PDF
                            </div>
                            <div class="col-md-2">
                                <button type="submit" id="get_followers" class="btn btn-success" onclick="clearDetails()">Download Followers</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection