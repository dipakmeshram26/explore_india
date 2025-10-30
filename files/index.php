<?php
session_start();

// Check login status
$isLoggedIn = isset($_SESSION['user_name']);
$userType = $isLoggedIn ? $_SESSION['user_type'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ExplorIndia | Discover Local Gems</title>
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: #f9f9f9;
      color: #333;
    }

    /* Navbar */
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #0078ff;
      color: #fff;
      padding: 15px 50px;
    }
    header h1 {
      font-size: 24px;
      margin: 0;
    }
    nav a {
      color: white;
      text-decoration: none;
      margin: 0 15px;
      font-weight: 500;
    }
    nav a:hover {
      text-decoration: underline;
    }

    /* Hero Section */
    .hero {
      text-align: center;
      padding: 100px 20px;
      background: linear-gradient(to right, #0078ff, #00c6ff);
      color: white;
    }
    .hero h2 {
      font-size: 40px;
      margin-bottom: 20px;
    }
    .search-bar {
      background: white;
      padding: 10px;
      border-radius: 50px;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 60%;
      margin: 0 auto;
    }
    .search-bar input {
      border: none;
      outline: none;
      width: 80%;
      padding: 10px 15px;
      border-radius: 50px;
      font-size: 16px;
    }
    .search-bar button {
      background: #0078ff;
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 50px;
      cursor: pointer;
      font-size: 16px;
    }
    .search-bar button:hover {
      background: #005fcc;
    }

    /* Trending Section */
    .trending {
      padding: 60px 50px;
    }
    .trending h3 {
      text-align: center;
      font-size: 28px;
      margin-bottom: 30px;
    }
    .places {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
    }
    .place-card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: 0.3s;
    }
    .place-card:hover {
      transform: translateY(-5px);
    }
    .place-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .place-card .info {
      padding: 15px;
    }

    footer {
      background: #0078ff;
      color: white;
      text-align: center;
      padding: 15px 0;
      margin-top: 40px;
    }
  </style>
</head>
<body>
  <header>
    <h1>ExplorIndia</h1>
    <nav>
      <a href="index.php">Home</a>

      <?php if ($isLoggedIn): ?>

        <!-- Business user options -->
        <?php if ($userType === "business"): ?>
          <a href="business_dashboard.php">Dashboard</a>
          <a href="add_listing.php">Add Business Listing</a>
        <?php else: ?>
          <a href="add_listing.php">Add Listing</a>
        <?php endif; ?>

        <span>👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php">Logout</a>

      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
      <?php endif; ?>
    </nav>
  </header>

  <section class="hero">
    <h2>Discover & Share Local Experiences</h2>
    <div class="search-bar">
      <input type="text" placeholder="Search for places... e.g. best coffee near Manewada">
      <button>Search</button>
    </div>
  </section>

  <section class="trending">
    <h3>🔥 Trending Places Near You</h3>
    <div class="places">
      <div class="place-card">
        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836" alt="">
        <div class="info">
          <h4>Chai Adda Café</h4>
          <p>⭐ 4.5 | Manewada, Nagpur</p>
        </div>
      </div>

      <div class="place-card">
        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38" alt="">
        <div class="info">
          <h4>TasteHub Restaurant</h4>
          <p>⭐ 4.2 | Sadar, Nagpur</p>
        </div>
      </div>

      <div class="place-card">
        <img src="https://images.unsplash.com/photo-1528698827591-e19ccd7bc23d" alt="">
        <div class="info">
          <h4>Central Garden</h4>
          <p>⭐ 4.7 | Dharampeth</p>
        </div>
      </div>
    </div>
  </section>

  <footer>
    © 2025 ExplorIndia | Made with ❤️ for Local Discovery
  </footer>
</body>
</html>
