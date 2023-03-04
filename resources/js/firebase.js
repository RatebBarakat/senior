import { initializeApp } from "https://www.gstatic.com/firebasejs/9.17.2/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.17.2/firebase-analytics.js";
import { getMessaging,getToken } from "https://www.gstatic.com/firebasejs/9.17.2/firebase-messaging.js";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyDcfcEEU4Nb5x1yN9NQotk_oiuAtD2E1xY",
    authDomain: "senior-notification.firebaseapp.com",
    projectId: "senior-notification",
    storageBucket: "senior-notification.appspot.com",
    messagingSenderId: "179111896854",
    appId: "1:179111896854:web:16997a549706cbe65e5237",
    measurementId: "G-WD90LMEHK0"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

const messaging = getMessaging(app);
getToken(messaging, { vapidKey: 'BHCUv8DdF5UFZ9ptJZZszT4Ze88deEWwRt5uYQa8Dw1J_h-TCqHHyJIsEFTz15yu8P5civYx735Ki_eQP8RuJmEBHCUv8DdF5UFZ9ptJZZszT4Ze88deEWwRt5uYQa8Dw1J_h-TCqHHyJIsEFTz15yu8P5civYx735Ki_eQP8RuJmE' }).then((currentToken) => {
    if (currentToken) {
        console.log(currentToken)
    } else {
        // Show permission request UI
        console.log('No registration token available. Request permission to generate one.');
        // ...
    }
}).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
    // ...
});
