<?php
session_start();
include 'db_connect.php';

$page_title = 'Contact Us - AgriAI';

// Fix Services Menu Visibility & Page Specific Styles
$extra_css = "
<style>
    /* Force Solid Header on this page */
    .glass-header-nav, #main-header {
        background: #ffffff !important;
        backdrop-filter: none !important;
        border-bottom: 1px solid #e5e7eb !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        z-index: 10000 !important;
        position: sticky !important;
    }

    .contact-hero {
        background: linear-gradient(rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.9)), url('https://images.unsplash.com/photo-1596700877983-9336d859d95f?q=80&w=1770&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        background-attachment: fixed; /* Parallax Effect */
        padding: 4rem 0;
        text-align: center;
        color: white;
    }

    .contact-card {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #f3f4f6;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.15);
    }

    .contact-icon {
        width: 60px;
        height: 60px;
        background: #dcfce7;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #16a34a;
        font-size: 1.5rem;
    }

    /* Fix scrolling on mobile */
    @media (max-width: 768px) {
        .contact-hero {
            background-attachment: scroll !important;
        }
    }

    .form-control {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem; /* Space for icon */
        border: 2px solid #f3f4f6;
        border-radius: 0.75rem;
        background: #f9fafb;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #16a34a;
        background: white;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.1);
    }

    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        transition: color 0.3s;
    }

    .form-control:focus + .input-icon {
        color: #16a34a;
    }
    
    .contact-form-card {
        background: white;
        padding: 3rem; 
        border-radius: 1.5rem; 
        border: 1px solid #f0fdf4;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05);
    }

    .submit-btn {
        width: 100%;
        padding: 1rem;
        border: none;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(22, 163, 74, 0.4);
    }
</style>
";

include 'includes/main_header.php';
?>

<!-- Hero Section -->
<header class="contact-hero">
    <div class="container">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem;">Get in Touch</h1>
        <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
            Have questions or need support? We're here to help you grow.
        </p>
    </div>
</header>


<!-- Contact Info Cards -->
<section class="section" style="padding: 5rem 0; background: #f9fafb;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: -8rem; position: relative; z-index: 10;">
            <!-- Phone -->
            <div class="contact-card">
                <div class="contact-icon"><i class="fas fa-phone"></i></div>
                <h3 style="margin-bottom: 0.5rem;">Call Us</h3>
                <p style="color: #6b7280; margin-bottom: 1rem;">Mon-Fri from 9am to 6pm</p>
                <a href="tel:+15551234567" style="color: #16a34a; font-weight: 600; font-size: 1.1rem; text-decoration: none;">+1 (555) 123-4567</a>
            </div>

            <!-- Email -->
            <div class="contact-card">
                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                <h3 style="margin-bottom: 0.5rem;">Email Support</h3>
                <p style="color: #6b7280; margin-bottom: 1rem;">We usually reply within 24 hours</p>
                <a href="mailto:support@agriai.com" style="color: #16a34a; font-weight: 600; font-size: 1.1rem; text-decoration: none;">support@agriai.com</a>
            </div>

            <!-- Office -->
            <div class="contact-card">
                <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3 style="margin-bottom: 0.5rem;">Visit Us</h3>
                <p style="color: #6b7280; margin-bottom: 1rem;">Main Headquarters</p>
                <a href="#" style="color: #16a34a; font-weight: 600; font-size: 1.1rem; text-decoration: none;">123 Agri Tech Park, CA</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section class="section" style="padding: 5rem 0;">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1rem;">Send us a message</h2>
                <p style="color: #6b7280;">Fill out the form below and our team will get back to you.</p>
            </div>

            <form class="contact-form-card">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="input-group">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; font-size: 0.9rem;">First Name</label>
                        <div style="position: relative;">
                            <input type="text" class="form-control" placeholder="John">
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>
                    <div class="input-group">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; font-size: 0.9rem;">Last Name</label>
                        <div style="position: relative;">
                            <input type="text" class="form-control" placeholder="Doe">
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; font-size: 0.9rem;">Email Address</label>
                    <div style="position: relative;">
                        <input type="email" class="form-control" placeholder="john@company.com">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151; font-size: 0.9rem;">Message</label>
                    <div style="position: relative;">
                        <textarea class="form-control" rows="5" placeholder="How can we help you?" style="padding-top: 1rem;"></textarea>
                        <i class="fas fa-comment-alt input-icon" style="top: 1.5rem; transform: none;"></i>
                    </div>
                </div>

                <button type="button" class="submit-btn">
                    <span>Send Message</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Map Section -->
<section style="height: 400px; width: 100%; background: #e5e7eb;">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100939.98555098464!2d-122.5076401794695!3d37.75781499660272!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80859a6d00690021%3A0x4a501367f076adff!2sSan%20Francisco%2C%20CA!5e0!3m2!1sen!2sus!4v1709923456789!5m2!1sen!2sus" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>

<?php include 'includes/main_footer.php'; ?>
