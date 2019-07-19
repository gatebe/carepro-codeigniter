<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?php echo $user->last_name; ?></h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo lang('close'); ?></span>
                </button>
            </div>
            <?php echo form_open_multipart('users/update/' . $user->id); ?>

            <?php echo form_hidden('user_id', $user->id); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('first_name'));
                        echo form_input('first_name', $user->first_name, ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('last_name'));
                        echo form_input('last_name', $user->last_name, ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('email'));
                        echo form_input('email', $user->email, ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('Phone'), 'phone');
                        echo form_input('phone', $address->phone, ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('Alt. Phone'), 'phone2');
                        echo form_input('phone2', $user->phone2, ['class' => 'form-control']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('pin'));
                        echo form_input('pin', $address->zip_code, ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('address line 1'));
                        echo form_input('address_line_1', $address->address_line_1, ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('address line 2'));
                        echo form_input('address_line_2', $address->address_line_2, ['class' => 'form-control']);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('fax'));
                        echo form_input('fax', $address->fax, ['class' => 'form-control']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('city'));
                        echo form_input('city', $address->city, ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('state'));
                        echo form_input('state', $address->state, ['class' => 'form-control', 'required' => '']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('country'));
                        ?>
                        <select id="country" class="form-control" required="" name="country" value="<?php echo $address->country; ?>">
                            <option value="USA">United States</option>
                        </select>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-6">
                        <h3>
                            <?php echo lang('Role'); ?>
                        </h3>
                        <?php
                        foreach ($groups as $group) :
                            $gID = $group['id'];
                            $checked = null;
                            $item = null;
                            $type = 'checkbox';
                            $g_name = $group['name'];
                            foreach ($currentGroups as $grp) {
                                if ($gID == $grp->id) {
                                    $checked = ' checked="checked"';
                                    break;
                                }
                            }
                            if (in_group($this->user->uid(), 'admin') && $group['id'] == user_roles()['admin']) {
                                $type = 'disabled"';
                            } else {
                                $type = 'disabled"';
                            }
                            if (is('admin')) : if($group['id'] != 5):?>
                                <label class="check">
                                    <?php echo $group['name']; ?>
                                    <input type="radio" <?php echo $type; ?> name="groups[]" value="<?php echo $group['id']; ?>" <?php echo $checked; ?>>
                                    <span class="checkmark"></span>
                                </label>
                        <?php endif; elseif (is('manager')) : ?>
                                <?php if ($user->id == $this->user->uid() && $group['id'] == user_roles()['manager']) : ?>
                                    <label class="check">
                                        <?php echo $group['name']; ?>
                                        <input type="radio" <?php echo $type; ?> name="groups[]" value="<?php echo $group['id']; ?>" <?php echo $checked; ?>>
                                        <span class="checkmark"></span>
                                    </label>
                                <?php elseif ($group['id'] == user_roles()['staff'] || $group['id'] == user_roles()['parent']) : ?>
                                    <label class="check">
                                        <?php echo $group['name']; ?>
                                        <input type="radio" <?php echo $type; ?> name="groups[]" value="<?php echo $group['id']; ?>" <?php echo $checked; ?>>
                                        <span class="checkmark"></span>
                                    </label>
                                <?php endif; ?>
                            <?php elseif (is('staff')) : ?>
                                <?php if ($group['id'] == user_roles()['parent']) : ?>
                                    <label class="check">
                                        <?php echo $group['name']; ?>
                                        <input type="radio" <?php echo $type; ?> name="groups[]" value="<?php echo $group['id']; ?>" <?php echo $checked; ?>>
                                        <span class="checkmark"></span>
                                    </label>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-sm-6">
                        <h3 class="text-center"><?php echo lang('Update photo'); ?></h3>
                        <div class="text-center">
                            <?php
                            if (is_file(APPPATH . '../assets/uploads/users/' . $user->photo)) {
                                echo '<img class="img-circle" style="height:100px;width: 200px;"
                                   src="' . base_url() . 'assets/uploads/users/' . $user->photo . '"/>';
                            }
                            ?>
                        </div>
                        <input class="form-control" type="file" name="userfile" size="20" />

                    </div>
                </div>

                <hr />

                <h3><?php echo lang('Change password'); ?></h3>
                <div class="row">
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('new_password'));
                        echo form_password('password', null, ['class' => 'form-control']);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        echo form_label(lang('confirm_password'));
                        echo form_password('password_confirm', null, ['class' => 'form-control']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('close'); ?></button>
                <button class="btn btn-primary"><?php echo lang('update'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>