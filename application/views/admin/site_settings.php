<div class="card">
    <div class="card-header">
        <div class="card-title">
            <?php echo lang('settings'); ?>
        </div>
    </div>
    <div class="card-body">
        <?php echo form_open('update', ['class' => 'settings', 'demo' => 1]); ?>
        <div class="row">
            <div class="col-md-6">
                <input type="hidden" value="<?php echo $settings->daycare_id ?>" name="id">
                <input type="hidden" value="<?php echo $settings->address_id ?>" name="address_id">
                <input type="hidden" value="<?php echo $settings->setting_id ?>" name="setting_id">
                <?php
                echo form_label(lang('name'),'name', ['class' => 'required']);
                echo form_input('name', $settings->name, ['class' => 'form-control', 'required' => 'required','id' => 'name']);
                echo form_label(lang('slogan'));
                echo form_input('slogan', $settings->slogan, ['class' => 'form-control']);
                echo form_label(lang('Facility ID'), 'facility_id');
                echo form_input('facility_id',$settings->facility_id, ['class' => 'form-control']);
                echo form_label(lang('Tax ID'), 'facility_id');
                echo form_input('employee_tax_identifier', $settings->employee_tax_identifier, ['class' => 'form-control']);
                echo form_label(lang('Daycare ID'));
                echo form_input('daycare_unquie_id', $settings->daycare_unquie_id, ['class' => 'form-control','readonly'=>'true']);
                echo "<hr/>";
                echo form_label(lang('email'),'email',['class' => 'required']);
                echo form_input('email', $settings->email, ['class' => 'form-control', 'required' => 'required', 'id' => 'email']);
                echo form_label(lang('phone'),'phone',['class' => 'required']);
                echo form_input('phone', $settings->phone, ['class' => 'form-control', 'required' => 'required','id' => 'phone']);
                echo form_label(lang('fax'));
                echo form_input('fax', $settings->fax, ['class' => 'form-control']);
                echo form_label(lang('street'),'address_line_1',['class' => 'required']);
                echo form_input('address_line_1', $settings->address_line_1, ['class' => 'form-control', 'required' => 'required', 'id' => 'address_line_1']);
                echo "<br/>";
                echo form_label(lang('street2'));
                echo form_input('address_line_2', $settings->address_line_2, ['class' => 'form-control']);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        echo form_label(lang('city'),'city',['class' => 'required']);
                        echo form_input('city', $settings->city, ['class' => 'form-control', 'required' => 'required','id' => 'city']);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        echo form_label(lang('state'), 'state' , ['class' => 'required']);
                        echo form_input('state', $settings->state, ['class' => 'form-control', 'required' => 'required','id' => 'state']);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        echo form_label(lang('postal_code'),'zip_code',['class' => 'required']);
                        echo form_input('zip_code',$settings->zip_code, ['class' => 'form-control', 'required' => 'required','id' => 'zip_code']);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        echo form_label(lang('country'),'country',['class' => 'required']);
                        echo form_input('country', $settings->country, ['class' => 'form-control', 'required' => 'required','id' => 'country']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <?php
                echo form_label(lang('timezone'), 'timezone', ['class' => 'required']);
                echo form_input('timezone', $settings->timezone, ['class' => 'form-control', 'required' => 'required', 'id' => 'timezone']);
                // echo form_label(lang('google_analytics'));
                // echo form_input('google_analytics', $settings->google_analytics, ['class' => 'form-control']);
                echo form_label(lang('date_format'), 'date_format', ['class' => 'required']);
                echo form_input('date_format', $settings->date_format, ['class' => 'form-control', 'required' => 'required', 'id' => 'date_format']);
                // echo form_label(lang('Lockscreen timer (mins)'));
                // echo form_input(['type' => 'number', 'step' => 'any', 'name' => 'lockscreen_timer'], $option['lockscreen_timer'], ['class' => 'form-control']);
                echo form_label(lang('Business hours'), 'hours_start');

                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label('Start time');
                        echo form_time('start_time', $settings->start_time, ['class' => 'form-control']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label('End time');
                        echo form_time('end_time', $settings->end_time, ['class' => 'form-control']);
                        ?>
                    </div>
                </div>
                <hr/>
                <!-- <div class="row">
                    <div class="col-md-1">
                        <?php echo form_hidden('daily_checkin', 0); ?>
                        <?php echo form_checkbox('daily_checkin', 1, $option['daily_checkin']); ?>
                    </div>
                    <div class="col-md-10"><?php echo lang('Restrict to daily checkin'); ?>
                        <i class="fa fa-question-circle text-warning show-tip"
                           data-toggle="tooltip"
                           title="<?php echo lang('Uncheck to calculate time accross days instead of just daily'); ?>"></i>
                    </div>
                </div> -->
                <!-- <div class="row">
                    <div class="col-md-1">
                        <?php echo form_hidden('allow_registration', 0); ?>
                        <?php echo form_checkbox('allow_registration', 1, $option['allow_registration']); ?>
                    </div>
                    <div class="col-md-10"><?php echo lang('Allow registration'); ?></div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <?php echo form_hidden('allow_reset_password', 0); ?>
                        <?php echo form_checkbox('allow_reset_password', 1, $option['allow_reset_password']); ?>
                    </div>
                    <div class="col-md-10"><?php echo lang('Allow resetting password'); ?></div>
                </div> -->
                <!-- <div class="row">
                    <div class="col-md-1">
                        <?php echo form_hidden('enable_captcha', 0); ?>
                        <?php echo form_checkbox('enable_captcha', 1, $option['enable_captcha']); ?>
                    </div>
                    <div class="col-md-10"><?php echo lang('Enable captcha'); ?></div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <?php echo form_hidden('demo_mode', 0); ?>
                        <?php echo form_checkbox('demo_mode', 1, $option['demo_mode']); ?>
                    </div>
                    <div class="col-md-10"><?php echo lang('Demo mode'); ?></div>
                </div> -->
                <!-- <div class="row">
                    <div class="col-md-1">
                        <?php echo form_hidden('maintenance_mode', 0); ?>
                        <?php echo form_checkbox('maintenance_mode', 1, $option['maintenance_mode']); ?>
                    </div>
                    <div class="col-md-10"><?php echo lang('Maintenance mode'); ?></div>
                </div> -->
                <!-- <div class="row">
                    <div class="col-md-1">
                        <?php echo form_hidden('use_smtp', 0); ?>
                        <?php echo form_checkbox('use_smtp', 1, $option['use_smtp']); ?>
                    </div>
                    <div class="col-md-10"><?php echo lang('Use SMTP'); ?>
                        <a class="cursor"
                           onclick="document.querySelector('.smtp-settings').classList.toggle('hidden');">
                            <?php echo lang('Update SMTP settings'); ?>
                        </a>
                        <div class="smtp-settings hidden">
                            <hr/>
                            <?php
                            if(session('company_demo_mode') == 0) {
                                echo form_label(lang('smtp_host'));
                                echo form_input('smtp_host', $option['smtp_host'], ['class' => 'form-control']);

                                echo form_label(lang('smtp_user'));
                                echo form_input('smtp_user', $option['smtp_user'], ['class' => 'form-control']);

                                echo form_label(lang('smtp_pass'));
                                echo form_password('smtp_pass', $option['smtp_pass'], ['class' => 'form-control']);

                                echo form_label(lang('smtp_port'));
                                echo form_input('smtp_port', $option['smtp_port'], ['class' => 'form-control']);
                            } else {
                                echo '<div class="alert alert-danger">'.lang('feature_disabled_in_demo').'</div>';
                            }
                            ?>
                        </div>

                    </div>
                </div> -->
                <hr/>
                <!-- <button type="submit" class="btn btn-default"><?php echo lang('update'); ?></button> -->
                <?php
                    echo form_button(
                        [
                            'type' => 'submit',
                            'class' => 'btn btn-default',
                        ], lang('update'));
                ?>
            </div>
        </div>
        <hr/>

        <?php echo form_close(); ?>
    </div>
</div>