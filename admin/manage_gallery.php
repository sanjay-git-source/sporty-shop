<?php
session_start();
include('connection.php');
include('adminsessionChecker.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./header.php'); ?>
    <?php include('./style.php'); ?>
    <style>
        .gallery-modal {
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
            max-width: 400px;
        }
        .gallery-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9;
        }
        .modal-content{
            border: none;
        }
        .modal-content input, label {
            margin-bottom: 10px;
        } 
        .modal-content input {
            width: 100%;
        }
        .close {
            float: right;
            font-size: 34px;
            cursor: pointer;
        }
        .btn {
            background-color: #FF7F50;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;        
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #F76100;
        }
        .search_section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include('./navbar.php'); ?>

    <div class="search_section">
        <h3>Gallery List</h3><br>
        <input type="text" id="image_search" placeholder="Search images...">
        <button onclick="searchImages()" class="btn">Search</button>
        <button onclick="clearSearch()" class="btn">Clear</button>
    </div>

    <div class="gallery-container">
        <div class="container">
            <div class="header">
                <h2>Gallery Management</h2>
                <div class="buttons">
                    <button id="addNewBtn" class="btn">Add New</button>
                    <button class="btn" onclick="selectAll()">Select All</button>
                    <button class="btn" onclick="deleteSelected()">Delete</button>
                </div>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Sno</th>
                            <th>Image</th>
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $result = $conn->query("SELECT * FROM gallery ORDER BY image_id DESC");
                        if ($result && $result->num_rows > 0) {
                            $n = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr data-id="' . htmlspecialchars($row['image_id']) . '">
                                    <td>' . $n . '</td>
                                    <td><img src="' . htmlspecialchars($row['image_path']) . '" width="100" height="100"></td>
                                    <td><input type="checkbox" name="record[]" value="' . htmlspecialchars($row['image_id']) . '"></td>
                                </tr>';
                                $n++;
                            }
                        } else {
                            echo '<tr><td colspan="3">No images found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addNewModal" class="gallery-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 style="color: #F76100;">Add Image</h2>
            <form id="addImageForm" enctype="multipart/form-data">
                <label for="image">Upload Image:</label><br>
                <input type="file" id="image" name="image" accept="image/*" required><br>
                <button type="submit" class="btn">Submit</button>
            </form>
        </div>
    </div>

    <script>

document.getElementById("addImageForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent default form submission

    var formData = new FormData(this);

    fetch("add_image.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) // Parse JSON response
    .then(data => {
        alert(data.message); // Show alert with the upload status
        if (data.status === "success") {
            location.reload(); // Reload page on success
        }
    })
    .catch(error => {
        alert("An error occurred while uploading the image.");
    });
});

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
            fetch('delete_images.php?deleteid=' + selectedIds.join(','), {
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
    }        var modal = document.getElementById("addNewModal");
        document.getElementById("addNewBtn").onclick = function() { modal.style.display = "block"; }
        document.querySelector(".close").onclick = function() { modal.style.display = "none"; location.reload(); }
        window.onclick = function(event) {
            if (event.target == modal) { modal.style.display = "none"; location.reload(); }
        }
    </script>
</body>
</html>
