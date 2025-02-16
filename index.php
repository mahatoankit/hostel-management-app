<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
      /* Custom Styles */
      body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa; /* Light gray background */
        color: #333;
        margin: 0;
        padding: 0;
      }

      .hero-section {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
        padding: 20px;
      }

      .hero-section h1 {
        font-size: 3rem;
        font-weight: bold;
        margin-bottom: 20px;
        animation: fadeIn 2s ease-in-out;
      }

      .hero-section p {
        font-size: 1.25rem;
        margin-bottom: 40px;
        animation: fadeIn 3s ease-in-out;
      }

      .enter-button {
        font-size: 1.25rem;
        padding: 10px 30px;
        border: none;
        border-radius: 5px;
        background-color: #007bff; /* Blue button */
        color: white;
        transition: background-color 0.3s ease, transform 0.3s ease;
      }

      .enter-button:hover {
        background-color: #0056b3; /* Darker blue on hover */
        transform: scale(1.05);
      }

      .footer {
        text-align: center;
        padding: 20px;
        background-color: rgba(0, 0, 0, 0.05); /* Light footer background */
        color: #333;
        position: fixed;
        bottom: 0;
        width: 100%;
      }

      /* Animations */
      @keyframes fadeIn {
        from {
          opacity: 0;
        }
        to {
          opacity: 1;
        }
      }
    </style>
  </head>
  <body>
    <?php require "partials/_nav.php"; ?>

    <!-- Hero Section -->
    <div class="hero-section">
      <div>
        <h1>Welcome to Hostel Management App</h1>
        <p>Your journey to seamless experiences starts here. Join us today!</p>
        <a href="auth/login.php" class="btn enter-button">Enter</a>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>