<!DOCTYPE html>

<html>
<script language="javascript" src="/js/jquery-1.3.2.js" type="text/javascript"></script>
<script> 
    $.support.cors = true;
</script>

<script>



function getClients()
{
    $('#clientID').empty()
    $.getJSON('http://don2008.psship.com:8080/theory-getdata.php?callback=?',
        'type=client',
        function(res){
            // Use jQuery's each to iterate over the opts value
            $('#clientID').append('<option value=0>Select One</option>');
            $.each(res, function(key, val) {
                $('#clientID').append('<option value="' + val.clientID + '">' + val.clientName + 
                '</option>');
        });

});
}


function getMatters(obj){
    $('#matterID').empty()
    // the callback=? is critical so it knows the response is coming in JSONP and
    // it doesn't block it as a cross-domain script
    $.getJSON('http://don2008.psship.com:8080/theory-getdata.php?callback=?',
        {type: 'matter',
         client: obj},
        function(res){
            // Use jQuery's each to iterate over the opts value
            $('#matterID').append('<option value=0>Select One</option>');
            $.each(res, function(key, val) {
                $('#matterID').append('<option value="' + val.matterID + '">' + val.matterName + 
                '</option>');
        });

});
}
</script>

<html>

<body onload='getClients()'>
<select name='clientID' id='clientID' onchange='getMatters(this.value)'>
    <option>Select One</option>
</select>
<br>
<select name='matterID' id='matterID'>
    <option>Select One</option>
</select>
</body>
</html>