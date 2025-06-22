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
<style>
    .fmodal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 70%;
            max-width:380px;
        }
        
        .fmodal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9;
        }
        .fmodal-content select,label{
            margin-bottom: 10px;
        } 
        .fmodal-content select{
            width:100%;
        }
        .fclose {
            
            float: right;
            font-size: 34px;
            cursor: pointer;
        }
        
    .fbtn {
        background-color: #F76100  ;
        border: none;
        color: white;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;        
        font-size: 14px;
        transition: background-color 0.3s ease;
    }
    .fbtn:hover {
        background-color: #F76100 ;
        color: white;
    }
        
        .search_section {
            margin-bottom: 20px;
        }
</style>
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


<div class="search_section" >
  <h3>Sizes List</h3><br>
  <input type="text" id="name_search" name="q" placeholder="Enter your search ....." pattern="[A-Za-z]+" title="Only alphabet characters are allowed" aria-label="Search Enquiry">
  <button onclick="search()" class="btn" aria-label="Search Button">Search</button>
  <button onclick="clearSearch()" class="btn" aria-label="Clear Search">Clear</button>

</div>

<div class="wholetbl">
  <div class="container">
    <div class="header">
      <h2>Size Management</h2>
      <div class="buttons">
      <button id="addNewBtn" class="selectall">Add New</button>
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
        <th style="width: 50px;">Select</th>
    </tr>
        </thead>
        <tbody>
        <?php 
$result = $conn->query("SELECT * FROM sizes ORDER BY size_id DESC");

if ($result && $result->num_rows > 0) {
    $n = 1;
    while ($row = $result->fetch_assoc()) {
        $size = htmlspecialchars($row['size_name']);
        
        // Convert size abbreviations to full names
        $size_display = match ($size) {
            'Small' => 'Small',
            'Medium' => 'Medium',
            'Large' => 'Large',
            'Extra Large' => 'Extra Large',
            'Double Extra Large' => 'Double Extra Large',
            'Triple Extra Large' => 'Triple Extra Large',
            default => $size // If it's not predefined, display as is
        };

        echo '<tr data-id="' . htmlspecialchars($row['size_id']) . '">
            <td data-label="Sno">' . $n . '</td>
            <td data-label="Name">' . $size_display . '</td>
            <td data-label="Select">
                <input type="checkbox" name="record[]" value="' . htmlspecialchars($row['size_id']) . '">
            </td>
        </tr>';
        $n++;
    }
} else {
    echo '<tr><td colspan="3">No results found</td></tr>';
}

$conn->close();
?>

        </tbody>
      </table>
    </div>
  </div>
</div>


<!-- Add New Fabric Modal -->
<div id="addNewModal" class="fmodal">
    <div class="fmodal-content">
        <span class="fclose">&times;</span>
        <h3 style="color: #F76100 ;">Add Size</h3>
        <form id="addFabricForm" action="add_size.php" method="post">
            <select name="size" id="fabric_name" required><br>
            <option value="Small">Small</option>
    <option value="Medium">Medium</option>
    <option value="Large">Large</option>
    <option value="Extra Large">Extra Large</option>
    <option value="Double Extra Large">Double Extra Large</option>
    <option value="Triple Extra Large">Triple Extra Large</option>
            </select>          
              <button type="submit" class="fbtn">Submit</button>
        </form>
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
            fetch('delete_sizes.php?deleteid=' + selectedIds.join(','), {
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

    // Get modal element
var modal = document.getElementById("addNewModal");

// Get open modal button
var btn = document.getElementById("addNewBtn");

// Get close button
var span = document.getElementsByClassName("fclose")[0];

// Listen for open click
btn.onclick = function() {
    modal.style.display = "block";
}

// Listen for close click
span.onclick = function() {
    modal.style.display = "none";
    location.reload();

}

// Listen for outside click
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
        location.reload();

    }
}

function handleFormSubmit(event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(event.target);

    fetch('add_size.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Display the message from the response

        if (data.status === 'success') {
            document.getElementById('addFabricForm').reset(); // Reset form after successful submission
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred.');
    });
}

// Attach the form submit handler
document.getElementById('addFabricForm').addEventListener('submit', handleFormSubmit);

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