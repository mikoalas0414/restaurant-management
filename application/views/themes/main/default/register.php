<?php echo $header; ?>
<div class="row page-heading"><h3><?php echo $text_register; ?></h3></div>

<div id="notification" class="row">
<?php if (!empty($alert)) { ?>
	<div class="alert alert-dismissable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo $alert; ?>
	</div>
<?php } ?>
</div>
<div class="row content">
	<div class="wrap-all page-content">
		<p class="text-info well"><?php echo $text_login_register; ?></p>
		<div class="register-box wrap-vertical">
			<form method="post" accept-charset="utf-8" action="<?php echo current_url(); ?>" role="form" class="">
				<div class="form-group">
					<label for="first-name"><?php echo $entry_first_name; ?></label></td>
					<input type="text" id="first-name" class="form-control" value="<?php echo set_value('first_name'); ?>" name="first_name">
					<?php echo form_error('first_name', '<span class="error help-block">', '</span>'); ?>
				</div>
				<div class="form-group">
					<label for="last-name"><?php echo $entry_last_name; ?></label>
					<input type="text" id="last-name" class="form-control" value="<?php echo set_value('last_name'); ?>" name="last_name">
					<?php echo form_error('last_name', '<span class="error help-block">', '</span>'); ?>
				</div>
				<div class="form-group">
					<label for="email"><?php echo $entry_email; ?></label>
					<input type="text" id="email" class="form-control" value="<?php echo set_value('email'); ?>" name="email">
					<?php echo form_error('email', '<span class="error help-block">', '</span>'); ?>
				</div>
				<div class="form-group">
					<label for="password"><?php echo $entry_password; ?></label>
					<input type="password" id="password" class="form-control" value="" name="password">
					<?php echo form_error('password', '<span class="error help-block">', '</span>'); ?>
				</div>
				<div class="form-group">
					<label for="password-confirm"><?php echo $entry_password_confirm; ?></label>
					<input type="password" id="password-confirm" class="form-control" name="password_confirm" value="">
					<?php echo form_error('password_confirm', '<span class="error help-block">', '</span>'); ?>
				</div>
				<div class="form-group">
					<label for="telephone"><?php echo $entry_telephone; ?></label>
					<input type="text" id="telephone" class="form-control" value="<?php echo set_value('telephone'); ?>" name="telephone">
					<?php echo form_error('telephone', '<span class="error help-block">', '</span>'); ?>
				</div>
				<div class="form-group">
					<label for="security-question"><?php echo $entry_s_question; ?></label>
					<select name="security_question" id="security-question" class="form-control">
					<?php foreach ($questions as $question) { ?>
						<option value="<?php echo $question['id']; ?>"><?php echo $question['text']; ?></option>
					<?php } ?>
					</select>
					<?php echo form_error('security_question', '<span class="error help-block">', '</span>'); ?>
				</div>
				<div class="form-group">
					<label for="security-answer"><?php echo $entry_s_answer; ?></label>
					<input type="text" id="security-answer" class="form-control" name="security_answer">
					<?php echo form_error('security_answer', '<span class="error help-block">', '</span>'); ?>
				</div>
				<div class="checkbox">
					<label><?php echo $entry_newsletter; ?>
						<input type="checkbox" name="newsletter" value="1">
					</label>
					<?php echo form_error('newsletter', '<span class="error help-block">', '</span>'); ?>
				</div>
			
				<div class="form-group">
					<p class="text-danger small"><?php echo $text_required; ?></p>
				</div>
		
				<div class="buttons">
					<button type="submit" class="btn btn-success"><?php echo $button_register; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php echo $footer; ?>