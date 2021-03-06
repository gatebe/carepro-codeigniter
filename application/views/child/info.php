<?php if(is_checked_in($child->id)): ?>
    <div class="card bg-success">
        <div class="card-body">
            <h3>
                <?php echo lang('Checked in'); ?>

                <?php if(!is('parent')): ?>
                    <button id="<?php echo $child->id; ?>" class="btn btn-danger btn-sm pull-right checkout-btn">
                        <img src="<?php echo assets('img/content/right.svg'); ?>" style="width:20px;"/>
                        <?php echo lang('Check out'); ?>
                    </button>
                <?php endif; ?>
            </h3>
            <?php
            echo '<strong class="text-info">'.lang('Date in').'</strong>: '
                .$this->child->checkedInLog($child->id, 'date_in')
                .' <strong class="text-info">'.lang('Time in').'</strong>: '
                .$this->child->checkedInLog($child->id, 'time_in')
                .' | '
                .$this->child->checkedInLog($child->id, 'timer')
                .' | '
                .'<strong class="text-info">'.lang('By').'</strong>: '
                .$this->child->checkedInLog($child->id, 'in_guardian');
            ?>
        </div>
    </div>
<?php else: ?>
    <div class="card bg-warning">
        <div class="card-body">
            <?php if(!is('parent')): ?>
                <h3>
                    <?php echo lang('Not checked in'); ?>

                    <?php if(!is('parent')): ?>
                        <button id="<?php echo $child->id; ?>" class="btn btn-success btn-sm pull-right checkin-btn">
                            <img src="<?php echo assets('img/content/left.svg'); ?>" style="width:21px;"/>
                            <?php echo lang('Check in'); ?>
                        </button>
                    <?php endif; ?>

                </h3>
            <?php endif; ?>

            <?php
            echo '<strong>'.lang('Last checked out').':</strong> '
                .$this->child->lastCheckedOut($child->id);
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title btn-block">
            <?php echo sprintf(lang('child_page_heading'), $child->first_name.' '.$child->last_name); ?>

            <?php if(!is('parent')): ?>
                <a href="#" class="btn btn-warning btn-xs pull-right" data-toggle="modal"
                   data-target="#updateChildModal"><span
                            class="fa fa-pencil-alt"></span>
                </a>
            <?php endif; ?>
        </h4>

    </div>
    <div class="card-body ">
        <?php if(!empty($child->nickname)): ?>
            <div class="row text-danger">
                <div class="col-md-6">
                    <h4>
                        <?php echo lang('nickname'); ?>:
                        <?php echo $child->nickname; ?>
                    </h4>

                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <strong>
                    <?php echo lang('name'); ?></strong>:
                <?php echo $child->first_name.' '.$child->last_name; ?>
            </div>
            <div class="col-md-6">
                <strong>
                    <?php echo lang('date_of_birth'); ?></strong>:
                <?php echo format_date($child->bday, FALSE); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>
                    <?php echo lang('national_id'); ?></strong>:
                <?php echo decrypt($child->national_id); ?>
            </div>
            <div class="col-md-6">
                <strong>
                    <?php echo lang('gender'); ?></strong>:
                <?php echo $child->gender; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>
                    <?php echo lang('blood_type'); ?></strong>:
                <?php echo $child->blood_type; ?>
            </div>
            <div class="col-md-6">
                <strong>
                    <?php echo lang('Ethnicity'); ?></strong>:
                <?php echo $child->ethnicity; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>
                    <?php echo lang('religion'); ?></strong>:
                <?php echo $child->religion; ?>
            </div>
            <div class="col-md-6">
                <strong>
                    <?php echo lang('birthplace'); ?></strong>:
                <?php echo $child->birthplace; ?>
            </div>
        </div>
    </div>
</div>

<?php if(!is('parent')): ?>
    <?php $this->load->view($this->module.'update_child_modal'); ?>
<?php endif; ?>

<div class="my_modal"></div>
