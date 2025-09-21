<?php
/**
 * Fix XAMPP redirect issue
 * This script helps identify and fix the XAMPP dashboard redirect problem
 */

echo "<h2>XAMPP Redirect Fix</h2>";

echo "<h3>The Problem:</h3>";
echo "<p>XAMPP is redirecting requests to <code>http://localhost/dashboard/</code> instead of staying within the SmartProZen project.</p>";

echo "<h3>Solutions:</h3>";

echo "<h4>Solution 1: Access the project directly</h4>";
echo "<p>Instead of going to <code>http://localhost/</code>, go directly to:</p>";
echo "<p><strong><a href='http://localhost/smartprozen/'>http://localhost/smartprozen/</a></strong></p>";

echo "<h4>Solution 2: Create a proper index.php in XAMPP root</h4>";
echo "<p>If you want to access the project from <code>http://localhost/</code>, you need to:</p>";
echo "<ol>";
echo "<li>Go to your XAMPP installation directory</li>";
echo "<li>Navigate to <code>htdocs/</code> folder</li>";
echo "<li>Create or modify <code>index.php</code> to redirect to SmartProZen</li>";
echo "</ol>";

echo "<h4>Solution 3: Modify XAMPP's default index</h4>";
echo "<p>Replace XAMPP's default index with a redirect to SmartProZen:</p>";
echo "<pre>";
echo htmlspecialchars('<?php
// Redirect to SmartProZen project
header("Location: /smartprozen/");
exit;
?>');
echo "</pre>";

echo "<h3>Current Project Status:</h3>";
echo "<p><a href='http://localhost/smartprozen/' class='btn'>✅ SmartProZen Homepage</a></p>";
echo "<p><a href='http://localhost/smartprozen/admin/login.php' class='btn'>✅ Admin Panel</a></p>";
echo "<p><a href='http://localhost/smartprozen/test_links.php' class='btn'>✅ Test Links</a></p>";

echo "<h3>Quick Test:</h3>";
echo "<p>Try these URLs:</p>";
echo "<ul>";
echo "<li><a href='http://localhost/smartprozen/'>http://localhost/smartprozen/</a> - Should work</li>";
echo "<li><a href='http://localhost/smartprozen/index.php'>http://localhost/smartprozen/index.php</a> - Should work</li>";
echo "<li><a href='http://localhost/smartprozen/simple_test.php'>http://localhost/smartprozen/simple_test.php</a> - Simple test</li>";
echo "</ul>";

echo "<h3>XAMPP Configuration Check:</h3>";
echo "<p>If you want to fix XAMPP's default behavior:</p>";
echo "<ol>";
echo "<li>Stop XAMPP</li>";
echo "<li>Go to <code>C:\\xampp\\apache\\conf\\extra\\httpd-vhosts.conf</code></li>";
echo "<li>Add a virtual host for your project</li>";
echo "<li>Restart XAMPP</li>";
echo "</ol>";

echo "<h3 style='color: green;'>✅ Recommended Solution:</h3>";
echo "<p><strong>Always use:</strong> <code>http://localhost/smartprozen/</code></p>";
echo "<p>This ensures you're accessing the SmartProZen project directly without XAMPP interference.</p>";
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3, h4 { color: #333; }
p { margin: 10px 0; }
ul, ol { margin: 10px 0; padding-left: 20px; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
.btn { display: inline-block; padding: 8px 16px; background-color: #007bff; color: white; border-radius: 4px; text-decoration: none; margin: 5px; }
.btn:hover { background-color: #0056b3; color: white; text-decoration: none; }
pre { background-color: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
code { background-color: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
</style>
