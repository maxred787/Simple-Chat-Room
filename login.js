function loadSection(isLogin=true) {
    if (isLogin) {
        $("#inputSection").html('\
            <h2>Login to Chatroom</h2>\
            <form action="login.php" method="POST" id="loginForm">\
                <fieldset name="loginInfo">\
                    <legend>Login</legend>\
                    <label for="formEmail">Email:</label>\
                    <input type="email" name="user" id="formEmail" pattern="[a-z0-9]+@connect.hku.hk" required>\
                    <label for="formPassword">Password:</label>\
                    <input type="password" name="password" id="formPassword" required>\
                    <input type="submit" name="login" value="login" id="submitBtn">\
                </fieldset>\
            </form>\
            <p>Click <a href="" id="loginRegSwap">here</a> to register an account</p>\
            <p id="errMsg"></p>\
        ');
    } else {
        $("#inputSection").html('\
            <h2>Register an Account</h2>\
            <form action="login.php" method="POST" id="loginForm">\
                <fieldset name="registrationInfo">\
                <legend>Registration</legend>\
                    <label for="formEmail">Email:</label>\
                    <input type="email" name="user" id="formEmail" pattern="[a-z0-9]+@connect.hku.hk" required>\
                    <label for="formPassword">Password:</label>\
                    <input type="password" name="password" id="formPassword" required>\
                    <label for="formConfirm">Confirm:</label>\
                    <input type="password" id="formConfirm" required>\
                    <input type="submit" name="register" value="register" id="submitBtn">\
                </fieldset>\
            </form>\
            <p>Click <a href="" id="loginRegSwap">here</a> for login</p>\
            <p id="errMsg"></p>\
        ')
    }
    formValidation(isLogin);
}

function formValidation(isLogin) {
    $("#loginRegSwap").click(e => {
        e.preventDefault();
        loadSection(!isLogin);
    })

    let formEmail = $("#formEmail");
    formEmail.on('blur', async e => {
        checkEmail(formEmail[0].value);
        let response = await fetch("check.php?user=" + formEmail[0].value);
        if (response.status == 200) {
            let data = await response.json();
            if (data.result) {
                removeErrMsg();
            } else {
                if (isLogin) {
                    $("#errMsg").html('Cannot find your email record');
                }
            }
        }
    })
    
    let formPassword = $("#formPassword");
    formPassword.on('blur', e => {
        checkPw(formPassword[0].value);
    })

    let loginForm = $("#loginForm");
    loginForm.on('submit', async e => {
        let emailErr = checkEmail(formEmail[0].value, true);
        if (!emailErr && !checkPw(formPassword[0].value)) {
            if ($("#formConfirm").length > 0) {
                if (formPassword[0].value == $("#formConfirm")[0].value) {
                    removeErrMsg();
                } else {
                    e.preventDefault();
                    $("#errMsg").html('Mismatch passwords!!')
                }
            } else {
                removeErrMsg();
            }
        } else {
            e.preventDefault();
        }
    })
}

function checkEmail(email) {
    let emailErr = true;
    if (email == '') {
        $("#errMsg").html('Missing email address');
    } else {
        let emailArr = email.split('@');
        if (emailArr.length == 2) {
            if (!emailArr[1].match("connect.hku.hk")) {
                $("#errMsg").html('Please enter a valid HKU @connect email address')
            } else {
                removeErrMsg();
                emailErr = false;
            }
        } else {
            $("#errMsg").html('Please enter a valid HKU @connect email address')
        }
    }
    return emailErr;
}

function checkPw(pw) {
    let pwError = true;
    if (pw == '') {
        $("#errMsg").html('Please provide a password');
    } else {
        removeErrMsg();
        pwError = false;
    }
    return pwError
}

function removeErrMsg() {
    $('#errMsg').html('');
}

$(document).ready(_ => {
    formValidation(true);
})