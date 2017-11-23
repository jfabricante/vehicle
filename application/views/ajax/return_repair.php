<div class="modal-content">
    <div class="box box-primary" style="margin-top: 20%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Details</h4>
        </div>
        <div class="box box-primary">
            <form id="myForm" role="form" class="form-horizontal" method="POST" action="<?php echo base_url(); ?>buyoff/return_repair" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="cs_no" value="<?php echo $cs_no; ?>">
                        <div class="col-xs-12">
                          <label>Action Taken</label>
                          <textarea class="form-control" rows="3" placeholder="Action Taken" name="action_taken"></textarea>
                        </div>
                    </div> 
                </div> 
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>