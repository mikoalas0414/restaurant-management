<?php echo $header; ?>
<div class="row content">
	<div class="col-md-12">
		<div id="notification">
			<div class="alert alert-dismissable">
				<?php if (!empty($alert)) { ?>
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $alert; ?>
				<?php } ?>
				<?php if (validation_errors()) { ?>
					<p class="alert-danger">Sorry but validation has failed, please check for errors.</p>
				<?php } ?>
			</div>
		</div>

		<form role="form" id="edit-form" class="form-horizontal" accept-charset="utf-8" method="post" action="<?php echo current_url(); ?>">
			<div id="general" class="tab-pane row wrap-all active">
				<div class="form-group">
					<label for="input-recipient" class="col-sm-2 control-label">To:</label>
					<div class="col-sm-5">
						<select name="recipient" id="input-recipient" class="form-control">
							<option value="all_newsletters">All Newsletter Subscribers</option>
							<option value="all_customers">All Customers</option>
							<option value="customer_group">Customer Group</option>
							<option value="customers">Customers</option>
							<option value="all_staffs">All Staffs</option>
							<option value="staff_group">Staff Group</option>
							<option value="staffs">Staffs</option>
						</select>
						<?php echo form_error('recipient', '<span class="text-danger">', '</span>'); ?>
					</div>
				</div>
				<div id="recipient-customer-group" class="recipient">
					<div class="form-group">
						<label for="input-customer-group" class="col-sm-2 control-label">Customer Group:</label>
						<div class="col-sm-5">
							<select name="customer_group_id" id="input-customer-group" class="form-control">
								<?php foreach ($customer_groups as $customer_group) { ?>
									<option value="<?php echo $customer_group['customer_group_id']; ?>" <?php echo set_select('customer_group_id', $customer_group['customer_group_id']); ?>><?php echo $customer_group['group_name']; ?></option>
								<?php } ?>
							</select>
							<?php echo form_error('customer_group_id', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
				</div>
				<div id="recipient-staff-group" class="recipient">
					<div class="form-group">
						<label for="input-staff-group" class="col-sm-2 control-label">Staff Group:</label>
						<div class="col-sm-5">
							<select name="staff_group_id" id="input-staff-group" class="form-control">
								<?php foreach ($staff_groups as $staff_group) { ?>
									<option value="<?php echo $staff_group['staff_group_id']; ?>" <?php echo set_select('staff_group_id', $staff_group['staff_group_id']); ?>><?php echo $staff_group['staff_group_name']; ?></option>
								<?php } ?>
							</select>
							<?php echo form_error('staff_group_id', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
				</div>
				<div id="recipient-customers" class="recipient">
					<div class="form-group">
						<label for="input-customer" class="col-sm-2 control-label">Customers:</label>
						<div class="col-sm-6">
							<input type="text" name="customer" id="input-customer" class="form-control" value="" placeholder="Start typing customer name..." />
							<?php echo form_error('customer', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
					<div id="customers-box" class="form-group">
						<label for="" class="col-sm-2 control-label"></label>
						<div class="col-sm-6">
							<div class="panel-selected">
								<table class="table table-striped table-border">
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div id="recipient-staffs" class="recipient">
					<div class="form-group">
						<label for="input-staff" class="col-sm-2 control-label">Staffs:</label>
						<div class="col-sm-6">
							<input type="text" name="staff" id="input-staff" class="form-control" value="" placeholder="Start typing staff name..." />
							<?php echo form_error('staff', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
					<div id="staffs-box" class="form-group">
						<label for="" class="col-sm-2 control-label"></label>
						<div class="col-sm-6">
							<div class="panel-selected">
								<table class="table table-striped table-border">
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div id="send-type" class="">
					<div class="form-group">
						<label for="input-send-type" class="col-sm-2 control-label">Send Type:</label>
						<div class="col-sm-5">
							<div class="btn-group btn-group-toggle" data-toggle="buttons">
								<label class="btn btn-default active" data-btn="btn-danger"><input type="radio" name="send_type" value="account" <?php echo set_radio('send_type', 'account', TRUE); ?>>Account</label>
								<label class="btn btn-default" data-btn="btn-success"><input type="radio" name="send_type" value="email" <?php echo set_radio('send_type', 'email'); ?>>Email</label>
							</div>
							<?php echo form_error('send_type', '<span class="text-danger">', '</span>'); ?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="input-subject" class="col-sm-2 control-label">Subject:</label>
					<div class="col-sm-5">
						<input type="text" name="subject" id="input-subject" class="form-control" value="<?php echo set_value('subject'); ?>" size="40" />
						<?php echo form_error('subject', '<span class="text-danger">', '</span>'); ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						<textarea name="body" id="input-body" class="form-control" style="height:300px;width:100%;"><?php echo set_value('body'); ?></textarea>
						<?php echo form_error('body', '<span class="text-danger">', '</span>'); ?>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript" src="<?php echo base_url("assets/js/tinymce/tinymce.js"); ?>"></script>
<script type="text/javascript">
tinymce.init({
    selector: 'textarea',
    menubar: false,
	plugins : 'table link image code charmap autolink lists textcolor',
	toolbar1: 'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect | bullist numlist',
	toolbar2: 'forecolor backcolor | outdent indent | undo redo | link unlink anchor image code | hr table | subscript superscript | charmap',
	removed_menuitems: 'newdocument',
	skin : 'tiskin',
	convert_urls : false,
    file_browser_callback : imageManager

});
</script>
<script type="text/javascript"><!--	
$('select[name="recipient"]').on('change', function() {
	$('.recipient').hide();
	
	$('#recipient-' + $(this).val().replace('_', '-')).show();
});

$('select[name=\'recipient\']').trigger('change');
//--></script> 
<script type="text/javascript"><!--
$('input[name=\'customer\']').select2({
	minimumInputLength: 2,
	ajax: {
		url: '<?php echo site_url(ADMIN_URI ."/customers/autocomplete"); ?>',
		dataType: 'json',
		quietMillis: 100,
		data: function (term, page) {
			return {
				term: term, //search term
				page_limit: 10 // page size
			};
		},
		results: function (data, page, query) {
			return { results: data.results };
		}
	},
	initSelection: function(element, callback) {
		return $.getJSON('<?php echo site_url(ADMIN_URI ."/customers/autocomplete?customer_id="); ?>' + (element.val()), null, function(json) {
        	var data = {id: json.results[0].id, text: json.results[0].text};
			return callback(data);
		});
	}
});

$('input[name=\'customer\']').on('select2-selecting', function(e) {
	$('#customer' + e.choice.id).remove();
	$('#customers-box table tbody').append('<tr id="customer' + e.choice.id + '"><td class="name">' + e.choice.text + '<td class="text-right">' + '<a class="btn btn-times" onclick="$(this).parent().parent().remove();"><i class="fa fa-times-circle"></i></a>' + '<input type="hidden" name="customers[]" value="' + e.choice.id + '" /></tr>');
});
//--></script>
<script type="text/javascript"><!--
$('input[name=\'staff\']').select2({
	minimumInputLength: 2,
	ajax: {
		url: '<?php echo site_url(ADMIN_URI ."/staffs/autocomplete"); ?>',
		dataType: 'json',
		quietMillis: 100,
		data: function (term, page) {
			return {
				term: term, //search term
				page_limit: 10 // page size
			};
		},
		results: function (data, page, query) {
			return { results: data.results };
		}
	},
	initSelection: function(element, callback) {
		return $.getJSON('<?php echo site_url(ADMIN_URI ."/staffs/autocomplete?staff_id="); ?>' + (element.val()), null, function(json) {
        	var data = {id: json.results[0].id, text: json.results[0].text};
			return callback(data);
		});
	}
});

$('input[name=\'staff\']').on('select2-selecting', function(e) {
	$('#staff' + e.choice.id).remove();
	$('#staffs-box table tbody').append('<tr id="staff' + e.choice.id + '"><td class="name">' + e.choice.text + '<td class="text-right">' + '<a class="btn btn-times" onclick="$(this).parent().parent().remove();"><i class="fa fa-times-circle"></i></a>' + '<input type="hidden" name="staffs[]" value="' + e.choice.id + '" /></tr>');
});
//--></script>
<?php echo $footer; ?>