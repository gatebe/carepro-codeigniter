<div class="card">
    <div class="card-header">
        <h4 class="card-title"
        ><?php echo lang('healthcare_providers'); ?>
            <button data-toggle="modal" data-target="#newProvider" class="btn btn-success btn-sm card-tools">
                <i class="fa fa-plus-circle"></i> <?php echo lang('Add new'); ?>
            </button>
        </h4>
    </div>
    <div class="card-body">

        <table class="table ">
            <th><?php echo lang('name'); ?></th>
            <th><?php echo lang('type_role'); ?></th>
            <th><?php echo lang('phone'); ?></th>
            <th><?php echo lang('address'); ?></th>
            <th><?php echo lang('notes'); ?></th>
            <th></th>
            <?php $query = $this->db->where('child_id', $child->id)->get('child_providers');
            foreach ($query->result() as $row): ?>
                <tr>
                    <td><?php echo $row->provider_name; ?></td>
                    <td><?php echo $row->type_role; ?></td>
                    <td><?php echo $row->phone; ?></td>
                    <td><?php echo $row->address; ?></td>
                    <td><?php echo $row->notes; ?></td>
                    <td>
                        <?php if(!is('parent')): ?>
                            <a class="delete" href="<?php echo site_url('child/deleteProvider/'.$row->id); ?>">
                                <i class="fa fa-trash-alt"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>
</div>

<div class="modal fade" id="newProvider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?php echo lang('new_provider'); ?></h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php echo lang('close'); ?></span>
                </button>
            </div>
            <?php echo form_open('child/addProvider'); ?>
            <div class="modal-body">
                <?php echo form_hidden('child_id', $child->id); ?>
                <span class="field_required">*</span>
                <input type="text" name="name" class="form-control" required placeholder="<?php echo lang('name'); ?>"/>                
                <br/>
                <span class="field_required">*</span>
                <input type="text" name="type_role" class="form-control" required
                       placeholder="<?php echo lang('type_role_help'); ?>"/>
                <br/>
                <span class="field_required">*</span>
                <input type="text" name="phone" class="form-control" required
                       placeholder="<?php echo lang('phone'); ?>"/>
                <br/>
                <span class="field_required">*</span>
                <input type="text" name="address" class="form-control" required
                       placeholder="<?php echo lang('address'); ?>"/>
                <br/>
                <textarea placeholder="<?php echo lang('notes'); ?>" class="form-control" name="notes"
                          rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo lang('close'); ?>
                </button>
                <button class="btn btn-primary">
                    <?php echo lang('submit'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>