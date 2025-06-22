<?php
session_start();
include('connection.php');
?>
<?php
include('adminsessionChecker.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
<?php include('./header.php')?>
<?php include('./style.php')?>
</head>

<body>
  

    <!-- Offcanvas Menu Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="offcanvas__close">+</div>
        
        <div class="offcanvas__logo">
            <a href="./index.html"><img src="img/logo.png" alt=""></a>
        </div>
        <div id="mobile-menu-wrap"></div>
        
    </div>
    <!-- Offcanvas Menu End -->
    <div>
    <?php include('./navbar.php'); ?>
</div>


<div class="search_section">
  <h3>Enquired List</h3><br>
  <input type="text" id="name_search" name="q" placeholder="Enter your search ....." pattern="[A-Za-z]+" title="Only alphabet characters are allowed" aria-label="Search Enquiry">
  <button onclick="search()" class="btn" aria-label="Search Button">Search</button>
  <button onclick="clearSearch()" class="btn" aria-label="Clear Search">Clear</button>

</div>

<div class="wholetbl">
  <div class="container">
    <div class="header">
      <h2>Enquire Management</h2>
      <div class="buttons">
        <button class="selectall" onclick="selectAll()">Select All</button>
        <button class="delete" onclick="deleteSelected()">Delete</button>
      </div>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
        <tr>
        <th style="width: 50px;">Sno</th>
        <th style="width: 150px;">Name</th>
        <th style="width: 200px;">Email</th>
        <th style="width: 150px;">Subject</th>
        <th style="width: 300px;">Message</th>
        <th style="width: 150px;">Time</th>
        <th style="width: 50px;">Select</th>
    </tr>
        </thead>
        <tbody>
          <?php 
          $result = $conn->query("SELECT id, name, email,subject, message, submitted_at FROM contacts ORDER BY id DESC");

          if ($result && $result->num_rows > 0) {
              $n = 1;
              while ($row = $result->fetch_assoc()) {
                  echo '<tr data-id="' . htmlspecialchars($row['id']) . '">
                      <td data-label="Sno">' . $n . '</td>
                      <td data-label="Name">' . htmlspecialchars($row['name']) . '</td>
                      <td data-label="Email">' . htmlspecialchars($row['email']) . '</td>
                      <td data-label="Subject">' . htmlspecialchars($row['subject']) . '</td>
                      <td data-label="Message">' . htmlspecialchars($row['message']) . '</td>
                      <td data-label="Time">' . htmlspecialchars($row['submitted_at']) . '</td>
                      <td data-label="Select">
                          <input type="checkbox" name="record[]" value="' . htmlspecialchars($row['id']) . '">
                      </td>
                  </tr>';
                  $n++;
              }
          } else {
              echo '<tr><td colspan="9">No results found</td></tr>';
          }

          $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>

    function search() {
        const searchInput = document.getElementById('name_search').value.toLowerCase();
        const rows = document.querySelectorAll('.table-wrapper tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let cell of cells) {
                if (cell.textContent.toLowerCase().includes(searchInput)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        });

        document.getElementById('name_search').value = '';
    }

    function clearSearch() {
            document.getElementById('name_search').value = '';
            search();  // Trigger the search to show all records
        }

    function deleteSelected() {
    var selectedIds = [];
    var checkboxes = document.getElementsByName('record[]');
    
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            selectedIds.push(checkbox.value);
        }
    });

    if (selectedIds.length > 0) {
        if (confirm("Are you sure you want to delete the selected records?")) {
            fetch('delete_enquirys.php?deleteid=' + selectedIds.join(','), {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload();
             }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while trying to delete records.');
            });
        }
    } else {
        alert("Please select at least one record to delete.");
    }
}


    function selectAll() {
        var checkboxes = document.getElementsByName('record[]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = true;
        });
    }
</script>



<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch">+</div>
        <form class="search-model-form">
            <input type="text" id="search-input" placeholder="Search here.....">
        </form>
    </div>
</div>
<!-- Search End -->

<!-- Js Plugins -->
<script src="../js/jquery-3.3.1.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.magnific-popup.min.js"></script>
<script src="../js/jquery-ui.min.js"></script>
<script src="../js/mixitup.min.js"></script>
<script src="../js/jquery.countdown.min.js"></script>
<script src="../js/jquery.slicknav.js"></script>
<script src="../js/owl.carousel.min.js"></script>
<script src="../js/jquery.nicescroll.min.js"></script>
<script src="../js/main.js"></script>
</body>

</html>