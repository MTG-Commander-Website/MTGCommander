'use strict';

document.addEventListener('DOMContentLoaded', function() {
	const rss_feeds = document.querySelectorAll('.wprss_ajax');

	rss_feeds.forEach(function(rss_feed) {
		const feed_settings = window[rss_feed.dataset.id];

		rss_retriever_fetch_feed(feed_settings)
		  	.then(data => {
		    	rss_feed.innerHTML = data;
		  	})
		  	.catch(error => {
		    	console.log(error);
		  	});
	});

	function rss_retriever_fetch_feed(feed_settings) {
	  return new Promise((resolve, reject) => {
	    jQuery.ajax({
			type: "post",
			dataType: "json",
			url: rss_retriever.ajax_url,
			data: {'action':'rss_retriever_ajax_request', 'settings' : feed_settings},
	      success: function(data) {
			  //Within here we can process the data.
	        resolve(processData(data));
	      },
	      error: function(error) {
	        reject(error);
	      },
	    })
	  })
	};

	function processData(data){
		let items = data.split('<div class="widget rss-item">');
		//Put header data into the final data.
		let finalData = items[0];
		//save footer data for later
		let footerData = items[items.length-1];

		items.forEach((item, index)=>{
				//Sort by Date?
				let itemTitle = item.split('<a class="wp_rss_retriever_title"')[1];
				itemTitle = itemTitle.split('>')[0];
				if(item.includes("commander") || item.includes("Commander") || item.includes("COMMANDER") || item.includes("EDH") || item.includes("dhrec")){	
					item = item.replace('&lt;sup&gt;&amp;reg;&lt;/sup&gt;', '');
					finalData += '<div class="widget rss-item">';
					finalData += item;
				}
				//Possibly put in an image if none exsists
		});
		finalData += footerData;
		return finalData;
	}
});
