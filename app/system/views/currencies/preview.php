<div class="row-fluid">
    <?= form_open(current_url(),
        [
            'id'   => 'edit-form',
            'role' => 'form',
                'method' => 'PATCH'
        ]
    ); ?>

    <?= $this->controller->renderForm(['preview' => TRUE]); ?>

    <?= form_close(); ?>
</div>
