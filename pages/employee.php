<div class="col-md-12">
    <h4>List of Research's</h4>
    <hr style="border-bottom:1px solid black"></hr>
</div>

<div class="col-lg-10">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php 
                // Assign button classes based on the value of 'io' in the query parameters
                $btn_class1 = ($_GET['io'] == '1') ? 'class="btn btn-md btn-success"' : 'class="btn btn-md btn-default"';
                $btn_class2 = ($_GET['io'] == '2') ? 'class="btn btn-md btn-success"' : 'class="btn btn-md btn-default"';
            ?>
            <a href="index.php?page=employee&io=1" <?php echo $btn_class1; ?>> Active</a>
            <a href="index.php?page=employee&io=2" <?php echo $btn_class2; ?>> Inactive</a>
        </div> 

        <div class="panel-body"> 
            <table id="emp" class="table table-bordered table-condensed">
                <thead>
                    <tr id="heads">
                        <th class="col-md-1 text-center"></th>
                        <th class="col-md-1 text-center">E-code</th>
                        <th class="col-md-3 text-center">Name</th>
                        <th class="col-md-2 text-center">Position</th>
                        <th class="col-md-1 text-center"></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                include '../includes/db.php';
                // Fetching employees from the database based on the 'io' value in the query parameters
                $query = mysqli_query($conn, "SELECT *, CONCAT(lastname, ', ', firstname, ' ', midname) as name 
                                              FROM employee 
                                              NATURAL JOIN position 
                                              WHERE io = '".$_GET['io']."' 
                                              AND eid != 1 
                                              ORDER BY name");

                while($row = mysqli_fetch_assoc($query)) {
                    $id = $row['eid'];
                    $eco = date("Y", strtotime($row['date_added'])) . $row['ecode'];
                ?>
                    <tr>
                        <td class="text-center"><img src='../images/<?php echo $row["e_pic"]; ?>' width='50px' height='60px'></td>
                        <td class="text-center"><?php echo $eco; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td class="text-center"><?php echo $row['position']; ?></td>
                        <td class="text-center"><center><a href="index.php?page=employee_profile&id=<?php echo $id; ?>"><i class="fa fa-eye"></i> Profile</a></center></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-md-2">
    <a class="col-sm-12 btn btn-md btn-info" href="#new_employee" data-toggle="modal"><center><i class="fa fa-plus"></i> New Researcher</center></a>
</div>

<div id="retCode1"></div>

<?php include '../includes/add_modal.php'; ?>

<script>
    jQuery(document).ready(function() {
        $('#suc_msg').hide();
        $('#err_msg').hide();

        jQuery("#pos_form").submit(function(e) {
            e.preventDefault();
            var formData = jQuery(this).serialize();
            $.ajax({
                type: "POST",
                url: "../forms/add_forms.php?action=position",
                data: formData,
                success: function(html) {
                    $('#retCode1').html(html);
                    var delay = 2000;
                    setTimeout(function() { window.location = 'index.php?page=position'; }, delay);
                }
            });
            return false;
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $("#emp").dataTable({
            "aaSorting": [[2, "asc"]]
        });
    });
</script>

<script>
    jQuery(document).ready(function() {
        jQuery("#emp_form").submit(function(e) {
            e.preventDefault();
            var formData = jQuery(this).serialize();
            $.ajax({
                type: "POST",
                url: "../forms/add_forms.php?action=employee",
                data: formData,
                success: function(html) {
                    $('#retCode2').append(html);
                }
            });
            return false;
        });
    });
</script>
