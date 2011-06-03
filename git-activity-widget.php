<!--
Copyright (c) 2011 Joseph Quigley

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

-->

<?php
//Requires SimpliePie feed parser: http://simplepie.org
//Tested on SimplePie 1.3

/* Actually any feed parser will work, with a few lines of modification ;) */
include_once('lib/SimplePieAutoloader.php');
include_once('lib/idn/idna_convert.class.php');

$cacheDir = "./cache";
$GIT_USERNAME = "YOUR_GIT_USERNAME";
$MAX_FEED_ITEMS = 15;
$MAX_DESC_LENGTH = 47; //Add +2 for middot and space when styling

// Single feed
$feed = new SimplePie();
$feed->set_feed_url('https://github.com/' . $GIT_USERNAME . '.atom');
$feed->handle_content_type();
$feed->enable_order_by_date(false);
$feed->enable_cache(true);
$feed->set_cache_location($cacheDir);
$feed->init();

$numItems = $feed->get_item_quantity();
if ($numItems > $MAX_FEED_ITEMS) {
	$numItems = $MAX_FEED_ITEMS;
}


?>
<div id="git_activity">    
    <div class="git_activity-jcarousellite">
		<ul>
			
		<?php
foreach ($feed->get_items(0, $numItems) as $item) {
		$feedItem = array();
		$dom = new DOMDocument();
		$dom->loadHTML($item->get_description() );
		$items = $dom->getElementsByTagName('blockquote');
		$size = $items->length;
		
		$descriptions = "";
		//if ($size > 0) {
		//	$descriptions = $items->item(0)->nodeValue;
		//	if (sizeof($descriptions) > 100) {
		//		$descriptions = substr($descriptions, 0, 100) . "...";
		//	}
		//}
		for ($i=0; $i<$size; $i++) {
			$description = $items->item($i)->nodeValue;
			if (sizeof($description) > 100) {
				$description = substr($description, 0, $MAX_DESC_LENGTH) . "...";
			}
			$descriptions .= "&middot;&nbsp;" . $description . "<br/>";
		}
		
		//var_dump(urlencode($item->get_description()));
		////Now get div class="message" values
		//$xpath = new DOMXPath($dom);
		//$tags = $xpath->query('div/div');
		//var_dump($tags);
		//foreach ($tags as $tag) {
		//    var_dump(trim($tag->nodeValue));
		//}

		$feedItem["url"] = $item->get_permalink();
		//Strip username plus following space from beginning of string, capitalize first letter
		$feedItem["title"] = ucfirst(ltrim(ltrim($item->get_title(), $GIT_USERNAME) ) );
		$feedItem["description"] = $descriptions;
		
		
		echo "<li><div class='info'><div><a href='" . $feedItem["url"] . "'>" . $feedItem["title"] . "</a></div>";
		echo "<div><span>" . $feedItem["description"] . "</span></div>";
		echo "</li>";
		echo '<div class="clearer"></div>';
}
?>
        </ul>
    </div>
    
</div>
