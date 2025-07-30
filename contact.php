<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | UrbanServe</title>
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
        
        /* Contact Sections */
        .contact-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .section-title {
            color: #f76d2b;
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            font-size: 1.8rem;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        @media (min-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        /* Contact Info */
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .contact-method {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        
        .contact-icon {
            background-color: #f0f7ff;
            color: #f76d2b;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .contact-details h3 {
            margin: 0 0 5px 0;
            color: #2d3748;
        }
        
        .contact-details p {
            margin: 0;
            color: #4a5568;
        }
        
        .contact-details a {
            color: #f76d2b;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .contact-details a:hover {
            color: #e05b1a;
            text-decoration: underline;
        }
        
        /* Contact Form */
        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #2d3748;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #f76d2b;
            outline: none;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .submit-btn {
            padding: 12px 20px;
            background-color: #f76d2b;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            align-self: flex-start;
        }
        
        .submit-btn:hover {
            background-color: #e05b1a;
        }
        
        /* Map */
        .map-container {
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            background-color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .map-placeholder {
            text-align: center;
            padding: 20px;
            color: #4a5568;
        }
        
        /* Hours */
        .hours-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .hours-table tr {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .hours-table tr:last-child {
            border-bottom: none;
        }
        
        .hours-table th,
        .hours-table td {
            padding: 12px 10px;
            text-align: left;
        }
        
        .hours-table th {
            color: #2d3748;
            font-weight: 600;
            width: 40%;
        }
        
        .hours-table td {
            color: #4a5568;
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
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container">
        <h2>Contact UrbanServe</h2>
        
        <!-- Main Contact Section -->
        <div class="contact-section">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h3 class="section-title"><i class="fas fa-info-circle"></i> Get in Touch</h3>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Our Office</h3>
                            <p>123 Service Plaza, Downtown<br>Mumbai, Maharashtra 400001</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Call Us</h3>
                            <p><a href="tel:+9118001234567">+91 1800 123 4567</a></p>
                            <p>Mon-Fri: 9am-6pm</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Email Us</h3>
                            <p><a href="mailto:contact@urbanserve.com">contact@urbanserve.com</a></p>
                            <p><a href="mailto:support@urbanserve.com">support@urbanserve.com</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Customer Support Hours</h3>
                            <table class="hours-table">
                                <tr>
                                    <th>Monday - Friday</th>
                                    <td>8:00 AM - 8:00 PM</td>
                                </tr>
                                <tr>
                                    <th>Saturday</th>
                                    <td>9:00 AM - 6:00 PM</td>
                                </tr>
                                <tr>
                                    <th>Sunday</th>
                                    <td>10:00 AM - 4:00 PM</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div>
                    <h3 class="section-title"><i class="fas fa-paper-plane"></i> Send Us a Message</h3>
                    <form class="contact-form">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Enter your name">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" class="form-control" placeholder="Enter your email">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number (Optional)</label>
                            <input type="tel" id="phone" class="form-control" placeholder="Enter your phone number">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" class="form-control">
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Customer Support</option>
                                <option value="provider">Service Provider Inquiry</option>
                                <option value="feedback">Feedback/Suggestions</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" class="form-control" placeholder="Type your message here..."></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="contact-section">
            <h3 class="section-title"><i class="fas fa-map-marked-alt"></i> Find Us</h3>
            <div class="map-container">
                <div class="map-placeholder">
                    <i class="fas fa-map-marker-alt" style="font-size: 3rem; color: #f76d2b; margin-bottom: 15px;"></i>
                    <h3>Our Headquarters</h3>
                    <p>Changanacherry, Kottayam</p>
                    <p>Map would be displayed here in a live implementation</p>
                </div>
            </div>
        </div>
        
        <!-- Back Link -->
        <div class="back-link-container">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>

    <script>
        // Simple loading simulation
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loading-overlay').style.opacity = '0';
                document.body.classList.add('loaded');
                setTimeout(function() {
                    document.getElementById('loading-overlay').style.display = 'none';
                }, 500);
            }, 800);
        });
    </script>
</body>
</html>