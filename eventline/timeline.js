/*==================================================
 *  Create timeline functions
 *==================================================
 */
var tl;

function onLoad() {
    
    var theme = Timeline.ClassicTheme.create(); 
    var filepath;
    
    filepath = arguments[0];
    bubbleH = arguments[1];
    bubbleW = arguments[2];
    mouse = arguments[3];
    center = arguments[4];
    controls = arguments[5];
    bandPos = arguments[6];
    detailPart = arguments[7];
    overPart = arguments[8];
    detailPixels = arguments[9];
    overPixels = arguments[10];
    detailInterval = arguments[11];
    overInterval   = arguments[12];
  
   theme.mousewheel = mouse; 
   theme.event.label.width = 500; 
   theme.event.bubble.width = bubbleW; 
   theme.event.bubble.maxHeight = bubbleH; 

  var eventSource = new Timeline.DefaultEventSource(); 
  
  if (bandPos == 'default')
    var bandInfos = [
      Timeline.createBandInfo({
        eventSource:    eventSource,
        date:           "Mar 8 999 00:00:00 GMT",
        width:          detailPart, 
        intervalUnit:   convertTime(detailInterval), 
        intervalPixels: detailPixels,
        theme: theme
      }),
      Timeline.createBandInfo({
        overview:       true,
        showEventText:  false,
        trackHeight:    0.5,
        trackGap:       0.2,
        eventSource:    eventSource,
        date:           "Mar 8 999 00:00:00 GMT",
        width:          overPart, 
        intervalUnit:   convertTime(overInterval), 
        intervalPixels: overPixels
      })
    ];
  else
    var bandInfos = [
      Timeline.createBandInfo({
        overview:       true,
        showEventText:  false,
        trackHeight:    0.5,
        trackGap:       0.2,
        eventSource:    eventSource,
        date:           "Mar 8 999 00:00:00 GMT",
        width:          "20%", 
        intervalUnit:   convertTime(overInterval), 
        intervalPixels: overPixels
      }),
      Timeline.createBandInfo({
        eventSource:    eventSource,
        date:           "Mar 8 999 00:00:00 GMT",
        width:          "80%", 
        intervalUnit:   convertTime(detailInterval), 
        intervalPixels: detailPixels,
        theme: theme
      })
    ];
  
  bandInfos[1].syncWith = 0;
  bandInfos[1].highlight = true;
  bandInfos[1].eventPainter.Layout=bandInfos[0].eventPainter.Layout;

  tl = Timeline.create(document.getElementById("eventlineplugin__timeline"), bandInfos, Timeline.HORIZONTAL);
  Timeline.loadXML(filepath, function(xml, url) { eventSource.loadXML(xml, url); }); 
  if (controls=='on') setupFilterHighlightControls(document.getElementById("eventlineplugin__controls"), tl, [0,1], theme); 
  javascript:centerTimeline(center);
}

function centerTimeline(dateStr) {
    tl.getBand(0).setCenterVisibleDate(new Date(dateStr));
}
        
var resizeTimerID = null;
function onResize() {
    if (resizeTimerID == null) {
        resizeTimerID = window.setTimeout(function() {
            resizeTimerID = null;
            tl.layout();
        }, 500);
    }
}

function convertTime (timeframe) {
    var result;
      switch (timeframe) {
          case 'MILLISECOND':
            result=Timeline.DateTime.MILLISECOND;
            break;
          case 'SECOND':
            result=Timeline.DateTime.SECOND;
            break;
          case 'MINUTE':
            result=Timeline.DateTime.MINUTE;
            break;
          case 'HOUR':
            result=Timeline.DateTime.HOUR;
            break;
          case 'DAY':
            result=Timeline.DateTime.DAY;
            break;
          case 'WEEK':
            result=Timeline.DateTime.WEEK;
            break;
          case 'MONTH':
            result=Timeline.DateTime.MONTH;
            break;
          case 'YEAR':
            result=Timeline.DateTime.YEAR;
            break;
          case 'DECADE':
            result=Timeline.DateTime.DECADE;
            break;
          case 'CENTURY':
            result=Timeline.DateTime.CENTURY;
            break;
          case 'MILLENNIUM':
            result=Timeline.DateTime.MILLENNIUM;
            break;
          case 'EPOCH':
            result=Timeline.DateTime.EPOCH;
            break;
          case 'ERA':
            result=Timeline.DateTime.ERA;
            break;
          default:
            result=Timeline.DateTime.YEAR;
       }
       return result;
}
