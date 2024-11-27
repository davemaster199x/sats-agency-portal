<div class="box-typical box-typical-padding">

    <h5 class="m-t-lg with-border">Search Results for [<?php echo $search_str; ?>]</h3>
    
    <header class="box-typical-header">
            <div class="box-typical box-typical-padding">
                <form action="<?= base_url('/sys/search') ?>" method="POST" id="search_form">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="autoComplete_wrapper">
                                <label for="autoComplete"></label>
                                <?php if($this->config->item('theme') === "sas"): ?>
                                    <input id="autoComplete" type="search" name="search"/>
                                <?php else: ?>
                                    <input name="search" type="text" id="search" class="form-control" placeholder="Search Property">
                                <?php endif; ?>
                            </div>
                            <div class="input-group-append">
                                <label class="form-control-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-inline">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
    </header>
    
    <div class="table-responsive">
        
        <?php if(!empty($search_res)): ?>
        <table class="table table-hover main-table">
            <thead>
                <tr>
                    <th>Address</th>
                    <th>Property Manager</th>
                    <th>Service Type</th>
                </tr>
            </thead>
    
             <tbody>
             <?php foreach($search_res as $row): ?>
                <tr>
                    <td><?php echo "<a href='/properties/property_detail/".$row['property_id']."'>". $row['address_1']." ".$row['address_2']." ".$row['address_3']." ".$row['state']." ".$row['postcode']. "</a>" ?> </td>
                    <td><?php echo $row['fname']." ".$row['lname'] ?></td>
                    <td>
                        <?php $tetes =  $row['property_services']; ?>
                        <?php if(!empty($tetes)): ?>
                            <?php foreach($tetes as $prop_service): ?>
                                <?= Alarm_job_type_model::icons($prop_service->ajt_id); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php echo "-----" ?>
                        <?php endif; ?>
                        
                    </td>
                 </tr>
    
             <?php endforeach; ?>
             </tbody>
        </table>
        
        <?php else: ?>
            <div>No results found matching your query: <?php echo [$search_str]; ?></div>
        <?php endif; ?>
    </div>
</div>