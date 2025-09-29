<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        :root {
            --primary-color: #1C2B4A;
            --secondary-color: #E2B714;
            --bg-color: #f9fafb;
            --text-color: #374151;
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }

        /* Basic Reset & Typography */
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            line-height: 1.6;
        }
        h1, h2, h3 {
            color: var(--primary-color);
            font-weight: 600;
        }
        h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
        }
        section {
            padding: 80px 20px;
            max-width: 1200px;
            margin: auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .section-header h2 {
            margin-bottom: 10px;
        }
        .section-header p {
            max-width: 600px;
            margin: 0 auto;
            color: #6b7280;
        }

        /* Responsive Sidebar/Nav */
        .sidebar {
            width: 280px;
            height: 100vh;
            background-color: var(--primary-color);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 40px;
            transition: transform 0.3s ease;
        }
        .main-content {
            margin-left: 280px;
        }
        .sidebar-profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--secondary-color);
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-profile-pic i {
            width: 60px;
            height: 60px;
            color: var(--primary-color);
        }
        .about-image-placeholder {
            width: 100%;
            min-height: 250px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
        }
        .about-image-placeholder i {
            width: 50px;
            height: 50px;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin-top: 40px;
            width: 100%;
            text-align: center;
        }
        .sidebar-nav a {
            display: block;
            color: #e5e7eb;
            padding: 15px 20px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        /* Mobile Nav */
        .mobile-nav-toggle {
            display: none;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1000;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 999;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-nav-toggle {
                display: block;
            }
        }

        /* Hero Section */
        #home {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: var(--primary-color);
            color: white;
        }
        #home h1 {
            color: white;
            font-size: 4rem;
            margin-bottom: 10px;
        }
        #home .tagline {
            font-size: 1.5rem;
            color: var(--secondary-color);
            margin-bottom: 30px;
        }
        .cta-button {
            background: var(--secondary-color);
            color: var(--primary-color);
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }
        .cta-button:hover {
            background: white;
        }

        /* Generic Card Style */
        .card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 30px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        /* About Section */
        #about .about-content {
            display: flex;
            flex-direction: column;
            gap: 25px;
            align-items: center;
            text-align: center;
        }
        #about .about-text { max-width: 700px; margin-top: 15px; }
        #about .about-image { }
        #about .about-image img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--secondary-color);
        }

        /* Education & Experience Timeline */
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        .timeline::after {
            content: '';
            position: absolute;
            width: 4px;
            background-color: var(--primary-color);
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -2px;
        }
        .timeline-item {
            padding: 10px 40px;
            position: relative;
            width: 50%;
        }
        .timeline-item:nth-child(odd) { left: 0; }
        .timeline-item:nth-child(even) { left: 50%; }
        .timeline-item::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            right: -10px;
            background-color: var(--secondary-color);
            border: 4px solid var(--primary-color);
            top: 25px;
            border-radius: 50%;
            z-index: 1;
        }
        .timeline-item:nth-child(even)::after { left: -10px; }
        .timeline-content {
            padding: 20px 30px;
            background: var(--card-bg);
            position: relative;
            border-radius: 6px;
            box-shadow: var(--shadow);
        }
        .timeline-content h3 { margin-top: 0; }
        .timeline-content .institution { font-weight: bold; color: var(--primary-color); }
        .timeline-content .year { font-style: italic; color: #6b7280; margin-bottom: 10px; }

        @media (max-width: 768px) {
            .timeline::after { left: 20px; }
            .timeline-item { width: 100%; padding-left: 60px; padding-right: 15px; }
            .timeline-item:nth-child(even) { left: 0%; }
            .timeline-item::after { left: 10px; }
        }

        /* Skills Section */
        #skills .skills-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        .skill-bar { margin-bottom: 15px; }
        .skill-bar .info { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .progress-line {
            height: 10px;
            width: 100%;
            background: #f0f0f0;
            border-radius: 10px;
            position: relative;
        }
        .progress-line span {
            height: 100%;
            background: var(--primary-color);
            position: absolute;
            border-radius: 10px;
        }
        @media (max-width: 768px) {
            #skills .skills-container { grid-template-columns: 1fr; }
        }

        /* Projects Section */
        #projects .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }
        .project-card {
            background: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        }
        .project-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .project-card-content { padding: 25px; }
        .project-card-content h3 { margin-top: 0; }
        .project-tags { margin-top: 15px; }
        .project-tags span {
            background: #eef2ff;
            color: #4f46e5;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            margin-right: 5px;
        }

        /* Testimonials */
        #testimonials .testimonial-slider {
            max-width: 800px;
            margin: auto;
            text-align: center;
        }
        .testimonial-item blockquote {
            font-size: 1.2rem;
            font-style: italic;
            border-left: 4px solid var(--secondary-color);
            padding-left: 20px;
            margin: 0 0 20px 0;
        }
        .testimonial-author { font-weight: bold; }

        /* Downloads & Contact */
        #downloads .download-list, #contact .contact-form {
            max-width: 700px;
            margin: auto;
        }
        .download-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }
        .submit-btn {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        .submit-btn:hover {
            background: var(--secondary-color);
            color: var(--primary-color);
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <?php
        $hero_image = get_setting('hero_image');
        // Check if image exists from the public directory
        if ($hero_image && file_exists($hero_image)): ?>
            <img src="<?php echo e($hero_image); ?>" alt="Profile Picture" class="sidebar-profile-pic">
        <?php else: ?>
            <div class="sidebar-profile-pic">
                <i data-feather="user"></i>
            </div>
        <?php endif; ?>
        <ul class="sidebar-nav">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About Me</a></li>
            <li><a href="#education">Education</a></li>
            <li><a href="#experience">Experience</a></li>
            <li><a href="#skills">Skills</a></li>
            <li><a href="#projects">Projects</a></li>
            <li><a href="#testimonials">Testimonials</a></li>
            <li><a href="#downloads">Downloads</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>

    <!-- Mobile Nav Toggle -->
    <button class="mobile-nav-toggle" id="mobile-nav-toggle">
        <i data-feather="menu"></i>
    </button>

    <div class="main-content">

        <!-- Home Section -->
        <section id="home">
            <div class="hero-content">
                <h1><?php echo get_setting('hero_title'); ?></h1>
                <p class="tagline"><?php echo get_setting('hero_tagline'); ?></p>
                <a href="#about" class="cta-button">Learn More <i data-feather="arrow-down-circle" style="vertical-align: middle;"></i></a>
            </div>
        </section>

        <!-- About Me Section -->
        <section id="about">
            <div class="section-header">
                <h2>About Me</h2>
            </div>
            <div class="card about-content">
                <div class="about-image">
                     <?php if ($hero_image && file_exists($hero_image)): ?>
                        <img src="<?php echo e($hero_image); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <div class="about-image-placeholder">
                            <i data-feather="image"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="about-text">
                    <h3>Bio & Philosophy</h3>
                    <p><?php echo nl2br(get_setting('about_me')); ?></p>
                    <p><?php echo nl2br(get_setting('education_philosophy')); ?></p>
                </div>
            </div>
        </section>

        <!-- Education Section -->
        <section id="education">
            <div class="section-header">
                <h2>Education</h2>
                <p>My academic background and qualifications.</p>
            </div>
            <div class="timeline">
                <?php foreach ($education as $item): ?>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <span class="year"><?php echo e($item['year']); ?></span>
                        <h3><?php echo e($item['degree']); ?></h3>
                        <p class="institution"><?php echo e($item['institution']); ?></p>
                        <p><?php echo nl2br(e($item['description'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Experience Section -->
        <section id="experience">
            <div class="section-header">
                <h2>Experience</h2>
                <p>My professional journey and work experience.</p>
            </div>
            <div class="timeline">
                <?php foreach ($experience as $item): ?>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <span class="year"><?php echo e($item['year_range']); ?></span>
                        <h3><?php echo e($item['position']); ?></h3>
                        <p class="institution"><?php echo e($item['institution']); ?></p>
                        <p><?php echo nl2br(e($item['description'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Skills Section -->
        <section id="skills">
            <div class="section-header">
                <h2>Skills</h2>
                <p>My technical and professional abilities.</p>
            </div>
            <div class="card skills-container">
                <div class="hard-skills">
                    <h3>Hard Skills</h3>
                    <?php foreach ($hard_skills as $skill): ?>
                    <div class="skill-bar">
                        <div class="info">
                            <p><?php echo e($skill['name']); ?></p>
                            <p><?php echo e($skill['level']); ?>%</p>
                        </div>
                        <div class="progress-line"><span style="width: <?php echo e($skill['level']); ?>%"></span></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="soft-skills">
                    <h3>Soft Skills</h3>
                     <?php foreach ($soft_skills as $skill): ?>
                    <div class="skill-bar">
                        <div class="info">
                            <p><?php echo e($skill['name']); ?></p>
                             <p><?php echo e($skill['level']); ?>%</p>
                        </div>
                        <div class="progress-line"><span style="width: <?php echo e($skill['level']); ?>%"></span></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section id="projects">
            <div class="section-header">
                <h2>Projects</h2>
                <p>A selection of my work. Click to learn more.</p>
            </div>
            <div class="project-grid">
                <?php foreach ($projects as $project):
                    $images = json_decode($project['image_album'], true);
                    $thumbnail = !empty($images) ? $images[0] : 'https://via.placeholder.com/400x220.png?text=No+Image';
                ?>
                <div class="project-card">
                    <img src="<?php echo e($thumbnail); ?>" alt="<?php echo e($project['title']); ?>">
                    <div class="project-card-content">
                        <h3><?php echo e($project['title']); ?></h3>
                        <p><?php echo e(substr($project['description'], 0, 100)); ?>...</p>
                        <div class="project-tags">
                            <?php foreach(explode(',', $project['category_tags']) as $tag): ?>
                            <span><?php echo e(trim($tag)); ?></span>
                            <?php endforeach; ?>
                        </div>
                         <?php if($project['external_link']): ?>
                            <a href="<?php echo e($project['external_link']); ?>" target="_blank" class="cta-button" style="margin-top: 20px; display: inline-block; font-size: 0.9rem;">View Project</a>
                         <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials">
            <div class="section-header">
                <h2>Testimonials</h2>
                <p>What others have to say about my work.</p>
            </div>
            <div class="card testimonial-slider">
                <?php foreach ($testimonials as $item): ?>
                <div class="testimonial-item">
                    <blockquote><?php echo e($item['quote']); ?></blockquote>
                    <p class="testimonial-author">- <?php echo e($item['author']); ?>, <?php echo e($item['author_role']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Download Center Section -->
        <section id="downloads">
            <div class="section-header">
                <h2>Download Center</h2>
                <p>Access my resume and other documents here. A password is required.</p>
            </div>
            <div class="card download-list">
                 <?php foreach ($downloads as $item): ?>
                 <div class="download-item">
                    <span><i data-feather="file-text"></i> <?php echo e($item['file_name']); ?></span>
                    <a href="download.php?id=<?php echo $item['id']; ?>" class="cta-button">Download</a>
                 </div>
                 <?php endforeach; ?>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact">
            <div class="section-header">
                <h2>Contact Me</h2>
                <p>Feel free to reach out. I'd love to hear from you!</p>
            </div>
            <div class="card contact-form">
                <form action="contact.php" method="post">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" rows="6" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </section>
    </div>

    <script>
        // Activate feather icons
        feather.replace();

        // Mobile nav toggle
        const toggleBtn = document.getElementById('mobile-nav-toggle');
        const sidebar = document.getElementById('sidebar');
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when a nav link is clicked on mobile
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 992) {
                    sidebar.classList.remove('active');
                }
            });
        });

        // Active link highlighting on scroll
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.sidebar-nav a');
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 60) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>