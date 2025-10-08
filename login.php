
<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: admin_dashboard.php"); // Redirect to the home page
    exit();
}

// Prevent caching of the login page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("includes/db.php");
//include("notlogintoken.php");


$msg = "";
$uname = "";




if(isset($_POST['login'])){
    // Get form data
    $uname = $_POST['uname'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
    if(empty($uname) || empty($password)) {
        $msg = "Username and password are required!";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, fname, lname, uname, pw, user_type FROM users WHERE uname = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if(password_verify($password, $user['pw'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fname'] = $user['fname'];
                $_SESSION['lname'] = $user['lname'];
                $_SESSION['uname'] = $user['uname'];
                $_SESSION['user_type'] = $user['user_type'] ?? 'user'; // Default to 'user' if column doesn't exist
                $date = date('Y-m-d H:i:s'); //set date
                
                //Redirect based on user type
                 if(($_SESSION['user_type'] ?? 'user') === 'admin') {
                     header("Location: admin_dashboard.php");
                    
                    $stmt = $conn->prepare("INSERT INTO papertrail (userid, firstname, lastname, dateadded, module, operation) VALUES ('$_SESSION[user_id]','$_SESSION[fname]','$_SESSION[lname]','$date','Login','logged In')");
                    $stmt->execute();
                 } else {
                     header("Location: user_dashboard.php");
                     
                 }
                
                exit();
            } else {
                $msg = "Invalid username or password!";
            }
        } else {
            $msg = "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Login - Flood Monitoring System</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      background-image: url('../assets/image.png');
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/image.png');
      background-size: cover;
      background-position: center;
    }
    .login-container {
      background: rgba(255, 255, 255, 0.9);
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
    }
    .logo {
      text-align: center;
      margin-bottom: 1.5rem;
    }
    .logo img {
      max-width: 150px;
    }
    .form-group {
      margin-bottom: 1.2rem;
      margin-right: 1.5rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #333;
    }
    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }
    .btn {
      width: 100%;
      padding: 0.75rem;
      background-color: #2c7be5;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .btn:hover {
      background-color: #1a68d1;
    }
    .alert {
      padding: 0.75rem;
      margin-bottom: 1rem;
      border-radius: 4px;
      text-align: center;
    }
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
    }
    .forgot-password {
      text-align: center;
      margin-top: 1rem;
    }
    .forgot-password a {
      color: #2c7be5;
      text-decoration: none;
    }
    .forgot-password a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="logo">
      <!-- Replace with your actual logo -->
      <img src="assets/imeldalogo.jpg" alt="Flood Monitoring System">
      <h2>Flood Early Warning System</h2>
    </div>
    
    <?php if($msg): ?>
      <div class="alert alert-error">
        <?php echo htmlspecialchars($msg); ?>
      </div>
    <?php endif; ?>
    
    <form method="post" action="">
      <div class="form-group">
        <label for="uname">Username</label>
        <input type="text" id="uname" name="uname" class="form-control" 
               value="<?php echo htmlspecialchars($uname); ?>" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>
      
      <button type="submit" name="login" class="btn">Login</button>
      
    </form>
  </div>
  <script>
    
          /*
      * The following is intentional, to force Firefox to run 
      * this JS snippet after a history invoked back/forward navigation.
      */
      window.onunload = function(){};    

      function formatTime(t) {
          return t.getHours() + ':' + t.getMinutes() + ':' + t.getSeconds();
      }

      if (window.history.state != null && window.history.state.hasOwnProperty('historic')) {
          if (window.history.state.historic == true) {
              document.body.style.display = 'none';
              console.log('I was here before at ' + formatTime(window.history.state.last_visit));
              window.history.replaceState({historic: false}, '');
              window.location.reload();
          } else {
              console.log('I was forced to reload, but WELCOME BACK!');
              window.history.replaceState({
                  historic  : true,
                  last_visit: new Date()
              }, '');
          }
      } else {
          console.log('This is my first visit to ' + window.location.pathname);
          window.history.replaceState({
              historic  : true,
              last_visit: new Date()
          }, '');
      }
  
  </script>
</body>
</html>