<!DOCTYPE html>
<html>
<head>
    <title>Twitter Timeline Challenge - RTCamp</title>
    <style type="text/css">
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        table td, table th {
            padding: 5px;
            text-align: center;
            border:1px solid black;
        }
        .titlePDF {
            text-decoration: underline;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <br/>
        <a href="{{ route('htmlToPdfView', ['download'=>'pdf']) }}"></a>
        <br/>

        <h3 class="titlePDF">All Followers</h3>

        <table>
            <tr>
                <th>No.</th>
                <th>Screen Name</th>
                <th>Name</th>
            </tr>
            @foreach ($followersArray as $key => $value)
                <tr>
                    <td>{{ $key + 1}}</td>
                    <td>{{ $value['name'] }}</td>
                    <td>{{ $value['screen_name'] }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</body>
</html>