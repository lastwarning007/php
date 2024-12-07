<?php
// Initialize variables
$result = '';
$error = '';
$photo_url = '';
$video_url = '';
$like_photo_url = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the URL from the form
    $url = trim($_POST['url']);
    
    if (empty($url)) {
        $error = 'Please enter a valid Instagram URL.';
    } else {
        // Construct the API URL
        $api_url = "https://last-warning.serv00.net/Instagram_downloder.php?url=" . urlencode($url);
        
        // Initialize cURL session
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // To return the response as a string
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for this case (optional)
        
        // Execute cURL and get the response
        $response = curl_exec($ch);
        
        // Check for errors in cURL execution
        if (curl_errno($ch)) {
            $error = "Error: " . curl_error($ch);
        } else {
            // Decode the JSON response
            $decoded_response = json_decode($response, true);
            if (isset($decoded_response['urls']) && is_array($decoded_response['urls'])) {
                $result = $decoded_response['urls'];
            } else {
                $error = 'No URLs found in the API response.';
            }

            // Check if photo, video, and like photo URLs are available
            if (isset($decoded_response['photo_url'])) {
                $photo_url = $decoded_response['photo_url'];
            }

            if (isset($decoded_response['video_url'])) {
                $video_url = $decoded_response['video_url'];
            }

            if (isset($decoded_response['like_photo_url'])) {
                $like_photo_url = $decoded_response['like_photo_url'];
            }
        }
        
        // Close cURL session
        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram Downloader</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .top-bar {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
            font-size: 18px;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .result {
            margin-top: 20px;
        }
        .error {
            color: red;
            text-align: center;
        }
        .urls-list {
            list-style-type: none;
            padding: 0;
        }
        .urls-list li {
            margin: 10px 0;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>



<!-- Top Bar -->
<div class="top-bar">
    LAST WARNING BADLIAR INSTAGRAM DOWNLOADER
</div>

<div class="container">
    <h1>Instagram Downloader</h1>
    <form method="POST" action="">
        <input type="text" name="url" placeholder="Enter Instagram post URL" value="<?php echo isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''; ?>" />
        <button type="submit">Download</button>
    </form>

    <!-- Display error message if there is one -->
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Display the result if there are URLs -->
    <?php if ($result): ?>
        <div class="result">
            <h2>Download Links:</h2>
            <ul class="urls-list">
                <?php 
                foreach ($result as $url) {
                    // Clean up the URL to remove backslashes
                    $clean_url = stripslashes($url);
                    echo "<li><a href=\"$clean_url\" target=\"_blank\">$clean_url</a></li>";
                }
                ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Display photo, video, and like photo download links if available -->
    <?php if ($photo_url || $video_url || $like_photo_url): ?>
        <div class="result">
            <h2>Download Links:</h2>
            <?php if ($photo_url): ?>
                <p>Photo: <a href="<?php echo htmlspecialchars($photo_url); ?>" target="_blank">Download Photo</a></p>
            <?php endif; ?>
            <?php if ($video_url): ?>
                <p>Video: <a href="<?php echo htmlspecialchars($video_url); ?>" target="_blank">Download Video</a></p>
            <?php endif; ?>
            <?php if ($like_photo_url): ?>
                <p>Like Photo: <a href="<?php echo htmlspecialchars($like_photo_url); ?>" target="_blank">Download Like Photo</a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>