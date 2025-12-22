<style>
/* Premium Contact Page Styles */
.contact-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 4rem 0;
    color: white;
    text-align: center;
}

.contact-hero h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    animation: fadeInDown 0.6s ease-out;
}

.contact-hero p {
    font-size: 1.2rem;
    opacity: 0.95;
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

.contact-container {
    max-width: 1200px;
    margin: -3rem auto 4rem;
    padding: 0 1.5rem;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.contact-card {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    animation: fadeInUp 0.6s ease-out;
}

.contact-card h2 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

div.contact-card label,
div.contact-card .form-label {
    display: block;
    font-weight: 800;
    color: #000000;
    margin-bottom: 0.6rem;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.form-label .required {
    color: #ef4444;
    margin-left: 0.25rem;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 150px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.priority-selector {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.priority-option {
    position: relative;
}

.priority-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.priority-label {
    display: block;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.85rem;
    font-weight: 600;
    color: #1e293b;
    background-color: #ffffff;
}

.priority-option input:checked + .priority-label {
    border-color: #667eea;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.priority-label:hover {
    border-color: #cbd5e1;
}

.submit-btn {
    width: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 1.25rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    margin-top: 1rem;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.report-cta-card {
    background: linear-gradient(135deg, #1e293b, #334155);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    text-align: center;
    margin-top: 2rem;
}

.report-cta-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.report-cta-card p {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-bottom: 1.5rem;
}

.btn-report-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #667eea;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-report-cta:hover {
    background: #5a67d8;
    transform: translateY(-2px);
}

.info-card {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.info-content h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
}

.info-content p {
    color: #64748b;
    margin: 0;
    font-size: 0.95rem;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-top: 2rem;
}

.feature-item {
    text-align: center;
    padding: 1.5rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
}

.feature-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    border-radius: 16px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.75rem;
}

.feature-item h4 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.feature-item p {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}

.success-message,
.error-message {
    padding: 1.25rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: none;
    animation: slideDown 0.3s ease-out;
}

.success-message {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    border: 2px solid #10b981;
}

.error-message {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
    border: 2px solid #ef4444;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 968px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-hero h1 {
        font-size: 2rem;
    }
    
    .priority-selector {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<!-- Hero Section -->
<div class="contact-hero">
    <h1><i class="fas fa-envelope-open-text"></i> Get In Touch</h1>
    <p>We're here to help! Send us a message and we'll respond as soon as possible.</p>
</div>

<!-- Main Contact Section -->
<div class="contact-container">
    <div class="contact-grid">
        <!-- Contact Form -->
        <div class="contact-card">
            <h2><i class="fas fa-paper-plane"></i> Send Message</h2>
            
            <div id="successMessage" class="success-message">
                <i class="fas fa-check-circle"></i> <strong>Success!</strong> Your message has been sent. We'll get back to you soon!
            </div>
            
            <div id="errorMessage" class="error-message">
                <i class="fas fa-exclamation-circle"></i> <strong>Error!</strong> <span id="errorText"></span>
            </div>
            
            <form id="contactForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Your Name <span class="required">*</span>
                        </label>
                        <input type="text" name="name" class="form-input" placeholder="John Doe" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            Email Address <span class="required">*</span>
                        </label>
                        <input type="email" name="email" class="form-input" placeholder="john@example.com" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" placeholder="+1 (555) 123-4567">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            Category <span class="required">*</span>
                        </label>
                        <select name="category" class="form-select" required>
                            <option value="general">💬 General Inquiry</option>
                            <option value="support">🛠️ Technical Support</option>
                            <option value="billing">💳 Billing Question</option>
                            <option value="technical">⚙️ Technical Issue</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        Subject <span class="required">*</span>
                    </label>
                    <input type="text" name="subject" class="form-input" placeholder="How can we help you?" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Priority Level</label>
                    <div class="priority-selector">
                        <div class="priority-option">
                            <input type="radio" name="priority" value="low" id="priority-low">
                            <label for="priority-low" class="priority-label">
                                🔹 Low
                            </label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" name="priority" value="medium" id="priority-medium" checked>
                            <label for="priority-medium" class="priority-label">
                                🔸 Medium
                            </label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" name="priority" value="high" id="priority-high">
                            <label for="priority-high" class="priority-label">
                                🔶 High
                            </label>
                        </div>
                        <div class="priority-option">
                            <input type="radio" name="priority" value="urgent" id="priority-urgent">
                            <label for="priority-urgent" class="priority-label">
                                🔴 Urgent
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        Message <span class="required">*</span>
                    </label>
                    <textarea name="message" class="form-textarea" placeholder="Tell us more about your inquiry..." required></textarea>
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
        
        <!-- Contact Info -->
        <div>
            <div class="contact-card">
                <h2><i class="fas fa-info-circle"></i> Contact Information</h2>
                
                <div class="info-card">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Email Us</h3>
                            <p>admin@newsbishwo.com</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h3>Response Time</h3>
                            <p>We typically respond within 24 hours</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="info-content">
                            <h3>Support Hours</h3>
                            <p>Monday - Friday, 9:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4>Fast Response</h4>
                    <p>Quick replies to your inquiries</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Secure</h4>
                    <p>Your data is safe with us</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Expert Team</h4>
                    <p>Professional support staff</p>
                </div>
            </div>

            <div class="report-cta-card">
                <h3><i class="fas fa-bug"></i> Found a Bug?</h3>
                <p>If you noticed an incorrect calculation or a technical issue, please use our dedicated reporting tool for a faster fix.</p>
                <a href="<?= app_base_url('/report') ?>" class="btn-report-cta">
                    <i class="fas fa-flag"></i> Report Issue
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    
    // Hide messages
    successMsg.style.display = 'none';
    errorMsg.style.display = 'none';
    
    // Show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    try {
        const formData = new FormData(this);
        
        const response = await fetch('<?php echo app_base_url('/contact/submit'); ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success
            successMsg.style.display = 'block';
            this.reset();
            
            // Scroll to success message
            successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Reset button after 2 seconds
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
            }, 2000);
        } else {
            // Show error
            errorText.textContent = data.message || 'Something went wrong. Please try again.';
            errorMsg.style.display = 'block';
            errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
        }
    } catch (error) {
        console.error('Error:', error);
        errorText.textContent = 'Network error. Please check your connection and try again.';
        errorMsg.style.display = 'block';
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
    }
});
</script>
