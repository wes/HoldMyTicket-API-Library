<?php

class hmt {

	# just configure the api key to use
	# you can aquire an API key in the holdmyticket control panel under account settings
	var $apikey = '';

	function getEvents($opts=array()){
		
		$venues = '';
		if(isset($opts['venues'])):
			$venues = $opts['venues'];
		endif;

		$url = "http://holdmyticket.com/api/events/getList/api_key/".$this->apikey;

		if(!empty($venues)):
			$url .= "/venues/".$venues;
		endif;

		$xml_response = $this->stream_remote_file($url);
		
		$res = new SimpleXMLElement($xml_response);

		return $res->event;

	}
	
	function getEvent($event_id){

		$url = "http://holdmyticket.com/api/events/getInfo/api_key/".$this->apikey."/event/".$event_id;
		$x = $this->simplexml_load_file($url);
		return $x['event'];
		
	}
	
	function getEventHTML($event_id){
		$url = "http://holdmyticket.com/api/events/getHTML/api_key/".$this->apikey."/event/".$event_id;
		return $this->stream_remote_file($url);
	}

	function stream_remote_file($url){
		$handle = fopen($url, "rb");
		return stream_get_contents($handle);
	}

	function formatDateRange($start,$end,$hideTime=false){

		list($s['date'],$s['time']) = explode(" ",$start);
		list($s['year'],$s['mon'],$s['day']) = explode("-",$s['date']);
		list($s['hour'],$s['min'],$s['sec']) = explode(":",$s['time']);

		list($e['date'],$e['time']) = explode(" ",$end);
		list($e['year'],$e['mon'],$e['day']) = explode("-",$e['date']);
		list($e['hour'],$e['min'],$e['sec']) = explode(":",$e['time']);

		$startTime = mktime($s['hour'],$s['min'],$s['sec'],$s['mon'],$s['day'],$s['year']);
		$endTime = mktime($e['hour'],$e['min'],$e['sec'],$e['mon'],$e['day'],$e['year']);

		if($end == '0000-00-00 00:00:00'){
			# just return the start date
			if($hideTime == true){
				return date("F j, Y",$startTime);
			}else{
				return date("F j, Y, g:i A",$startTime);
			}
		}elseif($s['day'] == $e['day'] && $s['mon'] == $e['mon'] && $s['year'] == $e['year']){
			# okay this is the same start and end date.. so just give them the day and start and end times..
			if($hideTime == true){
				return date("F j, Y",$startTime);
			}else{
				return date("F j, Y g:i A",$startTime)." - ".date("g:i a",$endTime);		
			}
		}elseif($s['day'] != $e['day'] && $s['mon'] == $e['mon']){
			# different day in same month.. so show something like August 23rd - 29th 2008
			return date("F j",$startTime)." - ".date("j, Y",$endTime);		
		}elseif($s['mon'] != $e['mon']){
			# different day and month so show, August 23rd - September 5th 2008
			return date("F j",$startTime)." - ".date("F j, Y",$endTime);		
		}

	}


}

?>
