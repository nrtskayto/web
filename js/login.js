function login(){
  var username = document.getElementById('username').value
  var password = document.getElementById('password').value

  
  if(username=="" && password==""){
    window.alert("fill the fields");
  }else{
    $.post("login.php",{'username':username,'password':password},function(data){
      console.log(data); 
    if(data == "admin"){
      location.replace("admin.html");
    }else if(data == "1" ){
      sessionStorage.setItem("user_name", username);
      location.replace("user.html");
    }else if (data == "2"){
      window.alert("lathos username");
      //wrong username
    } else if(data == "0"){
      //wrong password
      window.alert("lathos kwdikos");
    } else{
      //location.replace("error.html");
      window.alert(data);
    }
  });
} 
}
     