<?php
ob_start(); // Start output buffering
include 'db.php';
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sanitize inputs
$searchTerm = isset($_GET['search']) ? trim($conn->real_escape_string($_GET['search'])) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build query with COALESCE for image fallbacks
$query = "SELECT s.*, c.name AS category_name, 
          COALESCE(
            (SELECT image_url FROM service_images WHERE service_id = s.id LIMIT 1),
            s.image,
            'default.jpg'
          ) AS service_image
          FROM services s 
          JOIN service_categories c ON s.category_id = c.id";

$conditions = [];
if (!empty($searchTerm)) {
    $conditions[] = "(s.name LIKE '%$searchTerm%' OR s.description LIKE '%$searchTerm%')";
}
if ($categoryFilter > 0) {
    $conditions[] = "c.id = $categoryFilter";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

// Add pagination limit
$query .= " LIMIT 24"; // Show 24 items per page

// Execute query
$result = $conn->query($query);
if (!$result) {
    die("Database error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Services | UrbanServe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* Base Styles */
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        body.loaded {
            opacity: 1;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h2 {
            color: #2d3748;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f76d2b;
        }
        
        /* Search Container */
        .search-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            border-color: #f76d2b;
            outline: none;
        }
        
        .category-select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background: white;
            min-width: 200px;
        }
        
        .search-button {
            padding: 12px 20px;
            background-color: #f76d2b;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .search-button:hover {
            background-color: #e05b1a;
        }
        
        .reset-button {
            padding: 12px 20px;
            background-color: #e2e8f0;
            color: #4a5568;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .reset-button:hover {
            background-color: #cbd5e0;
        }
        
        /* Services Grid */
        .services-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .service-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .service-image-container {
            height: 200px;
            position: relative;
            overflow: hidden;
            background: #f5f5f5;
        }
        
        .service-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease, opacity 0.5s ease;
            opacity: 0;
        }
        
        .service-image.loaded {
            opacity: 1;
        }
        
        .service-image-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .service-info {
            padding: 20px;
        }
        
        .service-name {
            color: #f76d2b;
            font-size: 1.2rem;
            margin: 0 0 8px;
            font-weight: 600;
        }
        
        .service-category {
            display: inline-block;
            background: #e2e8f0;
            color: #4a5568;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 12px;
        }
        
        .service-price {
            font-weight: bold;
            color: #2d3748;
            margin: 10px 0;
            font-size: 1.1rem;
        }
        
        .service-description {
            color: #4a5568;
            font-size: 0.95rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .action-links {
            display: flex;
            gap: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        
        .action-links a {
            flex: 1;
            text-align: center;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .action-links a:first-child {
            background-color: #f0f4f8;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }
        
        .action-links a:first-child:hover {
            background-color: #e2e8f0;
        }
        
        .action-links a:last-child {
            background-color: #f76d2b;
            color: white;
            border: 1px solid #f76d2b;
        }
        
        .action-links a:last-child:hover {
            background-color: #e05b1a;
            border-color: #e05b1a;
        }
        
        /* No Results */
        .no-results {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px;
            color: #718096;
        }
        
        /* Back Link */
        .back-link-container {
            text-align: center;
            margin-top: 40px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: #f76d2b;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .back-link:hover {
            background-color: #e05b1a;
        }
        
        /* Loading Overlay */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            transition: opacity 0.5s ease;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #f76d2b;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .services-list {
                grid-template-columns: 1fr;
            }
            
            .action-links {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container">
        <h2>Browse Services</h2>
        
        <!-- Search Form -->
        <div class="search-container">
            <form method="GET" class="search-form" id="searchForm">
                <input type="text" name="search" class="search-input" 
                       placeholder="Search services..." value="<?= htmlspecialchars($searchTerm) ?>">
                
                <select name="category" class="category-select">
                    <option value="">All Categories</option>
                    <?php
                    $categories = $conn->query("SELECT * FROM service_categories");
                    while ($cat = $categories->fetch_assoc()):
                    ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryFilter == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <button type="submit" class="search-button">Search</button>
                <a href="services.php" class="reset-button">Reset</a>
            </form>
        </div>
        
        <!-- Services List -->
        <div class="services-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="service-card">
                        <div class="service-image-container">
                            <div class="service-image-placeholder"></div>
                            <img src="uploads/services/<?= htmlspecialchars($row['service_image']) ?>" 
                                 alt="<?= htmlspecialchars($row['name']) ?>" 
                                 class="service-image"
                                 loading="lazy"
                                 onload="this.classList.add('loaded')"
                                 onerror="this.src='images/services/default.jpg';this.onerror=null;">
                        </div>
                        
                        <div class="service-info">
                            <h3 class="service-name"><?= htmlspecialchars($row['name']) ?></h3>
                            <span class="service-category"><?= htmlspecialchars($row['category_name']) ?></span>
                            <div class="service-price">₹<?= number_format($row['base_price'], 2) ?></div>
                            <p class="service-description"><?= htmlspecialchars($row['description']) ?></p>
                            
                            <div class="action-links">
                                <a href="service_detail.php?id=<?= $row['id'] ?>">Details</a>
                                <a href="book_service.php?service_id=<?= $row['id'] ?>">Book Now</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No services found matching your search criteria.</p>
                    <p><a href="services.php" class="btn btn-primary">Show all services</a></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Back Link -->
        <div class="back-link-container">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>

    <script>
    // Page Load Handler
    document.addEventListener('DOMContentLoaded', function() {
        // Hide loading overlay when page is ready
        setTimeout(function() {
            document.getElementById('loading-overlay').style.opacity = '0';
            document.body.classList.add('loaded');
            
            // Remove overlay after fade out
            setTimeout(function() {
                document.getElementById('loading-overlay').style.display = 'none';
            }, 500);
        }, 300); // Small delay to prevent flicker if page loads very fast
        
        // Form submission handler
        document.getElementById('searchForm').addEventListener('submit', function() {
            document.getElementById('loading-overlay').style.display = 'flex';
            document.getElementById('loading-overlay').style.opacity = '1';
        });
        
        // Image loading handler
        document.querySelectorAll('.service-image').forEach(img => {
            if (img.complete && img.naturalWidth !== 0) {
                img.classList.add('loaded');
                if (img.previousElementSibling) {
                    img.previousElementSibling.style.display = 'none';
                }
            }
        });
    });
    
    // Fallback in case DOMContentLoaded doesn't fire
    setTimeout(function() {
        document.getElementById('loading-overlay').style.display = 'none';
        document.body.classList.add('loaded');
    }, 3000); // Max 3 seconds loading time
    </script>
</body>
</html>
<?php ob_end_flush(); ?>