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

                    <form action="{{ url('/downloadFollowers') }}" id="downloadForm">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <input class="form-control" id="followerName" type="text" name="followerName" placeholder="Enter valid Screen name of user" required>
                            </div>
                            <!-- <div class="col-md-3 topStyle bottomStyle">
                                <input type="radio" name="downloadType" value="xml" id="downloadType" required>XML<br>
                                <input type="radio" name="downloadType" value="pdf" id="downloadType">PDF
                            </div> -->
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