<?php
/*
Plugin Name: Google URL Creator
Description: Generate custom URL strings and use shortcodes to dynamically insert keywords into your copy.
Version: 1.0
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

//Add the menu under Tools
add_action("admin_menu","gurlc_menu");

function gurlc_menu() {
	add_submenu_page("tools.php","URL Creator", "URL Creator", "manage_options","google-url-creator", "gurlc_body_fcn");
}

/*------------------
Create URL generating form
-------------------*/
function gurlc_body_fcn() { 
$bad = array(" ", "'", '"');
$good = array("%20", "", "%22");
//Unclean variables
$pre_source = str_replace($bad, $good, $_POST['source']);
$pre_medium = str_replace($bad, $good, $_POST['medium']);
$pre_term = str_replace($bad, $good, $_POST['term']);
$pre_content = str_replace($bad, $good, $_POST['content']);
$pre_name = str_replace($bad, $good, $_POST['name']);
//Clean variables
$site = get_site_url();
$source = stripslashes($pre_source);
$medium = stripslashes($pre_medium);
$term = stripslashes($pre_term);
$content = stripslashes($pre_content);
$name = stripslashes($pre_name);
	?>
<style>
code {
	font-size: 2em;
	line-height: 1.5em;
}
</style>
<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br>
	</div>
	<h2>Generate a custom campaign URL</h2>
	<form action="<?php the_permalink(); ?>" id="gurlc" method="post">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
		    			<label for="source">Campaign Source *</label>
		    		</th>
		    		<td>
	        			<input type="text" name="source" id="source" value="<?php if(!empty($source)) { echo $source; } ?>" class="txt requiredField" /><br/>
	        			(referrer: google, citysearch, newsletter4)
	        		</td>
	        	</tr>
				<tr valign="top">
					<th scope="row">
		    			<label for="source">Campaign medium *</label>
		    		</th>
		    		<td>
	        			<input type="text" name="medium" id="medium" value="<?php if(!empty($medium)) { echo $medium; } ?>" class="txt requiredField" /><br/>
	        			(marketing medium: cpc, banner, email)
	        		</td>
	        	</tr>
				<tr valign="top">
					<th scope="row">
		    			<label for="source">Campaign term</label>
		    		</th>
		    		<td>
	        			<input type="text" name="term" id="term" value="<?php if(!empty($term)) { echo $term; } ?>" class="txt" /><br/>
	        			(identify the paid keywords)
	        		</td>
	        	</tr>
				<tr valign="top">
					<th scope="row">
		    			<label for="source">Campaign content</label>
		    		</th>
		    		<td>
	        			<input type="text" name="content" id="content" value="<?php if(!empty($content)) { echo $content; } ?>" class="txt" /><br/>
	        			(use to differentiate ads)
	        		</td>
	        	</tr>
				<tr valign="top">
					<th scope="row">
		    			<label for="source">Campaign name *</label>
		    		</th>
		    		<td>
	        			<input type="text" name="name" id="name" value="<?php if(!empty($name)) { echo $name; } ?>" class="txt" /><br/>
	        			(product, promo code or slogan)
	        		</td>
	        	</tr>
        	</tbody>
    	</table>

    	<p class="submit">
    	<input class="button-primary button" type="submit" value="Generate" name="submit" id="submit" />
    	</p>
	</form>

<?php
//output new url string
if (!empty($source)) {
echo "<code>$site/?";
echo "utm_source=$source";
echo "&utm_medium=$medium";
if (!empty($term)) { echo "&utm_term=$term"; }
if (!empty($content)) { echo "&utm_content=$content"; }
echo "&utm_campaign=$name</code>";
}

//Let's add some notes and info
?>
<p>
	For more information visit <a href="https://support.google.com/analytics/answer/1033867?hl=en">Google's URL builder page</a>.
</p>
<div class="updated">
	<h3>Notes</h3>
	<ul>
		<li>None of this works without Source, Medium and Name</li>
		<li>You can view the results of your campaign by going to Reporting- >Acquisition- >Campaigns in <a href="http://google.com/analytics">Google Analytics</a></li>
	</ul>
</div>
</div>
<?php }
?>