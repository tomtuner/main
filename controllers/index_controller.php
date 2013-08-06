<?php
#comment Check perms of /var/www/data-dist/main/$cachefile also check /var/www/data-dist/controllers/index_controller.php 
# also sometimes if a request comes in which refreshes the cache, untill that operation completes, each will refresh the cache
class IndexController extends Controller {
	public function loadeventold(){
		include_once "XML/RSS.php";
		$rss = new XML_RSS("http://events.rit.edu/web_service/rss_feed.cfm?categories=4%2C8%2C1%2C14%2C9%2C13");
		$rss->parse();								
		echo "<ul>";
		if(!($rss->getItems()))
			echo '<class="aCSS"><b>Have a great break!!</b></class>';
		foreach ($rss->getItems() as $item) {
			echo "<li><b><a class = 'aCSS' href=\"" . $item['link'] . "\">" . $item['title'] . "</a></b></li>";
			echo "<div class = 'RssDate'>" . substr_replace($item['pubdate'] ,"",-13) . "</div>";
			}
		echo "</ul>";
	}
	
	public function loadevent(){
        $cachefile = "/home/cclweb/docs/default/main/cache/events.html";
		//$cachefile = "http://campuslife.rit.edu/main/cache/events.html";
		$startTime = time();
		// IMPORTANT
		// is_readable call not working with relative OR absolute link....
		if (is_readable($cachefile) || filemtime($cachefile) < ($startTime-(3*60*60))){ // if the cache does not exist or is more than 3 hours old
			ob_start();//start output buffering
			// the old code for loadevent
			include_once "XML/RSS.php";
			$rss = new XML_RSS("http://events.rit.edu/web_service/rss_feed.cfm?categories=4%2C8%2C1%2C14%2C9%2C13");
			$rss->parse();								
			echo "<ul>";
			// if(!($rss->getItems())) echo '<class="aCSS"><b>Have a great break!!</b></class><!-- this assumes that there are no events because we are on break-->';
				foreach ($rss->getItems() as $item) {
					echo "<li><b><a class = 'aCSS' href=\"" . $item['link'] . "\">" . $item['title'] ."</a></b></li>";
					echo "<div class = 'RssDate'>" . substr_replace($item['pubdate'] ,"",-13) . "</div>";
				}
			echo "</ul>";

			// end old code for load event
			$fp = fopen($cachefile, 'w'); // open the cache file for writing
			fwrite($fp, ob_get_contents()); // save the contents of output buffer to the file
			fclose($fp); // close the file
			//echo "\n<!-- Not served from cache, If you get this many times in a row, check the comment in controllers/index_controller.php-->";
			error_log("[Informational] The events cache was refeshed in:default/main/controllers/index_controller.php startTime=".date("Y-M-d H:i:s",$startTime)); 
			ob_end_flush(); // Send the output to the browser 
			
		} else{
			header("Cache-Control: max-age=600");
			flush();
			//echo "Reading from cache<br>";
			readfile($cachefile);
		}
	}
	
	public function loadevents() {
		global $db;
		$MAX = 7;
		
		// Implemented to dynamically get events from the promo database and display them on the website
		// This is only necessary in the index controller because we need to use ajax to acquire the eventkey from the events.rit.edu feed
		
		$rssFeed = "http://events.rit.edu/web_service/rss_feed.cfm?categories=1,2,3,4,5,6,8,9,11,12,13,14,15,17,18";
		$xml = new Xml();
		$xml->setFile($rssFeed);
		$xml->parse();
	
		extract(getdate());
		$now = "{$year}-{$mon}-{$mday} {$hours}:{$minutes}:{$seconds}";
		
		// Get promos from database
		$sql = "SELECT * FROM promo.events INNER JOIN (promo.days INNER JOIN promo.locations ON promo.days.id = promo.locations.day_id) ON promo.days.event_id = promo.events.id WHERE promo.days.start_at > '{$now}' ORDER BY promo.days.start_at ASC LIMIT 0,15";

		$res = $db->query($sql);
		$counter = 0;
		$used = array();
		$doPrint = false;
		
		while ($row =& $res->fetchRow()) {
			$formattedDate = date("n.j.y",strtotime($row['start_at']));
			$eventsDate = date("m-j-Y",strtotime($row['start_at']));
			
			$eventKeyLink = $xml->getEventKeyLink($row['name']);
						
			// If the key exists, link to the actual event; else, link to the day of the event
			$eventLink = (isset($eventKeyLink))?$eventKeyLink:"https://events.rit.edu/index.cfm?action=date&name=day&value={$eventsDate}";
			
			if ($counter < $MAX) {
				if (isset($eventKeyLink)) {
					// So the event key is set meaning we are linking to the actual event and we need to check the event key so we don't print it twice
					$searchSalt = "event_key=([0-9]+)";
					preg_match("#{$searchSalt}#",$eventKeyLink,$matches);
					
					$needle = strtoupper($matches[1]);
				} else {
					// No event key meaning we are linking to a day so we need to check to event name
					$needle = strtoupper($row['name']);
				}
				
				if (!in_array($needle,$used)) {
					// The needle used isn't in the array 
					if (!isset($eventKeyLink)) {
						// Lets check if there is something 'like' it in the array because of misspellings and what not..this is not perfect but useful
						foreach ($used as $item) if ($this->compareLike($item,$needle)) break;
					}
					
					// store in the array
					$used[] = strtoupper($needle);
					// increment counter so we only get 7
					$counter++;
					$doPrint = true;
				} else {
					$doPrint = false;
				}
				
				if ($doPrint) echo "<strong>{$formattedDate}</strong>- <a href=\"{$eventLink}\">{$row['name']}</a>\n";
			}
		}
	}
	
	public function images(){
	#phpinfo();
	}
	
	private function compareLike($str,$str2) {
		// If either is a number, dont do anything because this is a string comparison for event names
		if (is_numeric($str)||is_numeric($str2)) return;
		
		$pros = 0;
		// length will be the longest of the two strings
		$length = (strlen($str)>strlen($str2))?strlen($str):strlen($str2);
		// this is the percentage of precision we want..0.7 would be 70%
		$acceptableCorrect = floor(0.7*$length);
		
		// If they aren't the same thing, lets see if they are at least 'close' in length..if not, do nothing
		if (strlen($str)!=strlen($str2)) if (abs(strlen($str)-strlen($str2)) > $acceptableCorrect) return;
	
		for ($i=0;$i<$length;$i++) {
			// Three separate comparisons in the loop to check for the same position, position+1 for first string, and position+1 for second string
			if (isset($str[$i])&&isset($str2[$i])) {
				if ($str[$i]==$str2[$i]) $pros++;
			}
			
			if (isset($str[$i+1])&&isset($str2[$i])) {
				if ($str[$i+1]==$str2[$i]) $pros++;
			}
			
			if (isset($str[$i])&&isset($str2[$i+1])) {
				if ($str[$i]==$str2[$i+1]) $pros++;
			}
		}
		
		if ($pros > $acceptableCorrect) return true;
		
		return false;
	}
	
}

?>
