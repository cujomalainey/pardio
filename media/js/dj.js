var controller = {};

controller.api = null;
controller.hash = "";
controller.callback = {};
controller.dbQueue = "";
controller.playState = "";
controller.done = false;

controller.run = function run() {
  // primary loop
  // check hash
  // check callback
  // run commands
}

controller.sleep = function sleep(delay) {
        var start = new Date().getTime();
        while (new Date().getTime() < start + delay);
      }

controller.build_portlets = function build_portlets() {
  $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
    .find( ".portlet-header" )
    .addClass( "ui-widget-header ui-corner-all" )
    .prepend( "<span class='ui-icon ui-icon-plusthick'></span>")
    .end()
    .find( ".portlet-content" );
 
  $( ".portlet-header .ui-icon" ).click(function() {
    $( this ).toggleClass( "ui-icon-plusthick" ).toggleClass( "ui-icon-minusthick" );
    $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
  });
  $( ".column" ).disableSelection();
  $('.portlet-content').toggle();
}

controller.get_queue = function get_queue() {
  $.getJSON('http://wizuma.com/index.php/voter_json/get_queue/' + controller.hash, function(data) {
        i = false;
        str = "";
        controller.dbQueue = data.queue;
        controller.hash = data.hash;
        for(var row in controller.dbQueue) {
                str += "<div class=\"portlet\"><div class=\"portlet-header\">" + controller.dbQueue[row].name + "</div><div class=\"portlet-content\"><img src=\"" + controller.dbQueue[row].icon_url + "\"/><p>By: " + controller.dbQueue[row].artist + "</p><p>From: " + controller.dbQueue[row].album + "</p></div></div>";
        }
        document.getElementById("songQueue").innerHTML = str;
        controller.build_portlets();
  });
}

controller.init = function init() {
  // on page load use SWFObject to load the API swf into div#apiswf
  var flashvars = {
    'playbackToken': $("#token").val(), // from token.js
    'domain': "wizuma.com",                // from token.js
    'listener': 'controller.callback'    // the global name of the object that will receive callbacks from the SWF
    };
  var params = {
    'allowScriptAccess': 'always'
  };
  var attributes = {};
  swfobject.embedSWF('http://www.rdio.com/api/swf/', // the location of the Rdio Playback API SWF
      'apiswf', // the ID of the element that will be replaced with the SWF
      1, 1, '9.0.0', 'expressInstall.swf', flashvars, params, attributes);

  //build sortable list here, setup callback for rebuild queue if list changed
  $( "#progressbar" ).progressbar({value: 20});
  $('#play').click(function() {
      if (controller.playState == 0 || controller.playState == 4)
      {
        controller.api.rdio_play();
        console.log("1");
      }
      else if (controller.playState == 2)
      {
        controller.api.rdio_play(controller.dbQueue[0].key);
        console.log("2");
      }
  });
  $('#queue').click(function() {
    $.getJSON('http://wizuma.com/index.php/dj_json/get_queue', function(data) {
      for(var row in data) {
        if (row != 0)
        {
          controller.api.rdio_queue(data[row].key);
          console.log(data[row].key);
        }
      }
    });
  });
  $('#stop').click(function() { controller.api.rdio_stop(); });
  $('#pause').click(function() { controller.api.rdio_pause(); });
  $('#previous').click(function() { controller.api.rdio_previous(); });
  $('#next').click(function() { controller.api.rdio_next(); });
  $( "#progressbar" ).progressbar({value: 40});
  this.get_queue();
  $( "#progressbar" ).progressbar({value: 60});
  var int=self.setInterval(controller.run(),500);
  $( "#progressbar" ).progressbar({value: 80});
  setTimeout(controller.finalInit, 1000);
  //run test (run rdio_test and wait for playstate callback)
}

controller.finalInit = function finalInit()
{
  while (controller.done == false)
  {
    controller.sleep(2000);
    console.log("looped");
  }
  controller.api.rdio_sendState();
  $( "#progressbar" ).progressbar({value: 100});
  $( "#dialog-confirm" ).dialog( "close" );
}

controller.callback.ready = function ready(user) {
  // Called once the API SWF has loaded and is ready to accept method calls.

  // find the embed/object element
  controller.api = $('#apiswf').get(0);

  controller.api.rdio_startFrequencyAnalyzer({
    frequencies: '8-band',
    period: 100
  });
  controller.done = true;
  if (user == null) {
    $('#nobody').show();
  } else if (user.isSubscriber) {
    $('#subscriber').show();
  } else if (user.isTrial) {
    $('#trial').show();
  } else if (user.isFree) {
    $('#remaining').text(user.freeRemaining);
    $('#free').show();
  } else {
    $('#nobody').show();
  }

  console.log(user);
  $( ".column" ).sortable({
      connectWith: ".column"
    });

}

controller.callback.freeRemainingChanged = function freeRemainingChanged(remaining) {
  $('#remaining').text(remaining);
}

controller.callback.playStateChanged = function playStateChanged(playState) {
  // The playback state has changed.
  // The state can be: 0 - paused, 1 - playing, 2 - stopped, 3 - buffering or 4 - paused.
  controller.playState = playState;
  console.log("Play state: " + playState);
}

controller.callback.playingTrackChanged = function playingTrackChanged(playingTrack, sourcePosition) {
  // The currently playing track has changed.
  // Track metadata is provided as playingTrack and the position within the playing source as sourcePosition.
  if (playingTrack != null) {
    $('#track').text(playingTrack['name']);
    $('#album').text(playingTrack['album']);
    $('#artist').text(playingTrack['artist']);
    $('#art').attr('src', playingTrack['icon']);
  }
}

controller.callback.playingSourceChanged = function playingSourceChanged(playingSource) {
  // The currently playing source changed.
  // The source metadata, including a track listing is inside playingSource.
}

controller.callback.volumeChanged = function volumeChanged(volume) {
  // The volume changed to volume, a number between 0 and 1.
}

controller.callback.muteChanged = function muteChanged(mute) {
  // Mute was changed. mute will either be true (for muting enabled) or false (for muting disabled).
}

controller.callback.positionChanged = function positionChanged(position) {
  //The position within the track changed to position seconds.
  // This happens both in response to a seek and during playback.
  $('#position').text(position);
}

controller.callback.queueChanged = function queueChanged(newQueue) {
  // The queue has changed to newQueue.
  console.log(newQueue);
}

controller.callback.shuffleChanged = function shuffleChanged(shuffle) {
  // The shuffle mode has changed.
  // shuffle is a boolean, true for shuffle, false for normal playback order.
}

controller.callback.repeatChanged = function repeatChanged(repeatMode) {
  // The repeat mode change.
  // repeatMode will be one of: 0: no-repeat, 1: track-repeat or 2: whole-source-repeat.
}

controller.callback.playingSomewhereElse = function playingSomewhereElse() {
  // An Rdio user can only play from one location at a time.
  // If playback begins somewhere else then playback will stop and this callback will be called.
}

controller.callback.updateFrequencyData = function updateFrequencyData(arrayAsString) {
  // Called with frequency information after apiswf.rdio_startFrequencyAnalyzer(options) is called.
  // arrayAsString is a list of comma separated floats.

  var arr = arrayAsString.split(',');

  $('.frequency').each(function(i) {
    $(this).height(parseInt(parseFloat(arr[i])*700));
  })
}