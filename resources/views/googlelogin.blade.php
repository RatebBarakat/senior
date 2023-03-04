<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <title>login</title>
</head>

<body>
    <a href="{{route('google.redirect',['google'])}}">google login</a> <br>

    @auth('social')
        {{auth()->guard('social')->user()->name}}
    @endauth
    <script type="module">
        // Give the service worker access to Firebase Messaging.
        // Note that you can only use Firebase Messaging here. Other Firebase libraries
        // are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
        importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
        importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
        /*
        Initialize the Firebase app in the service worker by passing in the messagingSenderId.
        */
        firebase.initializeApp({
            apiKey: 'api-key',
            authDomain: 'project-id.firebaseapp.com',
            databaseURL: 'https://project-id.firebaseio.com',
            projectId: 'project-id',
            storageBucket: 'project-id.appspot.com',
            messagingSenderId: 'sender-id',
            appId: 'app-id',
            measurementId: 'G-measurement-id',
        });

        // Retrieve an instance of Firebase Messaging so that it can handle background
        // messages.
        const messaging = firebase.messaging();
        messaging.setBackgroundMessageHandler(function (payload) {
            console.log("Message received.", payload);
            const title = "Hello world is awesome";
            const options = {
                body: "Your notificaiton message .",
                icon: "/firebase-logo.png",
            };
            return self.registration.showNotification(
                title,
                options,
            );
        });
    </script>
</body>

</html>


