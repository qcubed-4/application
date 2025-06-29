		<div class="title_action"><?php _p($this->strTitleVerb); ?></div>
		<div class="title"><?php _t('Person')?></div>
		<br class="item_divider" />

		<?php $this->lblId->renderWithName(); ?>
		<br class="item_divider" />

		<?php $this->txtFirstName->renderWithName(); ?>
		<br class="item_divider" />

		<?php $this->txtLastName->renderWithName(); ?>
		<br class="item_divider" />

		<?php $this->lstLogin->renderWithName(); ?>
		<br class="item_divider" />

		<?php $this->lstProjectsAsTeamMember->renderWithName(true, "Rows=10"); ?>
		<br class="item_divider" />


		<br />
		<?php $this->btnSave->render() ?>
		&nbsp;&nbsp;&nbsp;
		<?php $this->btnCancel->render() ?>
