$(document).ready(function() {
    // Auto textarea height
    $('.auto_height').each(function () {
        this.style.overflowY = "hidden";
    }).on('input', function () {
        this.style.height = 0;//'auto';
        this.style.height = (this.scrollHeight) + 'px';
        //$(this).outerHeight(0).outerHeight(this.scrollHeight);
    });
    
    // Load all messages
    var latest_id = 0;
    var sent_ids = [];
    function get_messages(from){
        $.ajax({
            type: 'GET',
            url: "/scripts/get_messages.php",
            data: {from: from},
            dataType: 'json',
            success: function (data) {
                var box = document.getElementById("messages");
                var atBottom = box.scrollHeight - box.clientHeight <= box.scrollTop + 1;
                for(key in data){
                    // If the message wasn't sent by us
                    if (!sent_ids.includes(data[key].id)){
                        // Null usernames are printed as "Anon"
                        var username = data[key].username!=null? data[key].username: "Anon";
                        // Print message to chat box
                        $('#messages').append("<div>"+username+": "+data[key].content+"<div>");
                    }
                }
                latest_id = data[data.length-1].id;
                if (atBottom){
                    $("#messages").scrollTop($("#messages")[0].scrollHeight);
                }
            },
            error: function(data) {
                //alert(data.responseText);
                alert(JSON.stringify(data, null, 4));
            }
        });
    }
    get_messages(latest_id);

    //Load new messages every interval
    setInterval(function() {
        get_messages(latest_id+1);
    }, 5000);

    // Submit message ajax
    var send = $('#message_form');
    send.submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: send.attr('method'),
            url: send.attr('action'),
            data: send.serialize(),
            dataType: 'json',
            success: function(data) {
                // Null usernames are printed as "Anon"
                var username = data[0].username!=null? data[0].username: "Anon";
                // Print message to chat box
                $('#messages').append("<div>"+username+": "+data[0].content+"<div>");
                sent_ids.push(Number(data[0].id));
                $("#messages").scrollTop($("#messages")[0].scrollHeight);
            },
            error: function(data) {
                alert(data.responseText);
                //alert(data+JSON.stringify(data, null, 4));
            }
        });
        $('#message_text').val("");
    });

    // Enter submits and shift+enter makes a new line
    $('#message_text').keydown(function(e) {
        if (e.keyCode == 13 && !e.shiftKey) {
            e.preventDefault();
            $('#message_form').submit();
        }
    });
});