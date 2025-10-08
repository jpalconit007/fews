
<div class="sidebar">
 <div class="logo">
            <img src="assets/imeldalogo.jpg" alt="">
            <!--<span class="nav-item" style="color: white;">Doña Imelda</span>-->
</div> 
    <ul div class="nav-link">
        <li><a href="user_dashboard.php">
        <i class="fa-solid fa-gauge" style="font-size: 1.5em;"></i>
<!-- Adjust the em value (0.5em-1em works well for icons) -->
            <span class="nav-item">Dashboard</span>
        </a></li>

        <li><a href="user_history.php">
        <i class="fa-solid fa-chart-simple" style="font-size: 1.5em;"></i>
            <span class="nav-item">History</span>
        </a></li>


                <li>
        <a href="#" onclick="showLogoutConfirmation()">
            <i class="fa-solid fa-right-from-bracket" style="font-size: 1.5em;"></i>
            <span class="nav-item">Logout</span>
        </a>
    </li>
    </ul>
</div>
<!-- Add this to your CSS or style section -->
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
    background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
    color: white;
}

#logoutModal button:last-child:hover {
    background: linear-gradient(135deg, #ff5252, #ff7676);
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

<script>
function showLogoutConfirmation() {
    document.getElementById('logoutModal').style.display = 'block';
}

function hideLogoutConfirmation() {
    document.getElementById('logoutModal').style.display = 'none';
}

function performLogout() {
    window.location.href = 'login.php';
}
</script>

<script>
async function handleLogout() {
    if (confirm('Are you sure you want to logout?')) {
        try {
            const response = await fetch('logout.php', {
                method: 'POST'
            });
            
            if (response.ok) {
                window.location.reload(); // Or redirect to login page
            } else {
                alert('Logout failed');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Logout failed');
        }
    }
}
</script>


<!-- <nav>
    <ul>
        <li>

<li><a href="#">
<i class="fa-solid fa-gauge"></i>
    <span class="nav-item">Dashboard</span>
</a></li>

<li><a href="#">
<i class="fa-solid fa-chart-simple"></i>
    <span class="nav-item">History</span>
</a></li>
</ul>
</nav> -->