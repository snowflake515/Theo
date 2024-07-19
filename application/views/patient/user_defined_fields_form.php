<div class="page-header">
  <h1>
    Patient Detail
  </h1>
</div>

<ul id="myTab" class="nav nav-tabs">
  <li class=""><?php echo anchor("patient/demographics/$patient_id", "Patient Information", 'id="demographics" class="tab_patients"'); ?></li>
  <li class=""><?php echo anchor("patient/office_information/$patient_id", "Office Information", 'id="office_information" class="tab_patients"'); ?> </li>
  <li class=""><?php echo anchor("patient/responsible_party/$patient_id", "Responsible Party", 'id="responsible_party" class="tab_patients"'); ?></a></li>
  <li class="active"><?php echo anchor("patient/user_defined_fields/$patient_id", "User Defined Fields", 'id="user_defined_fields" class="tab_patients"'); ?></a></li>
</ul>
<br/>
<form class="form-horizontal" role="form"  id="patient_form" action="<?php echo site_url("patient/save_user_defined_field"); ?>" method="POST" accept-charset="UTF-8">

  <input type="hidden" name="Patient_ID" id="Patient_ID" value="<?php echo $patient_id; ?>">
  <input type="hidden" name="UDFValues_Id" id="PPUDF_Id" value="<?php echo form_value('UDFValues_Id', $dt_udf_v) ?>">
  <input type="hidden" name="form_active" id="form_active" value="user_defined_fields">
  <input type="hidden" name="next_form" id="form_active" value="">

  

      <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">User Defined Fields Information</div>
        <div class="panel-body">

          <div class="row">
            <div class="col-md-6">

              <?php
              if($dt && form_value('User_Text1_Desc', $dt) != ""){
                $val = form_value('User_Text1_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text1_Value" class="col-sm-3 control-label">'.$dt->User_Text1_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text1_Value" id="User_Text1_Value"   value="'.$val.'">
                     '.form_error('User_Text1_Value').'  
                   </div>
                 </div>'; 
                 
              }
              
              if($dt && form_value('User_Text2_Desc', $dt) != ""){
                $val = form_value('User_Text2_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text2_Value" class="col-sm-3 control-label">'.$dt->User_Text2_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text2_Value" id="User_Text2_Value"   value="'.$val.'">
                     '.form_error('User_Text2_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Text3_Desc', $dt) != ""){
                $val = form_value('User_Text3_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text3_Value" class="col-sm-3 control-label">'.$dt->User_Text3_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text3_Value" id="User_Text3_Value"   value="'.$val.'">
                     '.form_error('User_Text3_Value').'    
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Text4_Desc', $dt) != ""){
                $val = form_value('User_Text4_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text4_Value" class="col-sm-3 control-label">'.$dt->User_Text4_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text4_Value" id="User_Text4_Value"   value="'.$val.'">
                     '.form_error('User_Text4_Value').'      
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Text5_Desc', $dt) != ""){
                $val = form_value('User_Text5_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text5_Value" class="col-sm-3 control-label">'.$dt->User_Text5_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text5_Value" id="User_Text5_Value"   value="'.$val.'">
                     '.form_error('User_Text5_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Text6_Desc', $dt) != ""){
                $val = form_value('User_Text6_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text6_Value" class="col-sm-3 control-label">'.$dt->User_Text6_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text6_Value" id="User_Text6_Value"   value="'.$val.'">
                     '.form_error('User_Text6_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Text7_Desc', $dt) != ""){
                $val = form_value('User_Text7_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text7_Value" class="col-sm-3 control-label">'.$dt->User_Text7_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text7_Value" id="User_Text7_Value"   value="'.$val.'">
                     '.form_error('User_Text7_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Text8_Desc', $dt) != ""){
                $val = form_value('User_Text8_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text8_Value" class="col-sm-3 control-label">'.$dt->User_Text8_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text8_Value" id="User_Text8_Value"   value="'.$val.'">
                     '.form_error('User_Text8_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Text9_Desc', $dt) != ""){
                $val = form_value('User_Text9_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text9_Value" class="col-sm-3 control-label">'.$dt->User_Text9_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text9_Value" id="User_Text9_Value"   value="'.$val.'">
                     '.form_error('User_Text9_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Text10_Desc', $dt) != ""){
                $val = form_value('User_Text10_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Text10_Value" class="col-sm-3 control-label">'.$dt->User_Text10_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control" name="User_Text10_Value" id="User_Text10_Value"   value="'.$val.'">
                     '.form_error('User_Text10_Value').'  
                   </div>
                 </div>'; 
              }
              ?>
              
              

            </div>

            <div class="col-md-6">
              <?php
              if($dt && form_value('User_Date1_Desc', $dt) != ""){
                $val = form_value('User_Date1_Value', $dt_udf_v);
                $val = convert_date_form($val,'User_Date1_Value',$dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Date1_Value" class="col-sm-3 control-label">'.$dt->User_Date1_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control date-form" placeholder="mm/dd/yyyy" name="User_Date1_Value" id="User_Date1_Value"   value="'.$val.'">
                     '.form_error('User_Date1_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Date2_Desc', $dt) != ""){
                $val = form_value('User_Date2_Value', $dt_udf_v);
                $val = convert_date_form($val,'User_Date2_Value',$dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Date2_Value" class="col-sm-3 control-label">'.$dt->User_Date2_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control date-form" placeholder="mm/dd/yyyy" name="User_Date2_Value" id="User_Date2_Value"   value="'.$val.'">
                     '.form_error('User_Date2_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Date3_Desc', $dt) != ""){
                $val = form_value('User_Date3_Value', $dt_udf_v);
                $val = convert_date_form($val,'User_Date3_Value',$dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Date3_Value" class="col-sm-3 control-label">'.$dt->User_Date3_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control date-form" placeholder="mm/dd/yyyy" name="User_Date3_Value" id="User_Date3_Value"   value="'.$val.'">
                     '.form_error('User_Date3_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Date4_Desc', $dt) != ""){
                $val = form_value('User_Date4_Value', $dt_udf_v);
                $val = convert_date_form($val,'User_Date4_Value',$dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Date4_Value" class="col-sm-3 control-label">'.$dt->User_Date4_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control date-form" placeholder="mm/dd/yyyy" name="User_Date4_Value" id="User_Date4_Value"   value="'.$val.'">
                     '.form_error('User_Date4_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Date5_Desc', $dt) != ""){
                $val = form_value('User_Date5_Value', $dt_udf_v);
                $val = convert_date_form($val,'User_Date5_Value',$dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Date5_Value" class="col-sm-3 control-label">'.$dt->User_Date5_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="text" class="form-control date-form" placeholder="mm/dd/yyyy" name="User_Date5_Value" id="User_Date5_Value"   value="'.$val.'">
                     '.form_error('User_Date5_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Real1_Desc', $dt) != ""){
                $val = form_value('User_Real1_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Real1_Value" class="col-sm-3 control-label">'.$dt->User_Real1_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="number" class="form-control" name="User_Real1_Value" id="User_Real1_Value"   value="'.$val.'">
                     '.form_error('User_Real1_Value').'
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Real2_Desc', $dt) != ""){
                $val = form_value('User_Real2_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Real2_Value" class="col-sm-3 control-label">'.$dt->User_Real2_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="number" class="form-control" name="User_Real2_Value" id="User_Real2_Value"   value="'.$val.'">
                     '.form_error('User_Real2_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Real3_Desc', $dt) != ""){
                $val = form_value('User_Real3_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Real3_Value" class="col-sm-3 control-label">'.$dt->User_Real3_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="number" class="form-control" name="User_Real3_Value" id="User_Real3_Value"   value="'.$val.'">
                     '.form_error('User_Real3_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Real4_Desc', $dt) != ""){
                $val = form_value('User_Real4_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Real4_Value" class="col-sm-3 control-label">'.$dt->User_Real4_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="number" class="form-control" name="User_Real4_Value" id="User_Real4_Value"   value="'.$val.'">
                     '.form_error('User_Real4_Value').'  
                   </div>
                 </div>'; 
              }
              
              if($dt && form_value('User_Real5_Desc', $dt) != ""){
                $val = form_value('User_Real5_Value', $dt_udf_v);
                  echo '<div class="form-group">
                   <label for="User_Real5_Value" class="col-sm-3 control-label">'.$dt->User_Real5_Desc.'</label>
                   <div class="col-sm-9">
                     <input type="number" class="form-control" name="User_Real5_Value" id="User_Real5_Value"   value="'.$val.'">
                     '.form_error('User_Real5_Value').'  
                   </div>
                 </div>'; 
              }
              ?>
              
            </div>
          </div>
        </div>
      </div>
    


  <button type="submit" class="btn btn-primary" ><i class="icon-save"></i>&nbsp;&nbsp; Save</button>


</form>
