<?php
function disable_acf_load_field( $field ) {
	$field['disabled'] = 1;
	return $field;
}
add_filter('acf/load_field/name=facebook_tuongtac', 'disable_acf_load_field');
add_filter('acf/load_field/name=view_ga', 'disable_acf_load_field');

function add_query_vars_filter( $vars ) {
  $vars[] = "idpost";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );


// queue get tuong tac facebook
if ( function_exists( 'wp_async_task_add' ) ){

    function queue_facebook_tuongtac_callback($args){
    	$i = 0;
    	$j = 0;
    	$k = 1;
    	$a = 0;
    	$id = $args['idpost'];
    	
    	$ids_graph = '';


		if(empty($id)) return;
		
		if($id == 179){
			$posts = get_posts(array(
			    'post_type' => array('post','video-clip','focus_news'),
			    'posts_per_page' => 6000 ,
			));
		} elseif ($id == 178) {
			$posts = get_posts(array(
			    'post_type' => array('post','video-clip','focus_news'),
			    'posts_per_page' => 100 ,
			));
		} else{

			$posts = get_posts(array(
			    'post_type' => array('post','video-clip','focus_news'),
			    'post__in' => array($id) ,
			));
			$a = 1;
		}

	    $graph_link1 = "https://graph.facebook.com/v2.11/?ids=";
		$graph_link2 = "&fields=engagement&access_token=";

		$access_token1 = "EAAcUw8IVMNwBAPrrsiMmm2oRzr3QAd5ETUEQasanqSpdHzvwBt4pMcDB8nTCW24k1B20ZBicDmEyvU0paXkkhhZC7AWKS1Vf4asozaBaOIwP9j6qwZAB9fBhBiIciOw3TNe6jeNGiTvfEYazDOQfibJ2ybacg7yvGZAaps3bXQZDZD";
		$access_token2 = "EAAcUw8IVMNwBABf7uuA1x5EWDACATpzkQ23HiNz96wyIldrfQLYT5TZBQBzTRhmo2bt21Vnwtt42JDwZAuOSdn8oPZB7VWR9U20N9jqXCuZCUVDDUnpBoHAc9Pa39lhTSHXpf2eDzvOLXKVvlYxufb4L8MgtTlHdsOLhYhHvZAAZDZD";
		$access_token3 = "EAAcUw8IVMNwBAGZAOu9J8fMyiC8R9k1K2ZCMYM0aDXbN4f3z2xzZAac2l4hzKrZComD9OVjgOZAwUnZBTt2oyPUqOOFyubBLSE6qwUsLU23wNoJTyxw2qCtuI1eVSWnp5Gu3yjtAQtyasc0pmG3ikyCn5FR5AKIfxWN8g8fZCCiAAZDZD";
		$access_token4 = "EAAcUw8IVMNwBAJ7eCMA14svBZA0ctz33rQqiHlkJVK0Nj8ADZAtdON0806wVoZByfgYa0TJINlRSlkwPsDQ5DZC3SGElTLbh4rQktAV4lwOnD7940yDzqgnbufwHb3Yliekb1iG6asBF6nEvR3Q8xpNDlLNAx1BRORBG5MpgtQZDZD";
		$access_token5 = "EAAcUw8IVMNwBAJxZAr44ZCTzNMoxGLdbsxBGgGWUt2eX56cfZBcSTMuwQ6BxMnP5r18rzZCI4zcWhFyFMNgCGoV3hZAa0me5zQ9wJZBKrcJX7eW0EsCPIvVPCZABJN6prZAnFcIYLdbYOdC8GArZAlOEmFobtDwZCoetbZAxvfhH66X8QZDZD";
		
		$arr_access_token = array($access_token1, $access_token2, $access_token3, $access_token4, $access_token5);

		if(!empty($posts)){
			foreach($posts as $post):

			    $id_post = $post->ID;
			    $link_post = get_permalink( $id_post );

			    if ($a == 1) {	

		    		$ids_graph = $link_post;

		    		$url_graph = $graph_link1.$ids_graph.$graph_link2.$arr_access_token[1];

		    		$url_graph = str_replace('cms.', '', $url_graph);
					$responses = json_decode(file_get_contents($url_graph));

					// xử lý responses gửi về 
					foreach ((array)$responses as $response) {
						$url_from_graph = $response->id;
						preg_match('/-([0-9]*).html/', $url_from_graph, $matches);
						$id_from_graph = $matches[1];

						$like = $response->engagement->reaction_count;
						$share = $response->engagement->share_count;
						$comment = $response->engagement->comment_count;
						$total = (int) $like + (int) $share + (int) $comment;
						update_field('facebook_tuongtac', $total, $id_from_graph);

						echo 'ID: '.$id_from_graph;
						echo PHP_EOL;
						echo 'Reaction FB: '.$total;
						echo PHP_EOL;
					}
		    		 
			    }

			    if ($i == 20) {
			    	$so_luong_bai = $k*20;
					echo 'SO LUONG BAI: '.$so_luong_bai;
					echo PHP_EOL;
					$k++;

			    	// mỗi lần request 50 links
			    	$i = 0;
			    	$ids_graph = $ids_graph.','.$link_post;
			    	$url_graph = $graph_link1.$ids_graph.$graph_link2.$arr_access_token[$j];

			    	// 50 posts sẽ đổi 1 access token
			    	if ($i % 20 == 0) $j++;	
			    	if ($j == 4) $j = 0;
					sleep(1);

					$url_graph = str_replace('cms.', '', $url_graph);
					$responses = json_decode(file_get_contents($url_graph));

					// xử lý responses gửi về 
					foreach ((array)$responses as $response) {
						$url_from_graph = $response->id;
						preg_match('/-([0-9]*).html/', $url_from_graph, $matches);
						$id_from_graph = $matches[1];

						$like = $response->engagement->reaction_count;
						$share = $response->engagement->share_count;
						$comment = $response->engagement->comment_count;
						$total = (int) $like + (int) $share + (int) $comment;
						update_field('facebook_tuongtac', $total, $id_from_graph);

						echo 'ID: '.$id_from_graph;
						echo PHP_EOL;
						echo 'Reaction FB: '.$total;
						echo PHP_EOL;
					}
			    	
			    } else {
			    	if ($i == 0) {
			    		$ids_graph = $link_post;
			    	} else {
			    		$ids_graph = $ids_graph.','.$link_post;
			    	}
			    	$i++;
			    }

			endforeach;
			wp_reset_postdata();
		}
	}	
    add_action( 'queue_facebook_tuongtac', 'queue_facebook_tuongtac_callback' );
}


// queue get view GA
if ( function_exists( 'wp_async_task_add' ) ){

	function queue_google_analytics_view_callback($args) {

		require 'gapi.class.php';
		$a = 0;

		$id = $args['idpost'];
		if(empty($id)) return;
		if($id == 179){
			$posts = get_posts(array(
			    'post_type' => array('post','video-clip','focus_news'),
			    'posts_per_page' => 10000 ,
			    
			    
			));
		} elseif ($id == 178) {
			$posts = get_posts(array(
			    'post_type' => array('post','video-clip','focus_news'),
			    'posts_per_page' => 200 ,
			    
			    
			));
		} else {
			$posts = get_posts(array(
			    'post_type' => 'post',
			    'post__in' => array( $id ) ,
			));
			$a = 1;
		}


		$i = 0;
		$j = 1;
		$flag = 0;
		$gaEmail = 'phuc-79@gg-ana.iam.gserviceaccount.com';
		$gaPassword = 'gg-ana-4b9d5c161c69.p12';
		$profileId = '94883462';

		$dimensions = array('pagePath'); 
		$metrics = array('pageviews');
		$sortMetric=null;
		$filter='';

		$ga = new gapi($gaEmail, $gaPassword);
		
		if(!empty($posts)){
			$so_luong_posts = sizeof($posts);
			foreach($posts as $post):

				$id_post = $post->ID;
				$link_post = get_permalink( $id_post );


				$old_view0 = get_field('view_ga', $id_post);
				if ($old_view0 == NULL) {
					update_field('view_ga', 0, $id_post);
				}


				if ($i == 99 || $a == 1) {
					$so_luong_bai = $j*100;
					echo 'so_luong_bai = '.$so_luong_bai;
					$j++;

					$i = 0;	
					if ($a == 1) {
						$filter = 'pagepath=~'.$id_post.'.html';
					} else {
						$filter = $filter.' || pagepath=~'.$id_post.'.html';
					}
					$ga->requestReportData($profileId, $dimensions, $metrics, $sortMetric, $filter);
					$result = $ga->getResults();

					//update_field('view_ga', 1, '1440019');	
					$arraytotal = [];
					foreach ($result as $value) {


						$metric = $value->getMetrics();
						$dimension = $value->getDimensions();

						$metric = (object) $metric;
						$dimension = (object) $dimension;

						$pageviews = $metric->pageviews;
						$pagepath = $dimension->pagepath;
						
						// lấy post id từ pagepath
						preg_match('/-([0-9]*).html/', $pagepath, $matches);
						$id_from_pagepath = (int) $matches[1];

						// update giá trị
						// $old_view = get_field('view_ga', $id_from_pagepath);
						// $pageviews = (int) $pageviews + (int) $old_view;

						if($arraytotal[$id_from_pagepath] == null)
						{
							$arraytotal[$id_from_pagepath] = $pageviews; 
						}
						else
						{
							$arraytotal[$id_from_pagepath] += $pageviews; 
						}

					}	


					foreach ($arraytotal as $key => $value) {
						update_field('view_ga', $value, $key);
						
						//update_field('facebook_tuongtac', $value, '1440019');
						echo 'ID: '.$key;
						echo PHP_EOL;
						
						echo 'VIEW: '.$value;
						echo PHP_EOL;
					}
						



					$arraytotal  = [];

					
				} else {
					if ($i == 0) {
						$filter = 'pagepath=~'.$id_post.'.html';
					} else {
						$filter = $filter.' || pagepath=~'.$id_post.'.html';
					}
					$i++;
				}

			endforeach;
			wp_reset_postdata();
		}

	}
	add_action( 'queue_view_ga', 'queue_google_analytics_view_callback' );
}