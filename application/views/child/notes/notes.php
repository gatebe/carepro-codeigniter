<?php $this->load->view('child/nav'); ?>
<div class="row">
    <div class="col-sm-2 col-lg-2 col-md-2 ">
        <?php $this->load->view('child/sidebar'); ?>
    </div>
    <div class="col-sm-10 col-lg-10 col-md-10">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="nav-item">
                <a class="nav-link active show" href="#notes" aria-controls="home" role="tab" data-toggle="tab">
                    <i class="fa fa-clipboard"></i>
                    <span class="hidden-sm-up"><?php echo lang('notes'); ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#incidents" aria-controls="incidents" role="tab" data-toggle="tab">
                    <i class="fa fa-exclamation-triangle text-warning"></i>
                    <span class="hidden-sm-up"><?php echo lang('incident_reports'); ?></span>
                </a>
            </li>
            <?php if(!is('parent')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="#note-categories" aria-controls="note-categories" role="tab"
                       data-toggle="tab">
                        <i class="fa fa-th"></i>
                        <span class="hidden-sm-up"><?php echo lang('Notes categories'); ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if(isset($_GET['viewNote']) || isset($_GET['viewIncident'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="#view-notes" aria-controls="view-notes" role="tab" data-toggle="tab">
                        <i class="fa fa-folder-open"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active show" id="notes">
                <br/>
                <div class="card">
                    <div class="card-header">
                    <?php if(is(['admin', 'manager', 'staff'])): ?>
                        <button type="button" class="btn btn-primary btn-flat btn-sm" data-toggle="modal"
                                data-target="#newNoteModal">
                            <i class="fa fa-plus-circle"></i>
                            <span class="hidden-sm-up"><?php echo lang('new_note'); ?> </span>
                        </button>
                    <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php foreach ($child->notes as $note): ?>
                            <div class="card">
                                <div class="card-header with-border">
                                    <h4 class="card-title">
                                        <a style="color: #1974cc;"
                                           id="<?php echo $note->id; ?>"
                                           class="viewNote"
                                           href="#">
                                            <?php echo $note->title; ?>
                                        </a>
                                    </h4>

                                    <?php if(!is('parent')): ?>
                                        <a class="pull-right delete "
                                           href="<?php echo site_url('notes/destroy/'.$note->id); ?>">
                                            <i class="fa fa-trash-alt text-danger"></i></a>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php echo word_limiter($this->conf->stripImage(htmlspecialchars_decode($note->content))); ?>
                                </div>
                                <div class="card-footer">
                                    <?php
                                    echo format_date($note->created_at)
                                        .' '
                                        .lang('by')
                                        .' '
                                        .$note->user_name;
                                    ?>
                                    |
                                    <strong><?php echo lang('Category'); ?>:</strong>
                                    <?php echo $note->category; ?>
                                    |
                                    <strong><?php echo lang('Tags'); ?></strong>
                                    <?php echo $note->tags; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="incidents">
                <br/>
                <div class="card">
                    <div class="card-header">

                        <?php if(is(['admin', 'manager', 'staff'])): ?>
                            <button type="button" class="btn btn-warning btn-flat btn-sm" data-toggle="modal"
                                    data-target="#newIncidentModal">
                                <i class="fa fa-plus-circle"></i>
                                <span class="hidden-sm-up"><?php echo lang('new incident'); ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php foreach ($child->incidents as $incident): ?>
                            <div class="card">
                                <div class="card-header with-border">
                                    <h4 class="card-title">
                                        <a style="color: #1974cc;"
                                           href="?viewIncident=<?php echo $incident->id; ?>#view-notes"
                                           class="text-info"> <?php echo $incident->title; ?></a>
                                    </h4>
                                    <?php if(!is('parent')): ?>
                                        <a class="pull-right delete "
                                           href="<?php echo site_url('notes/deleteIncident/'.$incident->id); ?>">
                                            <i class="fa fa-trash-alt text-danger"></i></a>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php echo word_limiter($incident->description); ?>
                                </div>
                                <div class="card-footer">
                                    <?php echo format_date($incident->date_occurred); ?>
                                    <?php echo lang('by'); ?>
                                    <?php echo $incident->user_name; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="note-categories">
                <br/>
                <?php
                if(is(['manager', 'admin', 'staff']))
                    $this->load->view($this->module.'categories');
                ?>
            </div>
            <?php if(!is('parent')): ?>
            <?php endif; ?>
            <?php if(isset($_GET['viewNote']) || isset($_GET['viewIncident'])): ?>
                <div role="tabpanel" class="tab-pane" id="view-notes">
                    <br/>
                    <?php if(isset($_GET['viewNote'])) {

                    }
                    if(isset($_GET['viewIncident'])) {
                        $this->load->view('child/notes/view-incident');
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php $this->load->view('child/notes/create-note-modal'); ?>
<?php $this->load->view($this->module.'create-incident-modal'); ?>
<?php $this->load->view('child/notes/view-note-modal'); ?>
<script>

    $('.viewNote').click(function () {
        var modal = $('#noteViewModal');
        var note_id = $(this).attr('id');
        $.ajax({
            url: site_url + 'notes/view',
            data: {note_id: note_id},
            type: 'POST',
            success: function (response) {
                res = JSON.parse(response);
                console.log(res);
                modal.find('.modal-title').html(res.title);
                modal.find('.note-content').html(decodeHtml(res.content));
                modal.find('.note-user').html(res.user);
                modal.find('.note-date').html(res.created_at);
                modal.find('.note-cat').html(res.category);
                modal.find('.note-tags').html(res.tags);
                modal.modal('show')
            },
            error: function (error) {
                console.log(error);
            }
        });
    })
</script>