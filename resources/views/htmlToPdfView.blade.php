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
                <th>Id</th>
                <th>Screen Name</th>
                <th>Name</th>
            </tr>
            @foreach ($allFollowers as $key => $value)
                @if ($value['users'])
                    @foreach ($value['users'] as $inner_key => $inner_value)
                        <tr>
                            <td>{{ $inner_value['id'] }}</td>
                            <td>{{ $inner_value['screen_name'] }}</td>
                            <td>{{ $inner_value['name'] }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </table>
    </div>
</body>
</html>