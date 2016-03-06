<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Laravel 5</div>
            </div>
        </div>
        <script>
//            var conn = new WebSocket('ws://localhost:8000');
//            conn.onopen = function(e) {
//                console.log("Connection established!");
//                conn.send('Hello World!');
//            };
//
//            conn.onmessage = function(e) {
//                console.log(e.data);
//            };
        </script>

        <script src="http://autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>
        <script>
            var conn = new ab.Session('ws://localhost:8080',
                function() {
                    conn.subscribe('onNewData', function(topic, data) {
                        // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                        console.log(data.data);
                    });
                },
                function() {
                    console.warn('WebSocket connection closed');
                },
                {'skipSubprotocolCheck': true}
            );
        </script>
    </body>
</html>
