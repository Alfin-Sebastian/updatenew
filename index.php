<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>UrbanServe | Book Trusted Local Services</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
  /* Urban Company Inspired Styles */
:root {
  --primary: #f76d2b; /* Urban Company's orange */
  --primary-dark: #e05b1a;
  --secondary: #2d3748;
  --accent: #f0f4f8;
  --text: #2d3748;
  --light-text: #718096;
  --border: #e2e8f0;
  --white: #ffffff;
  --black: #000000;
  --success: #38a169;
}

/* Base Styles */
body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  color: var(--text);
  line-height: 1.6;
  margin: 0;
  padding: 0;
  background-color: #f8f9fa;
}

.container {
  width: 90%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

/* Header */
.header {
  background-color: var(--white);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  position: sticky;
  top: 0;
  z-index: 100;
  padding: 15px 0;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  font-size: 24px;
  font-weight: 700;
  text-decoration: none;
  color: var(--black);
}

.logo span {
  color: var(--primary);
}

.nav-links {
  display: flex;
  gap: 25px;
}

.nav-links a {
  text-decoration: none;
  color: var(--text);
  font-weight: 500;
  font-size: 15px;
  transition: color 0.2s;
}

.nav-links a:hover {
  color: var(--primary);
}

.auth-buttons {
  display: flex;
  align-items: center;
  gap: 15px;
}

.auth-buttons span {
  font-weight: 500;
  color: var(--text);
}

.btn {
  display: inline-block;
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 14px;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-outline {
  border: 1px solid var(--primary);
  color: var(--primary);
  background-color: transparent;
}

.btn-outline:hover {
  background-color: rgba(247, 109, 43, 0.1);
}

.btn-primary {
  background-color: var(--primary);
  color: var(--white);
  border: 1px solid var(--primary);
}

.btn-primary:hover {
  background-color: var(--primary-dark);
  border-color: var(--primary-dark);
}

.btn-accent {
  background-color: var(--white);
  color: var(--primary);
  border: 1px solid var(--white);
}

.btn-accent:hover {
  background-color: rgba(255, 255, 255, 0.9);
}

/* Hero Section */
.hero {
  background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
              url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
  background-size: cover;
  background-position: center;
  color: var(--white);
  padding: 100px 0;
  text-align: center;
}

.hero h1 {
  font-size: 48px;
  font-weight: 700;
  margin-bottom: 20px;
  line-height: 1.2;
}

.hero p {
  font-size: 20px;
  max-width: 700px;
  margin: 0 auto 40px;
  opacity: 0.9;
}

.search-box {
  max-width: 700px;
  margin: 0 auto;
}

.search-box input {
  width: 100%;
  padding: 18px 20px;
  border: none;
  border-radius: 6px 0 0 6px;
  font-size: 16px;
  outline: none;
}

.search-box button {
  padding: 18px 25px;
  background-color: var(--primary);
  color: white;
  border: none;
  border-radius: 0 6px 6px 0;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.search-box button:hover {
  background-color: var(--primary-dark);
}

/* Sections Common Styles */
.section-title {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 15px;
  text-align: center;
}

.section-subtitle {
  color: var(--light-text);
  text-align: center;
  max-width: 700px;
  margin: 0 auto 50px;
  font-size: 16px;
}

/* Categories/Services Section */
.categories {
  padding: 80px 0;
  background-color: var(--white);
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.category-card {
  background-color: var(--white);
  border-radius: 8px;
  padding: 30px 20px;
  text-align: center;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.category-icon {
  width: 60px;
  height: 60px;
  margin: 0 auto 20px;
  background-color: rgba(247, 109, 43, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  font-size: 24px;
}

.category-card h3 {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
}

/* Featured Providers */
.featured-providers {
  padding: 80px 0;
  background-color: var(--accent);
}

.providers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.provider-card {
  background-color: var(--white);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
}

.provider-image {
  height: 180px;
  background-size: cover;
  background-position: center;
}

.provider-info {
  padding: 20px;
}

.provider-info h3 {
  margin: 0 0 10px;
  font-size: 18px;
}

.provider-services {
  display: flex;
  align-items: center;
  gap: 5px;
  color: var(--light-text);
  font-size: 14px;
  margin-bottom: 15px;
}

.provider-services i {
  color: var(--success);
}

.provider-price {
  font-weight: 600;
  margin-bottom: 15px;
}

.provider-actions {
  display: flex;
  gap: 10px;
}

/* Testimonials */
.testimonials {
  padding: 80px 0;
  background-color: var(--white);
}

.testimonial-slider {
  max-width: 800px;
  margin: 0 auto;
}

.testimonial {
  background-color: var(--accent);
  padding: 30px;
  border-radius: 8px;
  text-align: center;
}

.testimonial-text {
  font-size: 18px;
  font-style: italic;
  margin-bottom: 20px;
  color: var(--text);
}

.testimonial-author {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
}

.testimonial-author img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

/* CTA Section */
.cta {
  padding: 80px 0;
  background-color: var(--primary);
  color: var(--white);
  text-align: center;
}

.cta h2 {
  font-size: 36px;
  margin-bottom: 15px;
}

.cta p {
  font-size: 18px;
  max-width: 700px;
  margin: 0 auto 30px;
  opacity: 0.9;
}

/* Footer */
.footer {
  background-color: var(--secondary);
  color: var(--white);
  padding: 60px 0 20px;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 40px;
  margin-bottom: 40px;
}

.footer-column h3 {
  font-size: 18px;
  margin-bottom: 20px;
  position: relative;
  padding-bottom: 10px;
}

.footer-column h3::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 40px;
  height: 2px;
  background-color: var(--primary);
}

.footer-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-column ul li {
  margin-bottom: 12px;
}

.footer-column ul li a {
  color: #cbd5e0;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.2s;
}

.footer-column ul li a:hover {
  color: var(--white);
}

.social-links {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}

.social-links a {
  color: var(--white);
  background-color: rgba(255, 255, 255, 0.1);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.social-links a:hover {
  background-color: var(--primary);
}

.copyright {
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  color: #a0aec0;
  font-size: 14px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .navbar {
    flex-direction: column;
    gap: 15px;
  }
  
  .nav-links {
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .hero h1 {
    font-size: 36px;
  }
  
  .hero p {
    font-size: 18px;
  }
  
  .section-title {
    font-size: 28px;
  }
  
  .category-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
}

@media (max-width: 480px) {
  .search-box form {
    flex-direction: column;
  }
  
  .search-box input,
  .search-box button {
    border-radius: 6px;
    width: 100%;
  }
  
  .search-box button {
    margin-top: 10px;
  }
  
  .provider-actions {
    flex-direction: column;
  }
}
    /* CSS styles preserved (use original CSS from inde.txt or existing index.php) */
  </style>
</head>
<body>

<!-- Header -->
<header class="header">
  <div class="container">
    <nav class="navbar">
      <a href="index.php" class="logo">Urban<span>Serve</span></a>
      <div class="nav-links">
        <a href="services.php">Services</a>
        <a href="providers.php">Providers</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
        
        <?php if (isset($_SESSION['user'])): ?>
          <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="admin_dashboard.php">Dashboard</a>
          <?php elseif ($_SESSION['user']['role'] === 'provider'): ?>
            <a href="provider_dashboard.php">Dashboard</a>
          <?php elseif ($_SESSION['user']['role'] === 'customer'): ?>
            <a href="customer_dashboard.php">Profile</a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="auth-buttons">
        <?php if (isset($_SESSION['user'])): ?>
          <span>Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
          <a href="logout.php" class="btn btn-outline">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline">Log In</a>
          <a href="register.php" class="btn btn-primary">Sign Up</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1>Book Trusted Home Services Near You</h1>
    <p>Discover and book the best professionals for all your home service needs. Quality guaranteed.</p>
    <div class="search-box">
      <div style="display: flex; flex-direction: column; gap: 15px; width: 100%;">
  <div style="display: flex; align-items: center; gap: 10px;">
    <span style="white-space: nowrap;">Browse:</span>
    <div style="display: flex; gap: 10px; width: 100%;">
      <a href="services.php" 
         style="flex: 1; display: flex; align-items: center; justify-content: center;
                gap: 8px; padding: 10px 15px; background: #f76d2b; color: white;
                border-radius: 6px; text-decoration: none; font-weight: 500;">
        <i class="fas fa-concierge-bell"></i> Services
      </a>
      
      <a href="providers.php" 
         style="flex: 1; display: flex; align-items: center; justify-content: center;
                gap: 8px; padding: 10px 15px; background: #f76d2b; color: white;
                border-radius: 6px; text-decoration: none; font-weight: 500;">
        <i class="fas fa-user-tie"></i> Providers
      </a>
    </div>
  </div>
</div>
    </div>
  </div>
</section>

<!-- Popular Services -->

<section class="categories">
  <div class="container">
    <h2 class="section-title">Popular Services</h2>
    <p class="section-subtitle">Browse our most requested services from trusted professionals in your area</p>

    <div class="providers-grid">
      <?php
      // Modified query to include service images
      $services = $conn->query("
        SELECT 
          s.*, 
          c.name AS category_name, 
          ps.provider_id,
          (SELECT image_url FROM service_images WHERE service_id = s.id LIMIT 1) AS primary_image
        FROM services s 
        LEFT JOIN service_categories c ON s.category_id = c.id
        LEFT JOIN provider_services ps ON s.id = ps.service_id
        GROUP BY s.id
        LIMIT 8
      ");
      
      if ($services && $services->num_rows > 0):
        while ($service = $services->fetch_assoc()):
          // Determine which image to use (priority: service_images > services.image > fallback)
          $service_image = $service['primary_image'] ?? 
                          ($service['image'] ?: 'https://source.unsplash.com/random/500x300?service');
      ?>
        <div class="provider-card">
          <div class="provider-image" style="background-image: url('<?= htmlspecialchars($service_image) ?>');"></div>
          <div class="provider-info">
            <h3><?= htmlspecialchars($service['name']) ?></h3>
            <div class="provider-services">
              <i class="fas fa-tags"></i> <?= htmlspecialchars($service['category_name'] ?? 'Uncategorized') ?>
            </div>
            <div class="provider-price">From ₹<?= number_format($service['base_price'], 2) ?></div>
            <div class="provider-actions">
              <a href="service_detail.php?id=<?= $service['id'] ?>" class="btn btn-outline">View Details</a>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <p>No services available yet.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Featured Providers -->
<!-- Featured Providers -->
<section class="featured-providers">
  <div class="container">
    <h2 class="section-title">Featured Service Providers</h2>
    <p class="section-subtitle">Top-rated professionals trusted by thousands of customers</p>

    <div class="providers-grid">
      <?php
      $query = "
        SELECT 
            p.id AS provider_id, 
            u.id AS user_id,
            u.name AS provider_name, 
            u.profile_image,
            s.name AS service_name, 
            s.id AS service_id,
            p.avg_rating
        FROM providers p
        JOIN users u ON p.user_id = u.id
        JOIN provider_services ps ON p.user_id = ps.provider_id
        JOIN services s ON ps.service_id = s.id
        GROUP BY u.id
        LIMIT 6
      ";

      $providers = $conn->query($query);
      if ($providers && $providers->num_rows > 0):
        while ($row = $providers->fetch_assoc()):
          // Determine profile image (use profile_image if exists, otherwise fallback)
          $profile_image = $row['profile_image'] ?: 
                         'https://ui-avatars.com/api/?name=' . urlencode($row['provider_name']) . '&background=f76d2b&color=fff';
      ?>
        <div class="provider-card">
          <div class="provider-image" style="background-image: url('<?= htmlspecialchars($profile_image) ?>');"></div>
          <div class="provider-info">
            <h3><?= htmlspecialchars($row['provider_name']) ?></h3>
            <div class="provider-services">
              <i class="fas fa-check-circle"></i> <?= htmlspecialchars($row['service_name']) ?>
              <?php if ($row['avg_rating'] > 0): ?>
                <span style="margin-left: 10px; color: var(--primary);">
                  ★ <?= number_format($row['avg_rating'], 1) ?>
                </span>
              <?php endif; ?>
            </div>
           
            <div class="provider-actions">
              <a href="provider_profile.php?id=<?= $row['user_id'] ?>" class="btn btn-outline">View Profile</a>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <p>No providers found.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    
    <div class="copyright">
      &copy; <?= date('Y') ?> UrbanServe. All rights reserved.
    </div>
  </div>
</footer>

<!-- Scripts -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = 1;
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.category-card').forEach((card, index) => {
      card.style.opacity = 0;
      card.style.transform = 'translateY(20px)';
      card.style.transition = `all 0.3s ease ${index * 0.1}s`;
      observer.observe(card);
    });

    console.log('UrbanServe frontend loaded');
  });
</script>
</body>
</html>
