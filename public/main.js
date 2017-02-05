function getTimeRemaining(endtime){
  var t = Date.parse(endtime) - Date.parse(new Date());

  var seconds = Math.floor((t / 1000) % 60 );
  var minutes = Math.floor((t / 60000) % 60 );
  var hours = Math.floor(( t / (1000*60*60)) % 24 );
  var days = Math.floor(t / (1000*60*60*24) );

  return {
    'total': t,
    'days': days,
    'hours': hours,
    'minutes': minutes,
    'seconds': seconds
  };
}

function initializeClock(id, endtime){
  var clock = document.getElementById(id);

  var updateClock = function() {
    var t = getTimeRemaining(endtime);

    var daysSpan = clock.querySelector('.days');
    var hoursSpan = clock.querySelector('.hours');
    var minutesSpan = clock.querySelector('.minutes');
    var secondsSpan = clock.querySelector('.seconds');

    daysSpan.innerHTML = t.days;
    hoursSpan.innerHTML = t.hours;
    minutesSpan.innerHTML = t.minutes;
    secondsSpan.innerHTML = t.seconds;

    if (t.total <= 0) {
      clearInterval(timeinterval);
    }
  };

  updateClock(); // run function once at first to avoid delay

  var timeinterval = setInterval(updateClock, 1000);
}

$(document).ready(function() {
  $(".button-collapse").sideNav();

  var deadline = '2017-02-10T18:00:00Z';
  initializeClock('clock', deadline);
});
