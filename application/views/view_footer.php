    <script src="<?php echo base_url(); ?>public/js/custom.js"></script>

<script>
  $(function() {
      $(".datepicker").datepicker({
          dateFormat: "yy-mm-dd" // Change this format as needed
      });
  });
</script>
<script>
function search() {
  var input = document.getElementById("Search");
  if (!input) {
    return;
  }

  if (window.AdminTablePagination && window.AdminTablePagination.updateFromInput(input)) {
    return;
  }

  var filter = input.value.toLowerCase();
  var nodes = document.getElementsByClassName('products-row');

  for (i = 0; i < nodes.length; i++) {
    if (nodes[i].innerText.toLowerCase().includes(filter)) {
      nodes[i].style.display = "";
    } else {
      nodes[i].style.display = "none";
    }
  }
}

</script>
    
    <?php
        if($this->session->flashdata('error')) {
            echo '
            <script>
            toastr.error(\''.safe_data($this->session->flashdata('error')).'\');
            </script>
            ';
        }
        if($this->session->flashdata('success')) {
            echo '
            <script>
            toastr.success(\''.safe_data($this->session->flashdata('success')).'\');
            </script>
            ';
        }

        $this->session->set_flashdata('error', null);
        $this->session->set_flashdata('success', null);
    ?>

</body>
</html>
