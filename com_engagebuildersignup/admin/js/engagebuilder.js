function validateApiKey(){
    console.log('validate');
    api = document.getElementById('apikey');
    if (api.value == ''){
        alert('Please fill api key field.');
        return false;
    }
    return true;
}
