<?php if ( $_POST['submit'] == 'Export' ) : ?>
	<?php if ( empty( $_POST['suppliers'] ) ) : ?>
	<div class="notice notice-warning is-dismissible">
		<p><?php echo __( 'Please select a supplier' ); ?></p>
	</div>
<?php endif; ?>
<?php endif; ?>
				
				
<?php if ( $_POST['submit'] == 'Import' ) : ?>
	<?php
	if ( empty( $_FILES['csv']['tmp_name'] ) || ! is_uploaded_file( $_FILES['csv']['tmp_name'] ) ) :
		?>
<div class="notice notice-warning is-dismissible">
		<p><?php echo __( 'Please upload a CSV file' ); ?></p>
	</div>
	<?php else : ?>
		<div class="notice notice-warning is-dismissible">
		<p><?php echo __( 'CSV imported' ); ?></p>
	</div>
	<?php endif; ?>
<?php endif; ?>
