<?php
namespace EWA\RvnewsImportExport;
?>


<div class="wrap">
	<h2>Product Import/Export</h2>


<form name="form1" method="post" action="" enctype="multipart/form-data">
	<div class="export">
		<h3>Export</h3>
		<div class="imp-exp export-section">
<?php
$first_element = array( '' => __( '--Select--' ) );
$field_name    = 'suppliers';
$extra         = array(
	'id'    => $field_name,
	'class' => 'suppliers',
);
$options       = $suppliers;
$options       = $first_element + $suppliers;
$selected_item = ( isset( $_POST[ $field_name ] ) ) ? intval( $_POST[ $field_name ] ) : '';
$selected      = array( $selected_item );
echo "<div class='form-field'>";
echo Helper::form_label( 'Select a supplier to export:' );
echo '<br/>';
echo Helper::form_dropdown( $field_name, $options, $selected, $extra );
echo '</div>';
?>

<?php
submit_button( 'Export' );
?>

<hr/>
<input type="submit" name="submit" id="submit" class="btn-imp-exp button button-primary" value="Download Distributors Names">
<br/>
<i>Copy and paste from this cheatsheet for the Distributor Name fields on the spreadsheet because the names have to be exactly the same as in the database in order to associate a product to a distributor.</i>

<br/>
<br/>

<input type="submit" name="submit" id="submit" class="btn-imp-exp button button-primary" value="Download Category Names">
<br/>
<i>Copy and paste from this cheatsheet for the Category Name fields on the spreadsheet because the names have to be exactly the same as in the database in order to associate a product to a category.</i>
</div>
</div>
</div>
</div>



<div class="import">
	<h3>Import</h3>
	<div class="imp-exp import-section">
<div class="form-field">
	<label>Upload CSV File</label>
	<input type="file" name="csv">
</div>
<?php
submit_button( 'Import' );
?>



<?php wp_nonce_field( 'import_export' ); ?>
</form>
</div>
