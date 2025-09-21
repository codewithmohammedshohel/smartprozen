<?php
echo "<h1>Simple Test - SmartProZen Project</h1>";
echo "<p>If you can see this, the project is working correctly.</p>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] ?? 'Not set' . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['HTTP_HOST'] ?? 'Not set' . "</p>";
echo "<p><a href='index.php'>Go to Homepage</a></p>";
?>
