$(function() {
    get_update();
    $("#request").keyup(function(event){
        if(event.keyCode == 13){
            $.mobile.loading( 'show' );
            url = 'http://wizuma.com/index.php/voter_json/search/' + encodeURIComponent(document.getElementById("request").value);
            $.getJSON(url, function(data) {
                str = "";
                results = data.result.results;
                for(var line=0;line<results.length;line++) {
                    str += "<div data-role=\"collapsible\" data-theme=\"b\" data-content-theme=\"d\" data-inset=\"false\"><h3>" + results[line].name + "</h3><div class=\"ui-grid-a\"><div class=\"ui-block-a pic\"><img src=\"" + results[line].icon + "\"/></div><div class=\"ui-block-b main\"><p>By: " + results[line].artist + "</p><p>From: " + results[line].album + "</p><a data-role=\"button\" data-inline=\"true\" data-icon=\"check\" data-corners=\"true\" data-shadow=\"true\" data-theme=\"b\" href=\"javascript:request(\'" + results[line].key + "\');\" >Request this song!</a></div></div></div>";
                }
                document.getElementById("search-list").innerHTML = str;
                $("#search-list").trigger('create');
                $.mobile.loading( 'hide' );
                $("#lnkDialog").click();
            });
        }
    });
});

var int=self.setInterval(function(){get_update()},3000);

var queue = {};
queue.hash = "a";
queue.inAjax = false

function get_update() {
    //update so its a hash check and only update if hash is different
    if (queue.inAjax == false)
    {
        $.getJSON('http://wizuma.com/index.php/voter_json/get_queue/' + queue.hash, function(data) {
        i = false;
        str = "";
        results = data.queue;
        for(var row in results) {
            if (i == true) {
                str += "<div data-role=\"collapsible\" data-theme=\"b\" data-content-theme=\"d\" data-inset=\"false\"><h3>" + results[row].name + "</h3><div class=\"ui-grid-a\"><div class=\"ui-block-a pic\"><img src=\"" + results[row].icon_url + "\"/></div><div class=\"ui-block-b main\"><p>By: " + results[row].artist + "</p><p>From: " + results[row].album + "</p><form><fieldset data-role=\"controlgroup\" data-type=\"horizontal\"><legend>Vote:</legend><input type=\"radio\" name=\"radio-choice-h-2\" id=\"radio-choice-h-2a\" value=\"1\"><label for=\"radio-choice-h-2a\">Yes</label><input type=\"radio\" name=\"radio-choice-h-2\" id=\"radio-choice-h-2b\" value=\"0\"><label for=\"radio-choice-h-2b\">No</label></fieldset></form></div></div></div>";
            }
            else
            {
                i = true;
                str += "<div data-role=\"collapsible\" data-theme=\"a\" data-content-theme=\"a\" data-inset=\"false\"><h3>Now Playing: " + results[row].name + "</h3><div class=\"ui-grid-a\"><div class=\"ui-block-a pic\"><img src=\"" + results[row].icon_url + "\"/></div><div class=\"ui-block-b main\"><p>By: " + results[row].artist + "</p><p>From: " + results[row].album + "</p></div></div></div>";
            }
        }
        document.getElementById("list").innerHTML = str;
        $("#list").trigger('create');
        document.getElementById("name").innerHTML = data.name;
        document.getElementById("total").innerHTML = "Number of voters: " + data.total;
        queue.hash = data.hash;
        queue.inAjax = false;
        });
    }
}

function request(songKey)
{
    url = "http://wizuma.com/index.php/voter_json/submit/" + songKey;
    $.getJSON(url, function(data) {
        if (data == 0) {
            alert("Your song has been queued");
        }
        else
        {
            alert(data[1]);
        }
    });
    $("#lnkHome").click();
}