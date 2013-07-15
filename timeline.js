/*==================================================
 *  Create timeline functions
 *==================================================
 */
var tl;

function plugin_eventline() {
    
    var theme = Timeline.ClassicTheme.create(); 
    var filepath;
    
    filepath = plugin_eventline_filePath;
    bubbleH = plugin_eventline_bubbleHeight;
    bubbleW = plugin_eventline_bubbleWidth;
    mouse = plugin_eventline_mouse;
    center = plugin_eventline_center;
    controls = plugin_eventline_controls;
    bandPos = plugin_eventline_bandPos;
    detailPart = plugin_eventline_detailPercent;
    overPart = plugin_eventline_overPercent;
    detailPixels = plugin_eventline_detailPixels;
    overPixels = plugin_eventline_overPixels;
    detailInterval = plugin_eventline_detailInterval;
    overInterval   = plugin_eventline_overInterval;

    hotzone   = plugin_eventline_hotzone;
    hzStart   = plugin_eventline_hzStart;
    hzEnd     = plugin_eventline_hzEnd;
    hzMagnify = plugin_eventline_hzMagnify;
    hzUnit    = plugin_eventline_hzUnit;

    hzStart2   = plugin_eventline_hzStart2;
    hzEnd2     = plugin_eventline_hzEnd2;
    hzMagnify2 = plugin_eventline_hzMagnify2;
    hzUnit2    = plugin_eventline_hzUnit2;
    
    hzStart3   = plugin_eventline_hzStart3;
    hzEnd3     = plugin_eventline_hzEnd3;
    hzMagnify3 = plugin_eventline_hzMagnify3;
    hzUnit3    = plugin_eventline_hzUnit3;
  
   theme.mousewheel = mouse; 
   theme.event.label.width = 500; 
   theme.event.bubble.width = bubbleW; 
   theme.event.bubble.maxHeight = bubbleH; 

  var eventSource = new Timeline.DefaultEventSource(); 
  
  if (hotzone == 1)
    var bandInfos = [
     Timeline.createHotZoneBandInfo({
        zones: [
         { start:   hzStart,
           end:     hzEnd,
           magnify: hzMagnify,
           unit:    convertTime(hzUnit)
         },
         { start:   hzStart2,
           end:     hzEnd2,
           magnify: hzMagnify2,
           unit:    convertTime(hzUnit2)
         },
         { start:   hzStart3,
           end:     hzEnd3,
           magnify: hzMagnify3,
           unit:    convertTime(hzUnit3)
         }
        ],
        eventSource:    eventSource,
        date:           center,
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
        date:           center,
        width:          overPart, 
        intervalUnit:   convertTime(overInterval), 
        intervalPixels: overPixels
      })
    ];
  else
  
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
        width:          overPart, 
        intervalUnit:   convertTime(overInterval), 
        intervalPixels: overPixels
      }),
      Timeline.createBandInfo({
        eventSource:    eventSource,
        date:           "Mar 8 999 00:00:00 GMT",
        width:          detailPart, 
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
  if (controls==1) setupFilterHighlightControls(document.getElementById("eventlineplugin__controls"), tl, [0,1], theme); 
  javascript:centerTimeline(center);
}

function centerTimeline(dateStr) {
    tl.getBand(0).setCenterVisibleDate(new Date(dateStr));
}
        
var resizeTimerID = null;
function plugin_eventline_onResize() {
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
