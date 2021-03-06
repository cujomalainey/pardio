var controller = {};

controller.api = null;            //api object
controller.hash = "1";            //queue hash
controller.callback = {};         //callback object
controller.dbQueue = "";          //mirror of PHP queue array
controller.playState = "";        //integer of playstate
controller.initDone = false;      //initialization switch
controller.inAjax = false;        //ajax switch
controller.queueAdd = 0;          //temp incremental key TO BE DELETED
controller.callbackWait = false;  //waiting on callback object
controller.rdioQueue = "";        //rdio array of queue
controller.nowPlaying = {};       //now playing object
controller.canStreamCheck = [];   //stream checking queue
controller.move = {to:"", key:""};
controller.messing = false;
controller.callbackCounter = 0;
controller.lineFlag = false;

controller.run = function run() {
  //secondary insert function, move to index
  if (controller.move.to != "" && controller.callbackWait == false && controller.playState == 1)
  {
    for (var row in controller.rdioQueue)
    {
      if (controller.move.key == controller.rdioQueue[row].key)
      {
        console.log(controller.move);
        console.log(row + "->" + controller.move.to);
        controller.api.rdio_moveQueuedSource(row, controller.move.to);
        controller.move.to = "";
        break;
      }
    }
  }
  //can_stream check function
  if (controller.initDone == true && controller.dbQueue != "" && controller.canStreamCheck.length != 0)
  {
    for(var key in controller.canStreamCheck) {
      for(var row in controller.rdioQueue) {
        if (controller.canStreamCheck[key] == controller.rdioQueue[row].key)
        {
          canStream = (controller.rdioQueue[row].canStream) ? 1 : 0;
          $.getJSON('http://wizuma.com/index.php/dj_json/mark_stream/' + controller.rdioQueue[row].key + "/" + canStream, function(data) {
            controller.canStreamCheck.splice(controller.canStreamCheck.indexOf(controller.canStreamCheck[key].key),1);
            if (controller.rdioQueue[row].canStream == false)
            {
              controller.api.rdio_removeFromQueue(row);
            }
          });
        }
      }
    }
  }
  //main queue checking function
  if (controller.initDone == true && controller.dbQueue != "" && controller.callbackWait == false && controller.move.to == "" && controller.playState == 1 && controller.messing == false)
  {
    if (controller.dbQueue.length > controller.rdioQueue.length)
    {
      i = 0;
      for(var row in controller.rdioQueue) 
      {
        if (controller.checkQueue(row) == true)
        {
          break;
        }
        i = row;
      }
      if (controller.callbackWait == false)
      {
        i++;
        controller.api.rdio_queue(controller.dbQueue[i].key);
        controller.callbackWait = true;
      }
    }
    else if (controller.dbQueue.length < controller.rdioQueue.length)
    {
      i = 0;
      for(var row in controller.dbQueue) 
      {
        if (controller.checkQueue(row) == true)
        {
          break;
        }
        i++;
      }
      if (controller.callbackWait == false)
      {
        console.log("deleting at:" + i);
        controller.api.rdio_removeFromQueue(i);
        controller.callbackWait = true;
      }
    }
    else
    {
      for(var row in controller.dbQueue) 
      {
        if (controller.checkQueue(row) == true)
        {
          break;
        }
      }
    }
  }
  //queue request function, long polling
  if (controller.initDone == true && controller.inAjax == false)
  {
    controller.inAjax = true;
    $.getJSON('http://wizuma.com/index.php/dj_json/get_queue/' + controller.hash, function(data) {
      if (data.hash != controller.hash) 
      {
        controller.hash = data.hash;
        controller.dbQueue = data.queue;
        controller.canStreamCheck = [];
        for(var key in data.queue) {
          if (data.queue[key].can_stream == null)
          {
            controller.canStreamCheck.push(data.queue[key].key);
          }
        }
        controller.build_portlets(); 
      }
      controller.inAjax = false;
    });
  }
  //make sure callbacks always come through
  if (controller.callbackWait == true)
  {
    controller.callbackCounter++;
    if (controller.callbackCounter > 4)
    {
      controller.api.rdio_sendState();
    }
  }
}

var int=setInterval(function() {controller.run()},1500);

controller.checkQueue = function checkQueue(row)
{
  if (row == 0)
  {
    if (controller.dbQueue[row].key != controller.rdioQueue[row].key)
    {
      if (controller.lineFlag == false)
      {
        controller.api.rdio_sendState();
        controller.callbackWait = true;
        controller.lineFlag = true;
      }
      else
      {
        controller.lineFlag = false;
        controller.insertQueue(row);
        controller.inAjax = false;
      }
      return true;
    }
    else
    {
      return false;
    }
  }
  else 
  {
    if (controller.dbQueue[row].key != controller.rdioQueue[row].key)
    {
      controller.insertQueue(row);
      return true;
    }
    else
    {
      return false;
    }
  }
}

controller.insertQueue = function insertQueue(location)
{
  controller.api.rdio_queue(controller.dbQueue[location].key);
  controller.move.to = location;
  controller.move.key = controller.dbQueue[location].key;
  controller.callbackWait = true;
} 

controller.build_portlets = function build_portlets() {
  str = "";
        for(var row in controller.dbQueue) {
          str += "<div class=\"portlet\"><div class=\"portlet-header\">" + controller.dbQueue[row].name + "</div><div class=\"portlet-content\"><img src=\"" + controller.dbQueue[row].icon_url + "\"/><p>By: " + controller.dbQueue[row].artist + "</p><p>From: " + controller.dbQueue[row].album + "</p><button type=\"button\" onClick=\"javascript:controller.remove(\'" + controller.dbQueue[row].key + "\')\">Delete</button></div></div>";
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
      }
      else if (controller.playState == 2)
      {
        controller.api.rdio_play(controller.dbQueue[0].key);
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
      value: 0,
      slide: function( event, ui ) {
        controller.api.rdio_seek(ui.value);
      }
    });
  $.getJSON('http://wizuma.com/index.php/dj_json/played/' + playingSource.key, function(data) {
    console.log("song marked as play: " + data);
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
  controller.callbackCounter = 0;
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