# YouTube Live

Provides an embed code to show your current YouTube Live broadcast on any other site.

Basically this is a simpler, hosted version of my [WordPress plugin](https://wordpress.org/plugins/wp-youtube-live/).

## Setup

0. Log into https://console.cloud.google.com/
1. Create a new project
2. Enable the [YouTube Data API](https://console.cloud.google.com/apis/library/youtube.googleapis.com) product
3. Create an [API key](https://console.cloud.google.com/apis/credentials)

## Usage

Add this markup to your page:

```html
<iframe src="https://youtube-live.andrewrminion.com/live/{channelId}" id="youtube-live"></iframe>

<script>
	let iframe = document.querySelector('#youtube-live');

	window.addEventListener('message', function(e) {
		// message that was passed from iframe page
		let message = e.data;

		iframe.style.height = message.height + 'px';
		iframe.style.width = message.width + 'px';
	} , false);
</script>
```
