function updateUser() {
    // close previous errors

    var username1 = document.getElementById('username').value;
    var password = document.getElementById('password').value;
    var pwd1 = document.getElementById('new_pwd1').value;
    var pwd2 = document.getElementById('new_pwd2').value;

    if (username1.length < 5) {
         errors.append("Missing username");
    }

    if (password.length > 0) {
        errors = errors.concat(check_passwords(pwd1, pwd2));
     }

    // update user data
    if (confirm("Are you sure you want to proceed?")) {
        $.post("update_user_info.php",{username1:username1, password:password, pwd1:pwd1, pwd2:pwd2}, function(resp) {
            console.log(resp);
            if (resp.status === 200) {
                // clear the form data
            }
        });
    }
}