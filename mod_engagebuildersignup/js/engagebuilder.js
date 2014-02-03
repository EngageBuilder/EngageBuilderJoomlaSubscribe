function validateEmail(){
    email = document.getElementById('eb_email');
    if (email.value == ''){
        alert('Please fill your email.');
        return false;
    }
    if( !(/\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/.test(email.value)) ) {
        alert('Email field its incorrect.');
        return false;
    }
    return true;
}
