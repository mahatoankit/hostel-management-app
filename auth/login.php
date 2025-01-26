<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
  <?php 
    require "../partials/_nav.php";
    require "../config/db.php";
    session_start();
    $error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : "";
    unset($_SESSION['error_message']);
  ?>
<!-- ================================================== -->
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header text-center">
              <h3>Login</h3>
          
            </div>
            <div class="card-body">
                
              <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo htmlspecialchars($error_message); ?>
                </div>
              <?php endif; ?>

              <div class="d-flex justify-content-center mb-3">
                
                <button id="hosteller-login-btn" class="btn btn-outline-primary me-2 active">Login as Hosteller</button>
                <button id="admin-login-btn" class="btn btn-outline-primary">Login as Admin</button>
              </div>
              <form id="login-form" action="../controllers/LoginController.php" method="POST">
                
                <input type="hidden" name="user_type" id="user_type" value="hosteller">
                
                <div class="mb-3">
                  <label for="email" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
                </div>
                
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="d-grid">
                  <button type="submit" class="btn btn-primary btn-block" id="login-button">Login as Hosteller</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
<!-- ================================================== -->
    <script>
      document.getElementById('hosteller-login-btn').addEventListener('click', function() {
        document.getElementById('user_type').value = 'hosteller';
        document.getElementById('login-button').textContent = 'Login as Hosteller';
        this.classList.add('active');
        document.getElementById('admin-login-btn').classList.remove('active');
      });

      document.getElementById('admin-login-btn').addEventListener('click', function() {
        document.getElementById('user_type').value = 'admin';
        document.getElementById('login-button').textContent = 'Login as Admin';
        this.classList.add('active');
        document.getElementById('hosteller-login-btn').classList.remove('active');
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>