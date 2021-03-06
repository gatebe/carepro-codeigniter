<div class="modal fade" id="newNoteModal" tabindex="-1" role="dialog" aria-labelledby="newNoteLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newNoteLabel"><?php echo lang('new_note'); ?></h4>

                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span  class="sr-only"><?php echo lang('close'); ?></span>
                </button>
            </div>
            <?php echo form_open('notes/store'); ?>
            <?php echo form_hidden('child_id', $child->id); ?>
            <div class="modal-body">
                <?php

                echo form_label(lang('Title'), 'title', ['class' => 'required']);
                echo form_input('title', set_value('title'), ['class' => 'form-control', 'required' => '', 'id' => 'title']);

                echo form_label(lang('Category'));

                echo '<div class="clearfix">';
                foreach ($this->db->get('notes_categories')->result() as $cat) {
                    echo '<label class="options">';
                    echo '<span>'.lang($cat->name).'</span>';
                    echo form_radio('category_id', lang($cat->id), $cat->id == 1 ? TRUE : FALSE);
                    echo '<span class="radio"></span>';
                    echo '</label>';
                }
                echo '</div>';

                echo form_label(lang('Note tags'));
                echo '<div class="clearfix">';
                foreach ($this->db->get('notes_tags')->result() as $tag) {
                    echo '<label class="options">';
                    echo '<span>'.lang($tag->name).'</span>';
                    echo form_checkbox('tags[]', lang($tag->name), $tag->id == 1 ? TRUE : FALSE);
                    echo '<span class="checkbox"></span>';
                    echo '</label>';
                }
                echo '</div>';
                echo '<div class="clearfix"></div>';

                echo form_label(lang('Notes'), 'note-content', ['class' => 'required']);
                echo form_textarea('note-content', htmlspecialchars_decode(set_value('note-content')), ['class' => 'form-control editor', 'id' => 'note-content']);

                ?>
            </div>
            <div class="modal-footer">
                <?php

                echo form_button(
                    [
                        'type' => 'submit',
                        'class' => 'btn btn-primary'
                    ], lang('submit'));
                echo form_button(
                    [
                        'data-dismiss' => 'modal',
                        'class' => 'btn btn-default'
                    ], lang('close'));
                ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>