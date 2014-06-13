<?php 
/*

Plugin Name: Links Auditor
Plugin URI: http://tonyspiro.com
Description: A plugin that helps you manage the old link redirects for a new site.  Add your old links and new links and Link Auditor works automatically.
Version: 0.2
Author: Tony Spiro
Author URI: http://tonyspiro.com
License: GPL2

Copyright 2014  Tony Spiro (email: tspiro@tonyspiro.com)

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

/*	LA Redirects
==================================== */

/*

Testing

ini_set('display_errors',1); 
error_reporting(E_ALL);

*/


include('controllers.php');

$siteurl = get_bloginfo('siteurl');

function getUrl() {
  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
  $url .= $_SERVER["REQUEST_URI"];
  return $url;
}

$actual_link = getUrl();

$all_redirects = $redirects->getAll();

if($all_redirects){
	foreach($all_redirects as $redirect_id){
		$la_redirect = $redirects->getFields($redirect_id);
		$la_old_link = str_replace($siteurl, "", $la_redirect['old_link']);
		$la_new_link = str_replace($siteurl, "", $la_redirect['new_link']);
		if($actual_link == $siteurl . $la_old_link){
			header("Location: " . $siteurl . $la_new_link);
			die();
		}
	}
}

function la_redirect_options() {

	$redirects = new Redirects;

	?>
	<link rel="stylesheet" href="<?php echo plugins_url(); ?>/links-auditor/lib/bootstrap-3.1.1.css" />
	<script src="<?php echo plugins_url(); ?>/links-auditor/lib/bootstrap-3.1.1.js"></script>
	<link rel="stylesheet" href="<?php echo plugins_url(); ?>/links-auditor/style.css" />

	<div class="container">
		<?php
		if($_GET['page']=="links-auditor" && isset($_GET['message'])){
			?>
			<div class="alert alert-success">Redirects saved.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>
			<?php
		}
		?>
		<h1>Links Auditor 301 Redirects</h1>
		<p>
			Add your old paths <code>/old-path-from-old-site</code> in the old link field and the new path <code>/new-path-in-new-site</code> in the new link fields. Title and Section are there for your organization and convenience.  
			Links Auditor works automatically by redirecting users from your old links to your new ones.			
</p>
		<form action="" method="post">
			<input type="hidden" name="links_audit_submit" value="true">
			<table class="table table-striped table-bordered">
				<tr>
					<td class="col-md-3">Title</td>
					<td class="col-md-3">Section</td>
					<td class="col-md-3">Old Link</td>
					<td class="col-md-3">New Link</td>
				</tr>
				<?php
				/// Custom redirects
				$custom_redirects = $redirects->getAll();

				if($custom_redirects){
					?>
					<?php
					foreach($redirects->getAll() as $custom_id){

						$fields = $redirects->getFields($custom_id);
						?>
						<tr id="customRow<?php echo $custom_id; ?>">
							<td><input type="text" class="form-control" placeholder="Title" name="title[]" value="<?php echo $fields['title']; ?>" /></td>
							<td><input type="text" class="form-control" placeholder="Section" name="section[]" value="<?php echo $fields['section']; ?>" /></td>
							<td><input placeholder="Old Link" name="old_link[]" class="form-control" value="<?php echo $fields['old_link']; ?>" /></td>
							<td>
								<table class="no-border col-sm-12">
									<tr><td><input type="text" class="form-control" placeholder="New Link" name="new_link[]" value="<?php echo $fields['new_link']; ?>" /></td><td><a title="remove row" class="remove-custom pull-right" href="#" data-id="<?php echo $custom_id; ?>">x</a></td></tr>
								</table>
							</td>
						</tr>
						<?php
					}

				}
				?>
				<tr id="addRow">
					<td colspan="10"><a id="addRowBtn" class="btn btn-default pull-right" href="#">+ Add a new row</a></td>
				</tr>
				<tr>
					<td colspan="10" class="text-right"><button type="submit" class="btn btn-default btn-success">Save All</button></td></td>
				</tr>
			</table>
		</form>
		<div class="text-center" style="margin-bottom: 40px;">
			<!-- Button trigger modal -->
			<button class="btn btn-default" data-toggle="modal" data-target=".bs-example-modal-lg">
				Get .htaccess redirects
			</button>
		</div>
	</div><!-- .container -->

	<script>

		jQuery(window).on('resize', function(){

			setTimeout(function(){ jQuery('body').addClass('sticky-menu'); }, 500);

		});

		jQuery(function(){

			setTimeout(function(){ jQuery('body').addClass('sticky-menu'); }, 500);

			var rowId = 0;

			jQuery('#addRowBtn').on('click', function(e){

				e.preventDefault();

				var newRow = '<tr id="row' + rowId + '">' + 
				'<td><input type="text" class="form-control" placeholder="Title" name="title[]" /></td>' + 
				'<td><input type="text" class="form-control" placeholder="Section" name="section[]" /></td>' + 
				'<td><input name="old_link[]" class="pull-left form-control" placeholder="Old Link" /></td>' + 
				'<td><table class="no-border col-sm-12">' + 
				'<tr><td><input type="text" class="form-control" placeholder="New Link" name="new_link[]" /></td><td><a title="remove row" class="remove-row pull-right" href="#" data-id="' + rowId + '">x</a></td>' +
				'</tr></table></td>' + 
				'</tr>';

				jQuery('#addRow').before(newRow);

				rowId++;

				jQuery('.remove-row').on('click', function(e){
					e.preventDefault();
					var id = jQuery(this).data('id');
					jQuery('#row' + id).fadeOut(function(){
						jQuery(this).remove();
					});
				});

			});

		});
	</script>
	<script>

		jQuery(function(){
			
			jQuery('.remove-custom').on('click', function(e){

				e.preventDefault();

				var confirmDelete = confirm("Are you sure you want to delete this 1 row?  This cannot be undone.");

				if(confirmDelete){

					var id = jQuery(this).data('id');

					jQuery.ajax({
						type: "POST",
						url: '',
						data: { delete_custom : 'true', custom_id: id }
					
					}).done(function( html ) {

						jQuery('#customRow' + id).fadeOut(function(){
							jQuery(this).remove();
						});

					});
				}
			});
		});
	</script>  
<!-- Modal -->
<div class="modal bs-example-modal-lg"  id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Copy this text to your .htaccess file</h4>
			</div>
			<div class="modal-body">
				<textarea class="form-control" style="min-height: 200px;"><?php
					foreach($redirects->getAll() as $custom_id){
						$fields = $redirects->getFields($custom_id);
						$old_link = $fields['old_link'];
						$new_link = $fields['new_link'];
						if($old_link && $new_link) echo "Redirect 301 " . $old_link . " " . $new_link . "\n";
					}
					?></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-red" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
}

add_action( 'admin_menu', 'links_auditor' );

function links_auditor() {
	add_options_page( 'Links Auditor', 'Links Auditor', 'manage_options', 'links-auditor', 'la_redirect_options' );
}
