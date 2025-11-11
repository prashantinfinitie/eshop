var config = {
	apiKey: "%APIKEY%",
	authDomain: "%AUTHDOMAIN%",
	databaseURL: "%DATABASEURL%",
	projectId: "%PROJECTID%",
	storageBucket: "%STRORAGEBUCKET%",
	messagingSenderId: "%MESSAGINGSENDERID%",
    appId: "%APPID%"
};

firebase.initializeApp(config);
var notification = [];
var icon = '';