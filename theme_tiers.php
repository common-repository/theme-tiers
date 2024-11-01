<?php

	/*
	Plugin Name: Theme Tiers
	Description: Allows you to specify a different theme for each user level.
	Plugin URI: http://revivemediaservices.com
	Version: 1.0
	Author: Revive Media Services
	Author URI: http://revivemediaservices.com

	Copyright 2013  REVIVE MEDIA SERVICES  (email : Brandon@Revivemediaservices.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/

	add_filter('template', 'change_theme');
	add_filter('option_template', 'change_theme');
	add_filter('option_stylesheet', 'change_theme');

	function change_theme($theme) {

		if(is_user_logged_in()){

			// subscriber (cannot edit posts)
			if (!current_user_can('edit_posts')){

				$theme = get_option('subscriber_theme');

			// contributor (can edit posts but cannot delete publish posts)
			}elseif(current_user_can('edit_posts') && !current_user_can('delete_published_posts')){

				$theme = get_option('contributor_theme');

			// author (delete publish posts but cannot delete others pages)
			}elseif(current_user_can('delete_published_posts') && !current_user_can('delete_others_pages')){

				$theme = get_option('author_theme');

			// editor (delete others pages but cannot remove users)
			}elseif(current_user_can('delete_others_pages') && !current_user_can('remove_users')){

				$theme = get_option('editor_theme');

			// administrator (edit dashboard but cannot manage network users)
			}elseif(current_user_can('edit_dashboard') && !current_user_can('manage_network_users')){

				$theme = get_option('adminstrator_theme');

			// super administrator (edit dashboard but cannot manage network users)
			}elseif(current_user_can('manage_network_options')){

				$theme = get_option('super_administrator_theme');

			}else{
				$theme = get_option('default_visitor_theme');	
			}
		}else{
			$theme = get_option('default_visitor_theme');	
		}
		return $theme;


	}

	add_action('admin_menu', 'plugin_settings');

	function plugin_settings() {
    	add_menu_page('Theme Tiers by Revive Media Services', 'Theme Tiers', 'administrator', 'settings', 'display_settings');
	}

	function display_settings() {

		$themes = wp_get_themes();

	    $html = '</pre>
			<div class="wrap">
				<form action="options.php" method="post" name="options">
					
					<div style="background-color:#000;padding:10px;color:#fff;">
						<h1>Theme Tiers by <a href="http://revivemediaservices.com" title="web design" style="color:#00ff2f;text-decoration:none;">Revive Media Services</a></h1>
					</div>

					<div
						style="
							border:1px solid #bebebe;
							padding:5px;
							border-top:0px;
							background: #fdfdfd; /* Old browsers */
							background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZkZmRmZCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNlOGU4ZTgiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
							background: -moz-linear-gradient(top,  #fdfdfd 0%, #e8e8e8 100%); /* FF3.6+ */
							background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fdfdfd), color-stop(100%,#e8e8e8)); /* Chrome,Safari4+ */
							background: -webkit-linear-gradient(top,  #fdfdfd 0%,#e8e8e8 100%); /* Chrome10+,Safari5.1+ */
							background: -o-linear-gradient(top,  #fdfdfd 0%,#e8e8e8 100%); /* Opera 11.10+ */
							background: -ms-linear-gradient(top,  #fdfdfd 0%,#e8e8e8 100%); /* IE10+ */
							background: linear-gradient(to bottom,  #fdfdfd 0%,#e8e8e8 100%); /* W3C */
							filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#fdfdfd\', endColorstr=\'#e8e8e8\',GradientType=0 ); /* IE6-8 */
						">
						<p>Using the drop down menus next to each user level, choose the theme you would like that user level to see.</p>
						<p>You can select a single theme for as many roles as you like; there is no limit.</p>
					</div>

					<h2 style="padding-left:20px;">Select Your Settings</h2>
					' . wp_nonce_field('update-options') . '
					<div style="padding-left:60px;">
					<table class="form-table" width="100%" cellpadding="10">
				 		<tbody>
							<tr>
								<td scope="row" align="left">';
									$html .= '
									<select name="default_visitor_theme">';
										
										foreach($themes as $key => $value){
											//echo 'key:'.$key.'value:'.$value.'<br/>';
											$html .= '<option value="'.$key.'">'.$value.'</option>';
										}

		$html .= '
									</select>
									<label>Default Visitor (Not Logged In)</label>
								</td>
							</tr>
							<tr>
								<td scope="row" align="left">';
									$html .= '
									<select name="default_user_theme">';
										
										foreach($themes as $key => $value){
											//echo 'key:'.$key.'value:'.$value.'<br/>';
											$html .= '<option value="'.$key.'">'.$value.'</option>';
										}

		$html .= '
									</select>
									<label>Default User (Logged In)</label>
								</td>
							</tr>
							<tr>
								<td scope="row" align="left">';
									$html .= '
									<select name="subscriber_theme">';
										
										foreach($themes as $key => $value){
											//echo 'key:'.$key.'value:'.$value.'<br/>';
											$html .= '<option value="'.$key.'">'.$value.'</option>';
										}

		$html .= '
									</select>
									<label>Subscriber</label>
								</td>
							</tr>
							<tr>
								<td scope="row" align="left">';
									$html .= '
									<select name="contributor_theme">';
										
										foreach($themes as $key => $value){
											//echo 'key:'.$key.'value:'.$value.'<br/>';
											$html .= '<option value="'.$key.'">'.$value.'</option>';
										}

		$html .= '
									</select>
									<label>Contributor</label>
								</td>
							</tr>
							<tr>
								<td scope="row" align="left">';
									$html .= '
									<select name="author_theme">';
										
										foreach($themes as $key => $value){
											//echo 'key:'.$key.'value:'.$value.'<br/>';
											$html .= '<option value="'.$key.'">'.$value.'</option>';
										}

		$html .= '
									</select>
									<label>Author</label>
								</td>
							</tr>
							<tr>
								<td scope="row" align="left">';
									$html .= '
									<select name="editor_theme">';
										
										foreach($themes as $key => $value){
											//echo 'key:'.$key.'value:'.$value.'<br/>';
											$html .= '<option value="'.$key.'">'.$value.'</option>';
										}

		$html .= '
									</select>
									<label>Editor</label>
								</td>
							</tr>
							<tr>
								<td scope="row" align="left">';
									$html .= '
									<select name="adminstrator_theme">';
										
										foreach($themes as $key => $value){
											//echo 'key:'.$key.'value:'.$value.'<br/>';
											$html .= '<option value="'.$key.'">'.$value.'</option>';
										}

		$html .= '
									</select>
									<label>Administrator</label>
								</td>
							</tr>
							<tr>
								<td scope="row" align="left">';
									$html .= '
									<select name="super_administrator_theme">';
										
										foreach($themes as $key => $value){
											//echo 'key:'.$key.'value:'.$value.'<br/>';
											$html .= '<option value="'.$key.'">'.$value.'</option>';
										}

		$html .= '
									</select>
									<label>Super Administrator</label>
								</td>
							</tr>
						</tbody>
					</table>
					</div>
					<div style="padding-left:20px;padding-top:20px;">
	 				<input type="hidden" name="action" value="update" />
	 				<input type="hidden" name="page_options" value="default_visitor_theme,default_user_theme,subscriber_theme,contributor_theme,author_theme,editor_theme,adminstrator_theme,super_administrator_theme" />
	 				<input type="submit" name="Submit" value="Update" /></form></div></div>
	 			<pre>
		';


		//print_r($themes);
	    echo $html;

	}



?>