var controller = {};

controller.api = null;
controller.hash = "1";
controller.callback = {};
controller.dbQueue = "";
controller.playState = "";
controller.done = false;
controller.initDone = false;
controller.inAjax = false;
controller.queueAdd = 1;
controller.callbackWait = false;
controller.rdioQueue = "";
controller.nowPlaying = {};

controller.run = function run() {
  if (controller.initDone == true && controller.dbQueue != "" && controller.playState == 1 && controller.rdioQueue != "" && controller.rdioQueue[0].key != controller.dbQueue[0].key)
  {
    controller.dbQueue.splice(0,1);
    controller.build_portlets();
  }
  if (controller.callbackWait == false && controller.initDone == true && controller.dbQueue != "" && controller.playState == 1) 
  {
    console.log(controller.dbQueue.length);
    controller.callbackWait = true;
    controller.api.rdio_queue(controller.dbQueue[controller.queueAdd].key);
    controller.queueAdd += 1;
  }
  if (controller.initDone == true && controller.inAjax == false)
  {
    controller.inAjax = true;
    $.getJSON('http://wizuma.com/index.php/dj_json/get_queue/' + controller.hash, function(data) {
      if (data.hash != controller.hash) 
      {
        controller.hash = data.hash;
        controller.dbQueue = data.queue;
        console.log("looped");
        controller.build_portlets(); 
      }
      controller.inAjax = false;
    });
  }
}

var int=setInterval(function() {controller.run()},1000);

controller.build_portlets = function build_portlets() {
  str = "";
        for(var row in controller.dbQueue) {
          str += "<div class=\"portlet\"><div class=\"portlet-header\">" + controller.dbQueue[row].name + "</div><div class=\"portlet-content\"><img src=\"" + controller.dbQueue[row].icon_url + "\"/><p>By: " + controller.dbQueue[row].artist + "</p><p>From: " + controller.dbQueue[row].album + "</p></div></div>";
        }
        document.getElementById("songQueue").innerHTML = str;
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
  $('#stop').click(function() { controller.api.rdio_stop(); });
  $('#pause').click(function() { controller.api.rdio_pause(); });
  $('#previous').click(function() { controller.api.rdio_previous(); });
  $('#next').click(function() { controller.api.rdio_next(); });
  $( "#progressbar" ).progressbar({value: 40});
  $( "#progressbar" ).progressbar({value: 60});
  $( "#progressbar" ).progressbar({value: 80});
}

controller.remove = function remove(key) {
  $.getJSON('http://wizuma.com/index.php/dj_json/remove/' + key, function(data) {
    if (data == false)
    {
      alert('Song failed to remove');
    }
  });
}

controller.callback.ready = function ready(user) {
  // Called once the API SWF has loaded and is ready to accept method calls.

  // find the embed/object element
  controller.api = $('#apiswf').get(0);

  controller.api.rdio_startFrequencyAnalyzer({
    frequencies: '8-band',
    period: 100
  });
  
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
  $( "#progressbar" ).progressbar({value: 100});
  controller.api.rdio_sendState();
  controller.initDone = true;
  $( "#dialog-confirm" ).dialog( "close" );
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
}

controller.callback.playingSourceChanged = function playingSourceChanged(playingSource) {
  // The currently playing source changed.
  // The source metadata, including a track listing is inside playingSource.
  document.getElementById('track').innerHTML = playingSource.name + "<br/>" + playingSource.artist;
  $( "#progress" ).slider({
      min: 0,
      max: playingSource.duration,
      value: 0
    });
  controller.nowPlaying = playingSource;
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
  $( "#progress" ).slider({ value: position});
}

controller.callback.queueChanged = function queueChanged(newQueue) {
  // The queue has changed to newQueue.
  controller.rdioQueue = newQueue;
  controller.callbackWait = false;
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