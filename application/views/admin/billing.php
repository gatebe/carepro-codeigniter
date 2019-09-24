<div class="row">
    <div class="col-md-6">
        <!-- <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <?php echo lang('Currency'); ?>
                </h4>
            </div>
            <div class="card-body">
                <?php
        echo form_open('update', [
            'class' => 'settings',
            'demo'  => 1,
        ]);
        echo form_label(lang('currency_abbreviation'));
        echo form_input('currency_abbreviation', $option['currency_abbreviation'], [
            'class'    => 'form-control',
            'required' => 'required',
        ]);
        echo form_label(lang('currency_symbol'));
        echo form_input('currency_symbol', $option['currency_symbol'], [
            'class'    => 'form-control',
            'required' => 'required',
        ]);
        echo '<br/>';
        echo form_button([
            'type'  => 'submit',
            'class' => 'btn btn-primary',
        ], lang('Update'));
        echo form_close('demo');
        ?>
            </div>
        </div> -->

        <!-- <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <?php echo lang('PayPal'); ?>

                    <i class="fa fa-question-circle show-tip" data-toggle="tooltip"
                       title="<?php echo lang('Leave fields blank to deactivate'); ?>"></i>
                </h4>
            </div>
            <div class="card-body">
                <?php
        echo form_open('update', [
            'class' => 'settings',
            'demo'  => 1,
        ]);
        echo form_label(lang('PayPal locale'));
        echo form_input('paypal_locale', $option['paypal_locale'], ['class' => 'form-control']);
        echo form_label(lang('PayPal  email'));
        echo form_input('paypal_email', $option['paypal_email'], ['class' => 'form-control']);
        echo '<br/>';
        echo form_button([
            'type'  => 'submit',
            'class' => 'btn btn-primary',
        ], lang('Update'));
        echo form_close('demo'); ?>
            </div>
        </div> -->

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <?php echo lang('Stripe'); ?>

                    <i class="fa fa-question-circle show-tip" data-toggle="tooltip"
                       title="<?php echo lang('Leave fields blank to deactivate'); ?>"></i>

                    <?php $this->load->view('help/stripe-signup'); ?>

                </h4>
            </div>
            <div class="card-body">

                <?php
                if(session('company_demo_mode') == 0) {
                    $hidden = ['setting_id' => $settings->setting_id];
                    $attributes = [
                        'class'        => 'settings',
                        'autocomplete' => 'off',
                    ];
                    echo form_open('update', $attributes, $hidden);
                    ?>
                    <div class="custom-control custom-switch mb-3">
                        <?php if(session('daycare_id') > 1): ?>
                            <input type="checkbox" class="custom-control-input" id="stripe_toggle" name="stripe_toggle"
                                <?php
                                if($settings->stripe_toggle == 1) {
                                    echo "checked";
                                }
                                ?>
                            >
                        <?php endif; ?>
                        <label class="custom-control-label" for="stripe_toggle">
                            <strong>Stripe Test Mode</strong>
                        </label>
                    </div>
                    <div class="test_stripe text-danger
                      <?php
                    if($settings->stripe_toggle == 0) {
                        echo "d-none";
                    }
                    ?>
                    ">
                        <?php
                        if(session('daycare_id') > 1):
                            echo form_label(lang('Stripe test public key'));
                            echo form_input('stripe_pk_test', $settings->stripe_pk_test, ['class' => 'form-control']);
                            echo form_label(lang('Stripe test secret key'));
                            echo form_password('stripe_sk_test', $settings->stripe_sk_test, ['class' => 'form-control']);
                        else:
                            echo '<div class="alert alert-danger">NOT AVAILABLE IN DEMO MODE!</div>';
                        endif;
                        echo "<br/>";
                        ?>
                    </div>
                    <div class="live_stripe text-success
                    <?php
                    if($settings->stripe_toggle == 1) {
                        echo "d-none";
                    }
                    ?>
                    ">
                        <?php
                        if(session('daycare_id') > 1):
                            echo form_label(lang('Stripe live public key'));
                            echo form_input('stripe_pk_live', $settings->stripe_pk_live, ['class' => 'form-control']);
                            echo form_label(lang('Stripe live secret key'));
                            echo form_password('stripe_sk_live', $settings->stripe_sk_live, ['class' => 'form-control']);
                        else:
                            echo '<div class="alert alert-danger">NOT AVAILABLE IN DEMO MODE!</div>';
                        endif;
                        echo '<br/>';
                        ?>
                    </div>
                    <?php
                    echo form_label(lang('Enabled'), 'stripe_enabled');
                    echo form_dropdown('stripe_enabled', [
                        0 => lang('No'),
                        1 => lang('Yes'),
                    ], $settings->stripe_enabled, ['class' => 'form-control']);
                    echo '<br/>';
                    echo form_button([
                        'type'  => 'submit',
                        'class' => 'btn btn-primary',
                    ], lang('Update'));
                    echo form_close('demo');
                }
                else {
                    echo '<div class="alert alert-danger">'.lang('feature_disabled_in_demo').'</div>';
                } ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <?php echo lang('Payment methods'); ?>
                </h4>
            </div>
            <div class="card-body">
                <?php echo lang('Add a payment method'); ?> <span class="field_required"> *</span>
                <?php echo form_open('paymentMethods', [
                    'class' => 'settings',
                    'demo'  => 1,
                ]);
                echo '<div class="input-group">';
                echo form_input('title', NULL, [
                    'class'    => 'form-control',
                    'required' => '',
                ]);
                echo '<span class="input-group-btn">';
                echo form_button([
                    'type'  => 'submit',
                    'class' => 'btn btn-primary',
                ], '<i class="fa fa-plus"></i> '.lang('Add'));
                echo '</span></div>';
                echo form_close('demo'); ?>
                <br/>
                <table class="table table-bordered">
                    <?php foreach ($payMethods as $payMethod) : ?>
                        <tr>
                            <td class="col-md-11">
                                <?php echo $payMethod->title; ?>
                            </td>
                            <td class="col-md-1">
                                <a class="delete"
                                   href="<?php echo site_url('settings/deletePaymentMethod/'.$payMethod->id); ?>">
                                    <i class="fa fa-trash-alt text-danger"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <?php echo lang('Invoice'); ?>
                </h4>
            </div>
            <div class="card-body">
                <?php
                $hidden = ['setting_id' => $settings->setting_id];
                echo form_open('update', ['class' => 'settings'], $hidden);
                echo form_label(lang('Invoice terms'), 'invoice_terms');
                if($settings->invoice_terms == '') {
                    echo form_textarea('invoice_terms', session('company_invoice_terms'), ['class' => 'form-control']);
                }
                else {
                    echo form_textarea('invoice_terms', $settings->invoice_terms, ['class' => 'form-control']);
                }
                //                        echo form_label(lang('Invoice notes'), 'invoice_notes');
                //                        echo form_textarea('invoice_notes',$option['invoice_notes'], ['class' => 'form-control']);
                echo '<br/>';
                echo form_button([
                    'type'  => 'submit',
                    'class' => 'btn btn-primary',
                ], lang('submit'));
                echo form_close(); ?>
            </div>
        </div>

    </div>
</div>