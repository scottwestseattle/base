@php
$historyPath = isset($history['historyPath']) ? $history['historyPath'] : 'Path Not Set';
$programName = isset($history['programName']) ? $history['programName'] : null;
$programId = isset($history['programId']) ? $history['programId'] : 0;
$programType = isset($history['programType']) ? $history['programType'] : 0;
$programSubType = isset($history['programSubType']) ? $history['programSubType'] : 0;
$programAction = isset($history['programAction']) ? $history['programAction'] : 0;
$sessionName = isset($history['sessionName']) ? $history['sessionName'] : null;
$sessionId = isset($history['sessionId']) ? $history['sessionId'] : null;
$count = isset($history['count']) ? $history['count'] : 0;
$score = isset($history['score']) ? $history['score'] : 0;
$seconds = isset($history['seconds']) ? $history['seconds'] : 0;
$extra = isset($history['extra']) ? $history['extra'] : 0;
$route = isset($history['route']) ? $history['route'] : null;
$source = isset($history['source']) ? $history['source'] : HISTORY_SOURCE_NOTUSED;
@endphp
data-historypath="{{$historyPath}}"
data-programname="{{$programName}}"
data-programid="{{$programId}}"
data-programtype="{{$programType}}"
data-programsubtype="{{$programSubType}}"
data-programaction="{{$programAction}}"
data-sessionname="{{$sessionName}}"
data-sessionid="{{$sessionId}}"
data-historycount="{{$count}}"
data-historyscore="{{$score}}"
data-historyseconds="{{$seconds}}"
data-historyextra="{{$extra}}"
data-historyroute="{{$route}}"
data-historysource="{{$source}}"

