<div class="modal fade" id="registerChildModal" tabindex="-1" role="dialog" aria-labelledby="registerChildModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="registerChildModalLabel"><?php echo lang('Register child'); ?></h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo form_open('child/register'); ?>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('nickname'));
                        echo form_input('nickname', set_value('nickname'), ['class' => 'form-control','id' => 'nickname']);
                        echo form_label(lang('first_name'),'first_name',['class' => 'required']);
                        echo form_input('first_name', set_value('first_name'), ['class' => 'form-control', 'required' => '','id' => 'first_name']);
                        echo form_label(lang('last_name'),'last_name',['class' => 'required']);
                        echo form_input('last_name', set_value('last_name'), ['class' => 'form-control', 'required' => '', 'id' => 'last_name']);
                        echo form_label(lang('birthday'));
                        echo form_date('bday', set_value('bday', date('Y-m-d')), ['class' => 'form-control']);
                        echo form_label(lang('gender'));
                        echo form_dropdown('gender', ['male' => lang('male'), 'female' => lang('female'), 'other' => lang('other')], set_value('gender'), ['class' => 'form-control']);
                        echo form_label('ID','national_id',['class' => 'required']);
                        echo form_input('national_id', set_value('national_id'), ['class' => 'form-control', 'required' => '', 'id' => 'national_id']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('blood_type'));
                        echo form_dropdown('blood_type', blood_types(), set_value('blood_type'), ['class' => 'form-control',]);
                        echo form_label(lang('status'));
                        echo form_dropdown('status', [1 => lang('active'), 0 => lang('inactive')], set_value('status'), ['class' => 'form-control',]);
                        echo form_label(lang('Ethnicity'));
                        echo form_input('ethnicity', set_value('ethnicity'), ['class' => 'form-control',]);
                        echo form_label(lang('religion'));
                        echo form_input('religion', set_value('religion'), ['class' => 'form-control',]);
                        echo form_label(lang('birthplace'));
                        echo form_input('birthplace', set_value('birthplace'), ['class' => 'form-control',]);
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo lang('close'); ?>
                </button>
                <button class="btn btn-primary"><?php echo lang('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="newUserModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newUserModal"><?php echo lang('Register user'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo form_open('users/create'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-lg-6">
                            <?php
                            echo form_label(lang('first_name'),'first_name',['class' => 'required']);
                            echo form_input('first_name', set_value('first_name'), ['class' => 'form-control', 'required' => '', 'id' => 'first_name']);
                            echo form_label(lang('last_name'),'last_name',['class' => 'required']);
                            echo form_input('last_name', set_value('last_name'), ['class' => 'form-control', 'required' => '', 'id' => 'last_name']);
                            echo form_label(lang('email'),'email',['class' => 'required']);
                            echo form_email('email', set_value('email'), ['class' => 'form-control', 'required' => '', 'id' => 'email']);
                            echo form_label(lang('phone'),'phone',['class' => 'required']);
                            echo form_input('phone', set_value('phone'), ['class' => 'form-control', 'required' => '', 'id' => 'phone']);
                            echo form_label(lang('password'),'password',['class' => 'required']);
                            echo form_password('password', '', ['class' => 'form-control', 'required' => '','id' => 'password']);
                            echo form_label(lang('password_confirm'),'password_confirm',['class' => 'required']);
                            echo form_password('password_confirm', '', ['class' => 'form-control', 'required' => '','id' => 'password_confirm']);
                            ?>
                        </div>

                        <div class="col-lg-6">
                            <?php echo form_label(lang('roles')); ?>
                            <?php foreach ($this->db->get('groups')->result() as $group) : ?>
                                <?php if (is('admin')) : if($group->id != 5):?>
                                    <label class="check"><?php echo lang($group->name); ?>
                                        <?php echo form_radio('group', $group->id, set_radio('group', $group->id, true)); ?>
                                        <span class="checkmark"></span>
                                    </label>
                                    <?php endif; elseif (is('manager')) : ?>
                                    <?php if ($group->id == user_roles()['staff'] || $group->id == user_roles()['parent']) :
                                        if ($group->id == user_roles()['staff']) {
                                            $check = true;
                                        } else {
                                            $check = false;
                                        }
                                        ?>
                                        <label class="check"><?php echo lang($group->name); ?>
                                            <?php echo form_radio('group', $group->id, set_radio('group', $group->id, $check)); ?>
                                            <span class="checkmark"></span>
                                        </label>
                                    <?php endif; ?>
                                <?php elseif(is('staff')): ?>
                                    <?php if ($group->id == user_roles()['parent']):?>
                                    <label class="check"><?php echo lang($group->name); ?>
                                            <?php echo form_radio('group', $group->id, set_radio('group', $group->id,TRUE)); ?>
                                            <span class="checkmark"></span>
                                        </label>                                    
                                    <?php endif; ?>                                     
                                <?php endif; ?>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary"><?php echo lang('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<div class="modal fade" id="AssignRoomModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?php echo lang('Assign') . ' ' . lang('Rooms'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo form_open('child/doassignroom'); ?>
            <div class="modal-body">
                <input type="hidden" value="" name="child_id" id="child_id">
                <span class="field_required">*</span>
                <select class="form-control selectpicker rooms" data-live-search="true" multiple name="room[]" id="assign_room" require>
                </select>
                <br />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('Close'); ?></button>
                <button class="btn btn-primary"><?php echo lang('Assign'); ?></button>
            </div>
            <?php echo form_close(); ?>

        </div>
    </div>
</div>