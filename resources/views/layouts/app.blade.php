<!DOCTYPE html>
<html>
    <head>
        <!-- Anti-flicker snippet (recommended)  -->
        <style>.async-hide { opacity: 0 !important} </style>

        <title>Minimum Network</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link rel="shortcut icon" type="image/x-icon" href="{{ env('APP_URL') }}/image/favicon.ico" />
        <link href="https://fonts.googleapis.com/css?family=Roboto:200,300,400,600" rel="stylesheet">
        <link href="https://storage.googleapis.com/non-spec-apps/mio-icons/latest/outline.css" rel="stylesheet">  
        <link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/flexboxgrid/6.3.1/flexboxgrid.min.css" type="text/css" >
        <link rel="stylesheet" href="{{ env('APP_URL').elixir('css/app.css') }}">
    <body>
        <div class="container">
            <div id="app" v-cloak></div>
        </div>
        <!-- <script src="{{ env('APP_URL') }}/js/peer.js"></script>
        <script src="{{ env('APP_URL') }}/js/adapter.debug.js"></script>
        <script src="{{ env('APP_URL') }}/js/adapter.screenshare.js"></script>
        <script src="{{ env('APP_URL') }}/js/screenshare.js"></script>
        <script src="https://skyway.io/dist/screenshare.js"></script> -->
        <!-- <script src="https://cdn.webrtc.ecl.ntt.com/screenshare-latest.js"></script> -->
        <script type="text/javascript">
            var ua = window.navigator.userAgent;
            if (ua.indexOf("MSIE ") === -1 && ua.indexOf("Trident") === -1) {
                var tag = document.createElement('script');
                tag.async = false;
                tag.src = "{{ env('APP_URL') }}/js/skyway-latest.js";
                var body = document.getElementsByTagName('body');
                body[0].appendChild(tag);

                var tag1 = document.createElement('script');
                tag1.async = false;
                tag1.src = "https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.7/lib/draggable.bundle.js";
                var body1 = document.getElementsByTagName('body');
                body1[0].appendChild(tag1);
            } else {
                var tag = document.createElement('script');
                tag.async = false;
                tag.src = "{{ env('APP_URL') }}/js/peer-client.js";
                var body = document.getElementsByTagName('body');
                body[0].appendChild(tag);
            }
        </script>
        <!-- <script src="{{ env('APP_URL') }}/js/peer-client.js"></script> -->
        <!-- <script src="https://cdn.webrtc.ecl.ntt.com/skyway-latest.js"></script> -->
        <!-- <script src="https://cdn.webrtc.ecl.ntt.com/skyway-1.1.19.js"></script> -->   
        <!-- <script src="https://webrtc.github.io/adapter/adapter-latest.js"></script> -->
        <script src="{{ env('APP_URL') }}/js/adapter-latest.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.7/lib/draggable.bundle.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>
        <!-- m2b-81 -->
        <script src="{{ env('APP_URL') }}/js/RecordRTC.js"></script>
        <script src="{{ env('APP_URL') }}/js/bodymovin.js" type="text/javascript"></script>        
        <!-- m2b-81 -->        
        <script src="{{ env('APP_URL') }}/js/app.js"></script>
        <script src="https://cdn.temasys.com.sg/adapterjs/0.15.x/adapter.screenshare.js"></script>
    </body>
</html>
