<?php
session_start();

// Check login status
$isLoggedIn = isset($_SESSION['user_name']);
$userType  = $isLoggedIn ? $_SESSION['user_type'] : null;
?>

<?php if (isset($_GET['listing']) && $_GET['listing'] === 'success'): ?>
    <div id="successMsg" style="background:#d4edda;color:#155724;padding:12px;text-align:center;">
        ✅ Your business listing has been submitted successfully!
    </div>

    <script>
        setTimeout(function () {
            var msg = document.getElementById("successMsg");
            if (msg) {
                msg.style.display = "none";
            }
        }, 5000);
    </script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ExplorIndia | Discover Local Gems</title>
  <link rel="stylesheet" href="styles.css">
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
          <a href="profile-page.php">Profile</a>
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
        <?php
        include 'db.php';

        $sql = "
            SELECT id, title, city, avg_rating, cover_image 
            FROM listings 
            WHERE status='approved' AND is_active=1 
            ORDER BY created_at DESC 
            LIMIT 6
        ";

        $result = $conn->query($sql);

        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                $image = !empty($row['cover_image'])
                    ? "img/listings/covers/" . $row['cover_image']
                    : "img/default.jpg";
        ?>
            <div class="place-card">
                <img src="<?= $image ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                <div class="info">
                    <h4><?= htmlspecialchars($row['title']) ?></h4>
                    <p>
                        ⭐ <?= $row['avg_rating'] ?? 'New' ?> |
                        <?= htmlspecialchars($row['city']) ?>
                    </p>
                </div>
            </div>
        <?php
            endwhile;
        else:
        ?>
            <p style="text-align:center;width:100%;">
                No listings available right now.
            </p>
        <?php endif; ?>
    </div>
</section>


  <footer>
    © 2025 ExplorIndia | Made with ❤️ for Local Discovery
  </footer>
</body>
</html>
