$(function () {
     // Timer for reminder overtime
    function myTimer() {

        var timeNow = moment($('#timer').val(), "HH:mm").valueOf();  //milliseconds
        var first_time = moment().format('ll')+' '+($('#firstTime').val());
        var second_time = moment().format('ll')+' '+($('#secondTime').val());
        var fiveMinutesBeforeFirstTime = moment(first_time).subtract(5, 'minutes').format('HH:mm');
        var fiveteenMinutesBeforeFirstTime = moment(first_time).subtract(15, 'minutes').format('HH:mm');
        var fiveMinutesBeforeSecondTime = moment(second_time).subtract(5, 'minutes').format('HH:mm');
        var fiveteenMinutesBeforeSecondTime = moment(second_time).subtract(15, 'minutes').format('HH:mm');

        var fiveMinFirstTime = moment(fiveMinutesBeforeFirstTime, "HH:mm").valueOf(); //milliseconds
        var fiveteenMinFirstTime = moment(fiveteenMinutesBeforeFirstTime, "HH:mm").valueOf(); //milliseconds
        var fiveMinSecondtTime = moment(fiveMinutesBeforeSecondTime, "HH:mm").valueOf(); //milliseconds
        var fiveteenMinSecondtTime = moment(fiveteenMinutesBeforeSecondTime, "HH:mm").valueOf(); //milliseconds
 
        if (timeNow == fiveMinFirstTime) {
            alert('Overtime Status "NORMAL" expiring in 5 minutes');
        }else if( timeNow == fiveteenMinFirstTime ){
            alert('Overtime Status "NORMAL" expiring in 15 minutes');
        }else if( timeNow == fiveMinSecondtTime ){
            alert('Overtime Status "OVERTIME 1" expiring in 5 minutes');
        }else if( timeNow == fiveteenMinSecondtTime ){
            alert('Overtime Status "OVERTIME 1" expiring in 15 minutes');
        }
    }

    timer = setInterval(function(){myTimer()},25000);
});
