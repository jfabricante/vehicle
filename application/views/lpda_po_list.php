  <select class="form-control selectpicker" id = "lpda_no" name="lpda_no" data-live-search="true" required>
      <option value="1" <?php echo ($lpda_no == 1)? 'selected':'';?>>Nothing Selected</option>
      <?php
      foreach($lpda_list as $key){
          ?>
          <option 
          data-subtext="- <?php echo $key->LPDA_NO;?>" 
          data-content="<span class='label label-success'><?php echo $key->LPDA_NO;?></span>  <span class='label label-danger'><?php echo $key->VENDOR_NAME;?></span> <span class='label label-primary'><?php echo $key->STR_MONTH_CREATED;?></span>" 
          value="<?php echo $key->LPDA_NO; ?>" >
          </option>
          <?php 
      }
      ?>
  </select>

                    