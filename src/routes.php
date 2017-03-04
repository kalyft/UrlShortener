<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/[{shorten}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

	$data = $request->getParsedBody();
    $lurl = $data['lurl'];
    $provider = $data['provider'];
	
	if ($lurl =="") {
		echo "Invalid Url!";
		return $this->renderer->render($response, 'index.phtml', $args);
	}
	
	if($provider =='bitgiabit') {
		$bitly = new BitlyShortener('6625eeae6bc1222a0c826554b9a3113c23f8e368');
		$shortUrl = $bitly->shorten($lurl);
		if ($shortUrl) {
			echo 'Short URL: '.$shortUrl->url.'<br />';
			echo 'Long URL: '.$shortUrl->long_url.'<br />';
		} else {
			echo "Something went wrong.";
		}
	} else {
		$googl = new GooglShortener('AIzaSyBJbT5cZAt9kogk0IN6crBHR8502NteQcI');
		$url = $googl->shorten($lurl);
		echo 'Short URL: '.$url->id.'<br />';
		echo 'Long URL: '.$url->longUrl.'<br />';
	}
	// shorten a single URL
 
	
    return $this->renderer->render($response, 'index.phtml', $args);
});
