<?php
session_start();
if ($_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

include 'db.php';

$customer_id = $_SESSION['user']['id'];

// Handle cancellation via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $reason = trim($_POST['reason'] ?? 'Customer requested cancellation');
    
    // Update booking status
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled', cancellation_reason = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $reason, $booking_id, $customer_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}

// Fetch customer data
$customer_sql = "SELECT name, email FROM users WHERE id = ?";
$customer_stmt = $conn->prepare($customer_sql);
$customer_stmt->bind_param("i", $customer_id);
$customer_stmt->execute();
$customer_result = $customer_stmt->get_result();
$customer = $customer_result->fetch_assoc();

// Handle filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query with prepared statements
$query = "
    SELECT 
        b.*, 
        s.name AS service_name, 
        s.image AS service_image,
        u.name AS provider_name,
        b.cancellation_reason
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON b.provider_id = u.id
    WHERE b.user_id = ?
";

$where = [];
$params = [$customer_id];
$types = "i";

if ($status_filter !== 'all') {
    $where[] = "b.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($date_filter)) {
    $where[] = "DATE(b.booking_date) = ?";
    $params[] = $date_filter;
    $types .= "s";
}

if (!empty($where)) {
    $query .= " AND " . implode(" AND ", $where);
}

$query .= " ORDER BY b.booking_date DESC";

// Execute query with prepared statement
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result();

// Get counts for dashboard
$counts = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(status = 'pending') as pending,
        SUM(status = 'confirmed') as confirmed,
        SUM(status = 'completed') as completed,
        SUM(status = 'cancelled') as cancelled
    FROM bookings
    WHERE user_id = $customer_id
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | UrbanServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #f76d2b;
            --primary-dark: #e05b1a;
            --secondary: #2d3748;
            --accent: #f0f4f8;
            --text: #2d3748;
            --light-text: #718096;
            --border: #e2e8f0;
            --white: #ffffff;
            --success: #38a169;
            --warning: #dd6b20;
            --error: #e53e3e;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: var(--text);
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .card-img-top {
            height: 180px;
            object-fit: cover;
            border-radius: 6px 6px 0 0;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: rgba(237, 137, 54, 0.1);
            color: var(--warning);
        }

        .status-confirmed {
            background-color: rgba(56, 161, 105, 0.1);
            color: var(--success);
        }

        .status-completed {
            background-color: rgba(66, 153, 225, 0.1);
            color: #4299e1;
        }

        .status-cancelled {
            background-color: rgba(229, 62, 62, 0.1);
            color: var(--error);
        }

        .action-link {
            color: var(--primary);
            text-decoration: none;
            transition: all 0.2s;
        }

        .action-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .action-link.cancel {
            color: var(--error);
        }

        .action-link.cancel:hover {
            color: #c53030;
        }

        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }

        .modal-footer {
            border-top: none;
        }

        .stat-card {
            background: var(--white);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .stat-card h3 {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 8px;
        }

        .stat-card p {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">My Bookings</h1>
         
            <a href="customer_dashboard.php" class="btn btn-outline-primary">
                 ← Back to Profile
            </a>
            
        </div>

        <!-- Filter Controls -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $status_filter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($date_filter) ?>">
                    </div>
                    
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        <a href="customer_bookings.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Booking Stats -->
        <div class="row mb-4">
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <p><?= $counts['total'] ?></p>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <h3>Pending</h3>
                    <p><?= $counts['pending'] ?></p>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <h3>Confirmed</h3>
                    <p><?= $counts['confirmed'] ?></p>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <h3>Completed</h3>
                    <p><?= $counts['completed'] ?></p>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <h3>Cancelled</h3>
                    <p><?= $counts['cancelled'] ?></p>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-body">
                <?php if ($bookings->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Provider</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($booking = $bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($booking['service_image'] ?? 'https://via.placeholder.com/80?text=Service') ?>" 
                                                     alt="<?= htmlspecialchars($booking['service_name']) ?>" 
                                                     width="60" height="60" 
                                                     class="rounded me-3">
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($booking['service_name']) ?></h6>
                                                    <small class="text-muted"><?= date('M d, Y', strtotime($booking['booking_date'])) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($booking['provider_name']) ?></td>
                                        <td><?= date('h:i A', strtotime($booking['booking_date'])) ?></td>
                                        <td>₹<?= number_format($booking['amount'], 2) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= strtolower($booking['status']) ?>">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="view_booking.php?id=<?= $booking['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                                    <button class="btn btn-sm btn-outline-danger cancel-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#cancelModal"
                                                            data-booking-id="<?= $booking['id'] ?>">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($booking['status'] === 'completed'): ?>
                                                    <a href="rate_service.php?booking_id=<?= $booking['id'] ?>" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-star"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5>No bookings found</h5>
                        <p class="text-muted">You haven't made any bookings yet</p>
                        <a href="services.php" class="btn btn-primary">
                            <i class="fas fa-concierge-bell me-2"></i>Book a Service
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cancel Booking Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="cancelForm">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="modalBookingId">
                        <p>Are you sure you want to cancel this booking?</p>
                        <div class="mb-3">
                            <label for="cancelReason" class="form-label">Reason (optional)</label>
                            <textarea class="form-control" id="cancelReason" name="reason" rows="3" placeholder="Please specify reason for cancellation"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Set booking ID when cancel button is clicked
            $('.cancel-btn').click(function() {
                var bookingId = $(this).data('booking-id');
                $('#modalBookingId').val(bookingId);
            });

            // Handle form submission
            $('#cancelForm').submit(function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize() + '&cancel_booking=1';
                
                $.ajax({
                    type: 'POST',
                    url: window.location.href,
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Close modal and reload page to reflect changes
                            $('#cancelModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + (response.error || 'Failed to cancel booking'));
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>