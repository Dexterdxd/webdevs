<!DOCTYPE html>
<html>
<head>
    <title>Facebook Login JavaScript Example</title>
    <meta charset="UTF-8">
</head>
<body>
<script>
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
 
    if (response.status === 'connected') {
 
      testAPI();
    } else {

      document.getElementById('status').innerHTML = 'Please log ' +
          'into this app.';
    }
  }


  function checkLoginState() {
    FB.getLoginStatus(function (response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function () {
    FB.init({
      appId: '488121818633232',
      cookie: true,  
                  
      xfbml: true,  
      version: 'v4.0' 
    });



    FB.getLoginStatus(function (response) {
      statusChangeCallback(response);
    });

  };


  (function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function (response) {
      console.log('response', response);
      console.log('Successful login for: ' + response.name);
      document.getElementById('status').innerHTML =
          'Thanks for logging in, ' + response.name + '!';
      removeLoginButton();
      document.getElementById('logoutDiv').innerHTML = logoutButton();
    });
  }

  function logoutButton() {
    return '<button onclick="logoutFb()">Logout</button>';
  }

  function logoutFb() {
    FB.logout(function (response) {
      console.log('response', response);
      console.log('successfully logout');
      location.reload();
    });
  }

  function removeLoginButton() {
    let p = document.getElementsByTagName('fb:login-button')[0].parentElement;
    let c = document.getElementsByTagName('fb:login-button')[0];
    p.removeChild(c);
  }

</script>


<fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
</fb:login-button>
<div id="status">
</div>
<div id="logoutDiv"></div>
</body>
</html>
