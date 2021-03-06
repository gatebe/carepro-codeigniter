<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><?php echo lang('logo'); ?></div>
            </div>
            <div class="card-body">

                <!-- <?php if (is_file(APPPATH . '../assets/uploads/content/' . session('company_logo'))) : ?>
                                    <img style="width:100%" src="<?php echo base_url() . 'assets/uploads/content/' . session('company_logo'); ?>"/>
                <?php endif; ?> -->
                <?php if($settings->logo != ''): ?>
                    <img src="<?php echo base_url() . 'assets/uploads/daycare_logo/' . $settings->logo; ?>" style="width:100%"/>
                <?php else: ?>
                    <img src="<?php echo base_url() . 'assets/uploads/content/logo.png' ?>" style="width:100%"/>
                <?php endif;?>
                <hr />

                <div class="alert alert-warning">
                    <?php echo lang('logo_instructions'); ?>
                </div>

                <?php
                $hidden = array('daycare_id' => $settings->daycare_id, 'daycare_unquie_id' => $settings->daycare_unquie_id);
                echo form_open_multipart('upload_logo', 'class="input-group"', $hidden); ?>
                <input class="form-control" type="file" required name="logo" />
                <span class="input-group-btn">
                    <button class="btn btn-default">
                        <?php echo lang('update'); ?>
                    </button>
                </span>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <?php echo lang('invoice logo'); ?>
                </div>
            </div>
            <div class="card-body">
                <?php if ($settings->invoice_logo == NULL) : ?>
                    <img src="<?php echo base_url() . 'assets/uploads/content/' . $this->session->userdata('company_invoice_logo'); ?>" />
                <?php else : ?>
                    <img src="<?php echo base_url() . 'assets/uploads/invoice_logo/' . $settings->invoice_logo; ?>" />
                <?php endif; ?>
                <hr />
                <div class="alert alert-warning">
                    <?php echo lang('logo_instructions'); ?>
                </div>
                <?php
                $hidden = array('daycare_unquie_id' => $settings->daycare_unquie_id, 'settings_id' => $settings->setting_id);
                echo form_open_multipart('invoice_logo', 'class="input-group"', $hidden);
                echo form_input(['type' => 'file', 'name' => 'invoice_logo', 'required' => '', 'class' => 'form-control']);
                echo '<span class="input-group-btn">';
                echo form_button(['type' => 'submit', 'class' => 'btn btn-primary'], lang('Update'));
                echo '</span>';
                echo form_close();
                ?>
            </div>
        </div>
    </div>
</div>