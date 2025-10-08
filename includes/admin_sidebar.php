<!-- Update your modal HTML -->
<div id="logoutModal">
    <div class="modal-content">
        <p>Are you sure you want to logout?</p>
        <div class="button-group">
            <button onclick="hideLogoutConfirmation()">Cancel</button>
            <button onclick="performLogout()">Yes, Logout</button>
        </div>
    </div>
</div>
<!-- <div id="userloginmodal">
    <div class="modal-content">
        <p>Are you sure you want to logout?</p>
        <div class="button-group">
            <?php 
                session_start();
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                require_once("includes/db.php");

                $sql = "select id, fname FROM users where";
                $result = $conn->query($sql);

            ?>
        </div>
    </div>
</div> -->

<!-- sidebar -->
<div class="admin-sidebar">
    <div class="logo">
        <img src="assets/imeldalogo.jpg" alt="">
    </div> 
    <ul class="admin-nav-link">
        <li>
            <a href="admin_dashboard.php">
                <i class="fa-solid fa-gauge"></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="admin_notif.php">
                <i class="fa fa-bell"></i>
                <span class="nav-item">Alerts</span>
            </a>
        </li>

        <li>
            <a href="admin_log.php">
                <i class="fa-solid fa-book-open-reader"></i>
                <span class="nav-item">Logs</span>
            </a>
        </li>

          <li>
            <a href="admin_record.php">
                <i class="fa fa-file"></i>
                <span class="nav-item">Records</span>
            </a>
        </li>

        <li>
            <a href="admin_history.php">
                <i class="fa-solid fa-chart-simple"></i>
                <span class="nav-item">History</span>
            </a>
        </li>

        <!-- <li>
            <a href="admin_residents.php">
                <i class="fa-solid fa-user-plus"></i>
                <span class="nav-item">Residents</span>
            </a>
        </li> -->

        <li>
            <a href="#" onclick="showLogoutConfirmation()">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="nav-item">Logout</span>
            </a>
        </li>

        
    </ul>

    <div class="sidebar-footer">
        <div class="small" style="">Logged in as:
            <br>
            <?php
            if ($_SESSION["uname"] == true){
                  echo $_SESSION["lname"];
             }
        ?>
        </div>
        
    </div>
</div>

<style> 

    /* Logout Modal Styles */
#logoutModal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    z-index: 1000;
    animation: fadeIn 0.3s ease-out;
}

#logoutModal .modal-content {
    background: white;
    width: 350px;
    margin: 150px auto;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    animation: slideDown 0.4s ease-out;
    border: 1px solid #e0e0e0;
}

#logoutModal p {
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 25px;
    text-align: center;
    padding: 10px 0;
}

#logoutModal .button-group {
    display: flex;
    justify-content: center;
    gap: 15px;
}

#logoutModal button {
    padding: 10px 25px;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

#logoutModal button:first-child {
    background-color: #f1f1f1;
    color: #555;
}

#logoutModal button:first-child:hover {
    background-color: #e0e0e0;
    transform: translateY(-2px);
}

#logoutModal button:last-child {
    background: linear-gradient(135deg, #243c74, #1a68d1);
    color: white;
}

#logoutModal button:last-child:hover {
    background: linear-gradient(135deg, #243c74, #1a68d1);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 107, 107, 0.3);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from { 
        transform: translateY(-50px);
        opacity: 0;
    }
    to { 
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsive adjustments */
@media (max-width: 480px) {
    #logoutModal .modal-content {
        width: 85%;
        margin: 100px auto;
    }
}

</style>



<script>
function showLogoutConfirmation() {
    document.getElementById('logoutModal').style.display = 'block';
}

function hideLogoutConfirmation() {
    document.getElementById('logoutModal').style.display = 'none';
}

function performLogout() {
    
    window.location.href = 'logout.php';
}
</script>