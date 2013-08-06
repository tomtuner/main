<?php

class RssController extends Controller 
{
	public function index() 
	{
		global $db;
		
		/*
		* 10 evr events based on date
		*/
		$eventsIds = $db->getCol("select event_id from dates as d, on_campus_events as oce, events as e where event_end_time > ".time()." and d.on_campus_event_id = oce.id AND e.id = oce.event_id LIMIT 20");
		
		$events = Event::getList($eventsIds);
				
		//Establish Header info
		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" ?>
			<rss version=\"2.0\">";
			
		//Define our channel information
		echo "<channel>
				<title>Center For Campus Life!</title>
					<description>
						This is the Center for Campuslife FEED!
					</description>
			    <link>http://campuslife.rit.edu/main/</link>";

			    /*
				* Here, we build our items!!!
				*/
				foreach($events as $event)
				{
					if($event->isEventsCalendar())
					{
						$title = $event->getName();
						$title = str_replace('&',"&amp;",$title);
						
						$descr = $event->getDescription();
						$descr = str_replace('&',"&amp;",$descr);
						
						echo "<item>";
						    echo "<title>";
								echo $title;
							echo "</title>";
						    echo "<description>";
								echo $descr;
							echo "</description>";
						echo "</item>";
					}
				
				}
				
			//end channel and rss    
			echo "</channel>";
		echo "</rss>";

		
	}
	
	
}

?>
