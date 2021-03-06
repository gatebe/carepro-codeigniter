<div class="card">
    <div class="card-header">
        <h4 class="card-title"></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">

                <?php if ($id == $this->user->uid()) : ?>
                    <?php echo lang('user_is_self_warning'); ?>
                <?php else : ?>
                    <div class="callout callout-info">
                        <?php if ($user_status === "activate") : ?>
                            <h3><?php echo lang('activate_heading'); ?></h3>
                            <p><?php echo sprintf(lang('activate_subheading'), $this->user->get($id, 'name')); ?></p>
                        <?php elseif ($user_status === "deactivate") : ?>
                            <h3><?php echo lang('deactivate_heading'); ?></h3>
                            <p><?php echo sprintf(lang('deactivate_subheading'), $this->user->get($id, 'name')); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php echo form_open('users/'.$user_status.'/status'); ?>
                    <input type="hidden" name="user_id" value="<?php echo $id ?>">
                    <input type="hidden" name="confirm" value="yes" />
                    <?php 
                        if(is('staff')){
                            $url = 'parents';
                        }else{
                            $url = 'users';
                        }
                        echo anchor($url,
                             lang('Cancel'), 
                             'class="btn btn-default"'); 
                    ?>
                    <button class="btn btn-primary"><?php echo lang('Yes'); ?></button>
                    <?php echo form_close(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-footer">

    </div>
</div>